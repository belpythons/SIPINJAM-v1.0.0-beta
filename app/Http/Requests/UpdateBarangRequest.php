<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi berbasis permission Spatie — satu-satunya sumber kebenaran.
        // Saat P1 menambah peran staf_aset, cukup beri 'master.manage' di seeder;
        // kedelapan FormRequest ini tidak perlu disentuh lagi.
        return $this->user()?->can('master.aset.manage') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('barang') ?? $this->id;

        return [
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:barangs,kode,'.$id,
            'stok_total' => 'required|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,tidak_tersedia',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }
}
