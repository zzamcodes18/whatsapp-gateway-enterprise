<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Message;
use App\Models\MessageTemplate;
use App\Services\WaEngineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageTemplateController extends Controller
{
    public function __construct(protected WaEngineService $engineService) {}

    /**
     * Display a listing of user's message templates.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $templates = $user->messageTemplates()
            ->when($request->query('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $userDevices = $user->devices()->where('status', 'connected')->get();

        return view('templates.index', [
            'templates' => $templates,
            'userDevices' => $userDevices,
        ]);
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:otp,promo,notification,button,general'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'footer' => ['nullable', 'string', 'max:255'],
            'buttons' => ['nullable', 'array'],
            'buttons.*.type' => ['required_with:buttons', 'string', 'in:reply,url,copy,call,select'],
            'buttons.*.text' => ['required_with:buttons', 'string'],
            'buttons.*.url' => ['nullable', 'string'],
            'buttons.*.code' => ['nullable', 'string'],
            'buttons.*.phone' => ['nullable', 'string'],
            'buttons.*.id' => ['nullable', 'string'],
        ]);

        $buttonsPayload = null;
        if (! empty($validated['buttons'])) {
            $buttonsPayload = array_values(array_filter($validated['buttons'], fn ($b) => ! empty($b['text'])));
        }

        $template = $user->messageTemplates()->create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
            'footer' => $validated['footer'] ?? null,
            'buttons' => $buttonsPayload,
            'is_active' => true,
        ]);

        $user->logActivity('template.create', "Membuat template pesan baru: {$template->name} (ID: #{$template->id})");

        return redirect()->route('templates.index')
            ->with('success', "Template pesan '{$template->name}' (ID: #{$template->id}) berhasil dibuat!");
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, MessageTemplate $template): RedirectResponse
    {
        if ($template->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:otp,promo,notification,button,general'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'footer' => ['nullable', 'string', 'max:255'],
            'buttons' => ['nullable', 'array'],
        ]);

        $buttonsPayload = null;
        if (! empty($validated['buttons'])) {
            $buttonsPayload = array_values(array_filter($validated['buttons'], fn ($b) => ! empty($b['text'])));
        }

        $template->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
            'footer' => $validated['footer'] ?? null,
            'buttons' => $buttonsPayload,
        ]);

        $request->user()->logActivity('template.update', "Perbarui template pesan ID: #{$template->id}");

        return redirect()->route('templates.index')
            ->with('success', "Template pesan '{$template->name}' berhasil diperbarui!");
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(Request $request, MessageTemplate $template): RedirectResponse
    {
        if ($template->user_id !== $request->user()->id) {
            abort(403);
        }

        $name = $template->name;
        $template->delete();

        $request->user()->logActivity('template.delete', "Menghapus template pesan '{$name}'");

        return redirect()->route('templates.index')
            ->with('success', "Template pesan '{$name}' telah dihapus.");
    }

    /**
     * Test send template to WhatsApp number.
     */
    public function testSend(Request $request, MessageTemplate $template): RedirectResponse
    {
        if ($template->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'phone' => ['required', 'string', 'min:8'],
            'sample_variables' => ['nullable', 'string'], // JSON or comma separated e.g. otp=123456,name=Budi
        ]);

        $device = Device::where('id', $validated['device_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (! $device->isConnected()) {
            return back()->withErrors(['phone' => 'Perangkat yang dipilih sedang tidak terhubung ke WhatsApp.']);
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        // Parse test variables
        $variables = [];
        if (! empty($validated['sample_variables'])) {
            $decoded = json_decode($validated['sample_variables'], true);
            if (is_array($decoded)) {
                $variables = $decoded;
            } else {
                // Parse key=val lines
                $lines = explode("\n", str_replace(',', "\n", $validated['sample_variables']));
                foreach ($lines as $line) {
                    if (str_contains($line, '=')) {
                        [$k, $v] = explode('=', $line, 2);
                        $variables[trim($k)] = trim($v);
                    }
                }
            }
        }

        // Render placeholders
        $body = MessageTemplate::renderPlaceholders($template->content, $variables);
        $title = MessageTemplate::renderPlaceholders($template->title, $variables);
        $footer = MessageTemplate::renderPlaceholders($template->footer, $variables);

        $buttons = [];
        if (! empty($template->buttons)) {
            foreach ($template->buttons as $btn) {
                $item = $btn;
                $item['text'] = MessageTemplate::renderPlaceholders($btn['text'] ?? '', $variables);
                if (! empty($btn['url'])) {
                    $item['url'] = MessageTemplate::renderPlaceholders($btn['url'], $variables);
                }
                if (! empty($btn['code'])) {
                    $item['code'] = MessageTemplate::renderPlaceholders($btn['code'], $variables);
                }
                $buttons[] = $item;
            }
        }

        if (! empty($buttons)) {
            $result = $this->engineService->sendInteractiveMessage(
                $device->session_id,
                $cleanPhone,
                [
                    'title' => $title ?? '',
                    'body' => $body,
                    'footer' => $footer ?? '',
                    'buttons' => $buttons,
                ]
            );
        } else {
            $result = $this->engineService->sendTextMessage(
                $device->session_id,
                $cleanPhone,
                $body
            );
        }

        if (! empty($result['success']) && $result['success'] === true) {
            Message::create([
                'user_id' => $request->user()->id,
                'device_id' => $device->id,
                'remote_jid' => $cleanPhone.'@s.whatsapp.net',
                'message_type' => ! empty($buttons) ? 'interactive' : 'text',
                'message_content' => $body,
                'direction' => 'outbound',
                'status' => 'sent',
                'wa_message_id' => $result['messageId'] ?? null,
            ]);

            return back()->with('success', "Test pesan template ID: #{$template->id} berhasil dikirim ke +{$cleanPhone}!");
        }

        return back()->withErrors(['phone' => 'Gagal mengirim test pesan template: '.($result['message'] ?? 'Unknown error')]);
    }
}
