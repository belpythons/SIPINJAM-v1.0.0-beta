<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipe_peminjaman' => ['required', 'in:barang,ruangan'],
            'barang_id'       => ['required_if:tipe_peminjaman,barang', 'nullable', 'exists:barangs,id'],
            'ruangan_id'      => ['required_if:tipe_peminjaman,ruangan', 'nullable', 'exists:ruangans,id'],
            'tanggal_mulai'   => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'waktu_mulai'     => ['required', 'date_format:H:i'],
            'waktu_selesai'   => ['required', 'date_format:H:i', 'after:waktu_mulai'],
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
