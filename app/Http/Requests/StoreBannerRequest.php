<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi berbasis permission Spatie — satu-satunya sumber kebenaran.
        // Saat P1 menambah peran staf_aset, cukup beri 'master.manage' di seeder;
        // kedelapan FormRequest ini tidak perlu disentuh lagi.
        return $this->user()?->can('master.konten.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'image_path' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }
}
