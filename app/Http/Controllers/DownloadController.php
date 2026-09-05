<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class DownloadController extends Controller
{
    /**
     * Download module WHMCS sebagai ZIP (generated on-the-fly).
     *
     * Struktur ZIP: wagateway/ (sesuai struktur modules/addons/wagateway)
     */
    public function whmcsModule(): StreamedResponse
    {
        $source = base_path('whmcs-module');

        abort_unless(is_dir($source), 404, 'Module WHMCS tidak ditemukan di server.');

        $zipName = 'wagateway-whmcs-module.zip';

        return response()->streamDownload(function () use ($source) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'wagw_');

            $zip = new ZipArchive();
            if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                abort(500, 'Gagal membuat arsip ZIP.');
            }

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (! $file->isFile()) {
                    continue;
                }
                $filePath = $file->getRealPath();
                // Root ZIP = folder "wagateway" agar user tinggal ekstrak ke modules/addons/
                $relative = 'wagateway/' . substr($filePath, strlen($source) + 1);
                $zip->addFile($filePath, str_replace('\\', '/', $relative));
            }

            $zip->close();

            echo file_get_contents($tmpFile);
            unlink($tmpFile);
        }, $zipName, [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $zipName . '"',
        ]);
    }
}
