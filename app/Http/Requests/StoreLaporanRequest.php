<?php

namespace App\Http\Requests;

use App\Models\Laporan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'peminjaman_id' => ['required', 'exists:peminjamans,id'],
            'jenis' => ['required', Rule::in(Laporan::JENIS)],
            'deskripsi' => ['required', 'string', 'max:2000'],
            'bukti' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
