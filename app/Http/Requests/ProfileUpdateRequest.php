<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\NomorWhatsapp;
use App\Support\PhoneNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => ['nullable', 'image', 'max:5120'], // Max 5MB
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],

            // ── Identitas sivitas (P2) ──
            'identity_number' => ['nullable', 'string', 'max:32'],
            'user_type' => ['nullable', Rule::in(User::TIPE)],
            'program_studi' => ['nullable', 'string', 'max:120'],
            'unit_kerja' => ['nullable', 'string', 'max:120'],
            'jabatan' => ['nullable', 'string', 'max:120'],
            'angkatan' => ['nullable', 'string', 'max:10'],

            // Nomor diterima dalam format 08xx / 62xx / +62xx dan dinormalisasi
            // ke E.164 oleh mutator User::phone().
            'phone' => [
                'nullable',
                'string',
                new NomorWhatsapp,
                Rule::unique(User::class, 'phone')
                    ->ignore($this->user()->id),
            ],
        ];
    }

    /**
     * Nomor dinormalisasi SEBELUM divalidasi, supaya aturan unique
     * membandingkan bentuk yang sama dengan yang tersimpan di basis data.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('phone')) {
            $this->merge(['phone' => PhoneNumber::normalize($this->input('phone')) ?? $this->input('phone')]);
        }
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'Nomor WhatsApp ini sudah dipakai akun lain.',
            'user_type.in' => 'Tipe sivitas harus salah satu dari: mahasiswa, dosen, atau staff.',
        ];
    }
}
