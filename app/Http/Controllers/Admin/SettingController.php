<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.pengaturan.edit', [
            'qrisImagePath' => Setting::get('qris_image_path'),
            'previewPages' => Setting::get('digital_preview_pages', 10),
        ]);
    }

    public function update(UpdateSettingRequest $request)
    {
        if ($request->hasFile('qris_image')) {
            $previousPath = Setting::get('qris_image_path');
            $path = $request->file('qris_image')->storePublicly('qris', 'public');

            if ($path === false) {
                throw new RuntimeException('Gambar QRIS gagal disimpan ke penyimpanan publik.');
            }

            Setting::set('qris_image_path', $path);

            if ($previousPath && $previousPath !== $path) {
                Storage::disk('public')->delete($previousPath);
            }
        }
        if ($request->filled('digital_preview_pages')) {
            Setting::set('digital_preview_pages', (int) $request->validated('digital_preview_pages'));
        }

        return back()->with('success', 'Pengaturan QRIS berhasil disimpan.');
    }
}
