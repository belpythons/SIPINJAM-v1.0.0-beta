<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'nama'          => 'required|string|max:255',
            'kode'          => 'required|string|max:50|unique:barangs,kode',
            'stok_total'    => 'required|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'kategori'      => 'nullable|string|max:100',
            'deskripsi'     => 'nullable|string',
            'status'        => 'required|in:tersedia,tidak_tersedia',
            'image_path'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }
}
