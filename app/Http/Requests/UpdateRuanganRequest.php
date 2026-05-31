<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        $id = $this->route('ruangan') ?? $this->id;
        return [
            'nama'       => 'required|string|max:255',
            'kode'       => 'required|string|max:50|unique:ruangans,kode,' . $id,
            'kapasitas'  => 'required|integer|min:1',
            'lokasi'     => 'nullable|string|max:255',
            'deskripsi'  => 'nullable|string',
            'status'     => 'required|in:tersedia,tidak_tersedia',
            'image_path' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
        ];
    }
}
