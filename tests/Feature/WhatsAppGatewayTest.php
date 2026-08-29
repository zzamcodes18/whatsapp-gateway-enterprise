<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Device;
use App\Models\User;
use Tests\TestCase;

class WhatsAppGatewayTest extends TestCase
{
    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Whatsapp Gateway Enterprise');
        $response->assertSee('WhatsApp Gateway');
        $response->assertSee('Pairing Code');
    }

    public function test_public_pages_render_successfully(): void
    {
        $this->get('/terms')->assertStatus(200)->assertSee('Syarat dan Ketentuan');
        $this->get('/privacy')->assertStatus(200)->assertSee('Kebijakan Privasi');
        $this->get('/faq')->assertStatus(200)->assertSee('Pertanyaan yang Sering Diajukan');
        $this->get('/support')->assertStatus(200)->assertSee('Pusat Bantuan');
    }

    public function test_login_and_register_pages_render(): void
    {
        $loginRes = $this->get('/login');
        $loginRes->assertStatus(200);
        $loginRes->assertSee('Sign in');

        $regRes = $this->get('/register');
        $regRes->assertStatus(200);
        $regRes->assertSee('Create Your Account');
    }

    public function test_user_can_login_and_view_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'test_user_'.time().'@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $dashRes = $this->actingAs($user)->get('/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Dashboard Overview');
    }

    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $regularUser = User::factory()->create(['role' => 'user']);

        $this->actingAs($regularUser)->get('/admin')->assertStatus(403);
        $this->actingAs($regularUser)->get('/admin/users')->assertStatus(403);
        $this->actingAs($regularUser)->get('/admin/devices')->assertStatus(403);
        $this->actingAs($regularUser)->get('/admin/bot-server')->assertStatus(403);
    }

    public function test_admin_user_can_access_admin_panel(): void
    {
        $adminUser = User::factory()->create(['role' => 'admin']);

        $this->actingAs($adminUser)->get('/admin')->assertStatus(200)->assertSee('Admin Control Center');
        $this->actingAs($adminUser)->get('/admin/users')->assertStatus(200)->assertSee('Manajemen Pengguna');
        $this->actingAs($adminUser)->get('/admin/devices')->assertStatus(200)->assertSee('Seluruh Perangkat Terdaftar');
        $this->actingAs($adminUser)->get('/admin/bot-server')->assertStatus(200)->assertSee('Konfigurasi Server Bot OTP');
    }

    public function test_device_crud_and_status_views(): void
    {
        $user = User::factory()->create(['role' => 'user', 'device_limit' => 5]);

        $device = Device::create([
            'user_id' => $user->id,
            'session_id' => 'dev_test_'.uniqid(),
            'name' => 'Test Unit Jakarta',
            'connection_type' => 'pairing_code',
            'status' => 'pairing_ready',
            'pairing_code' => 'TEST-1234',
        ]);

        $res = $this->actingAs($user)->get('/devices');
        $res->assertStatus(200);
        $res->assertSee('Test Unit Jakarta');

        $showRes = $this->actingAs($user)->get("/devices/{$device->id}");
        $showRes->assertStatus(200);
        $showRes->assertSee('Test Unit Jakarta');
        $showRes->assertSee('TEST-1234');

        $statusRes = $this->actingAs($user)->getJson("/devices/{$device->id}/status");
        $statusRes->assertStatus(200);
        $statusRes->assertJsonFragment(['name' => 'Test Unit Jakarta']);
    }

    public function test_daily_limit_reset_command(): void
    {
        $user = User::factory()->create([
            'messages_sent_today' => 45,
            'last_limit_reset_at' => now()->subDay()->toDateString(),
        ]);

        $this->artisan('gateway:reset-daily-limits')->assertSuccessful();

        $this->assertEquals(0, $user->fresh()->messages_sent_today);
        $this->assertEquals(now()->toDateString(), $user->fresh()->last_limit_reset_at->toDateString());
    }

    public function test_api_key_authentication_and_v1_rest_endpoints(): void
    {
        $user = User::factory()->create(['daily_message_limit' => 100]);
        $generated = ApiKey::generate($user, 'Test API Key');
        $plainKey = $generated['plain_text_token'];

        // Without key -> 401
        $noKeyRes = $this->getJson('/api/v1/devices');
        $noKeyRes->assertStatus(401);

        // With valid key -> 200
        $withKeyRes = $this->withHeaders([
            'X-API-Key' => $plainKey,
        ])->getJson('/api/v1/devices');

        $withKeyRes->assertStatus(200);
        $withKeyRes->assertJsonStructure(['success', 'data']);
    }

    public function test_device_store_generates_uuidv4_and_supports_update(): void
    {
        $user = User::factory()->create(['role' => 'user', 'device_limit' => 5]);

        $storeRes = $this->actingAs($user)->postJson('/devices', [
            'name' => 'Device UUID Test',
            'connection_type' => 'qr',
        ]);

        $storeRes->assertStatus(200);
        $deviceData = $storeRes->json('device');
        $this->assertNotEmpty($deviceData['session_id']);
        
        // Assert session_id is a valid UUIDv4 regex pattern
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $deviceData['session_id']
        );

        $deviceId = $deviceData['id'];

        // Test updating device
        $updateRes = $this->actingAs($user)->putJson("/devices/{$deviceId}", [
            'name' => 'Device UUID Renamed',
            'phone_number' => '62899887766',
        ]);

        $updateRes->assertStatus(200);
        $this->assertEquals('Device UUID Renamed', Device::find($deviceId)->name);
    }

    public function test_admin_can_store_and_update_device(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create(['role' => 'user']);

        $storeRes = $this->actingAs($admin)->postJson('/admin/devices', [
            'user_id' => $targetUser->id,
            'name' => 'Admin Created Unit',
            'connection_type' => 'qr',
        ]);

        $storeRes->assertStatus(200);
        $device = Device::where('name', 'Admin Created Unit')->first();
        $this->assertNotNull($device);
        $this->assertEquals($targetUser->id, $device->user_id);

        $updateRes = $this->actingAs($admin)->putJson("/admin/devices/{$device->id}", [
            'user_id' => $targetUser->id,
            'name' => 'Admin Updated Unit',
            'phone_number' => '628111222333',
            'status' => 'connected',
        ]);

        $updateRes->assertStatus(200);
        $this->assertEquals('Admin Updated Unit', $device->fresh()->name);
        $this->assertEquals('connected', $device->fresh()->status);
    }
}
