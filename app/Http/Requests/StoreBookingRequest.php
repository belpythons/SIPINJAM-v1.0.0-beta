<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->user()?->is_blocked) {
            abort(403, 'Akun Anda diblokir: ' . $this->user()->blocked_reason);
        }
        return true;
    }

    public function rules(): array
    {
        return [
            'tipe_peminjaman' => ['required', 'in:barang,ruangan'],
            'barang_id'       => ['required_if:tipe_peminjaman,barang', 'nullable', 'exists:barangs,id'],
            'ruangan_id'      => ['required_if:tipe_peminjaman,ruangan', 'nullable', 'exists:ruangans,id'],
            'jumlah'          => ['nullable', 'integer', 'min:1'],
            'tanggal_mulai'   => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'waktu_mulai'     => ['required', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/'],
            'waktu_selesai'   => [
                'required',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/',
                function ($attribute, $value, $fail) {
                    $tanggalMulai = $this->input('tanggal_mulai');
                    $tanggalSelesai = $this->input('tanggal_selesai');
                    $waktuMulai = $this->input('waktu_mulai');

                    if ($tanggalMulai && $tanggalSelesai && $waktuMulai && $value) {
                        try {
                            $dateMulai = \Illuminate\Support\Carbon::parse($tanggalMulai)->format('Y-m-d');
                            $dateSelesai = \Illuminate\Support\Carbon::parse($tanggalSelesai)->format('Y-m-d');
                            if ($dateMulai === $dateSelesai) {
                                $timeMulai = \Illuminate\Support\Carbon::parse($waktuMulai);
                                $timeSelesai = \Illuminate\Support\Carbon::parse($value);
                                if ($timeSelesai->lte($timeMulai)) {
                                    $fail('Waktu selesai harus setelah waktu mulai pada hari yang sama.');
                                }
                            }
                        } catch (\Throwable $e) {
                            // Let date validation rule handle parse errors
                        }
                    }
                }
            ],
            'keterangan'      => ['required', 'string'],
            'catatan'         => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'tanggal_selesai.after'        => 'Tanggal selesai harus setelah tanggal mulai.',
            'barang_id.required_if'        => 'Pilih barang yang ingin dipinjam.',
            'barang_id.exists'             => 'Barang tidak ditemukan di database.',
            'ruangan_id.required_if'       => 'Pilih ruangan yang ingin direservasi.',
            'ruangan_id.exists'            => 'Ruangan tidak ditemukan di database.',
            'waktu_selesai.after'          => 'Waktu selesai harus setelah waktu mulai.',
        ];
    }
}
