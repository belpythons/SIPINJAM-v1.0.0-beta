<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\ImageService;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BannerController extends Controller
{
    public function index(): Response
    {
        $banners = Banner::orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/KelolaBanner', [
            'banners' => $banners,
        ]);
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        try {
            $data = [];

            if ($request->hasFile('image_path')) {
                $data['image_path'] = ImageService::cropAndSave($request->file('image_path'), 'banner');
            }

            Banner::create($data);

            return redirect()->back()->with('success', 'Banner berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan banner: ' . $e->getMessage());
        }
    }

    public function update(UpdateBannerRequest $request, int $id): RedirectResponse
    {
        try {
            $banner = Banner::findOrFail($id);
            $data = [];

            if ($request->hasFile('image_path')) {
                ImageService::deleteOldImage($banner->image_path);
                $data['image_path'] = ImageService::cropAndSave($request->file('image_path'), 'banner');
                $banner->update($data);
            }

            return redirect()->back()->with('success', 'Banner berhasil diperbarui!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui banner: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $banner = Banner::findOrFail($id);
            ImageService::deleteOldImage($banner->image_path);
            $banner->delete();

            return redirect()->back()->with('success', 'Banner berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus banner: ' . $e->getMessage());
        }
    }
}
