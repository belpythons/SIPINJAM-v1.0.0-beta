<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCalendarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Otorisasi berbasis permission Spatie — satu-satunya sumber kebenaran.
        // Saat P1 menambah peran staf_aset, cukup beri 'master.manage' di seeder;
        // kedelapan FormRequest ini tidak perlu disentuh lagi.
        return $this->user()?->can('master.konten.manage') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'image_path' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
            'year' => 'required|integer|min:2020|max:2100',
        ];
    }
}
