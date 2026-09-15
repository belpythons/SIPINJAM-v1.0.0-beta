<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StorePengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis' => ['required', 'in:' . \App\Models\Pengajuan::JENIS_TUNGGAL . ',' . \App\Models\Pengajuan::JENIS_EVENT],

            // Langkah 2: Identitas kegiatan (dilewati jika jenis=tunggal)
            'nama_kegiatan' => ['sometimes', 'required_if:jenis,event', 'string'],
            'unit_penyelenggara' => ['sometimes', 'required_if:jenis,event', 'string'],
            'jumlah_peserta' => ['sometimes', 'integer', 'min:0', 'required_if:jenis,event'],
            'pj_nama' => ['sometimes', 'required_if:jenis,event', 'string'],
            'pj_phone' => ['sometimes', 'required_if:jenis,event',
                'regex:/^(\+62|0)?8\d{8,12}$/', 'max:15'],
            'deskripsi' => ['sometimes', 'string'],

            // Lampiran proposal (wajib jika jenis=event)
            'lampiran_proposal' => ['sometimes', 'file', 'mimes:pdf,doc,docx', 'max:5120',
                'required_if:jenis,' . \App\Models\Pengajuan::JENIS_EVENT],

            // Langkah 3: Jadwal utama
            'mulai_at' => ['required', 'date_format:Y-m-d H:i', 'after_or_equal:today'],
            'selesai_at' => ['required', 'date_format:Y-m-d H:i', 'after:mulai_at'],

            // Langkah 4: Items array - minimal 1 item, maksimal config.maks_item_per_pengajuan
            'items' => ['required', 'array', 'min:1', 'max:' . config('sipinjam.maks_item_per_pengajuan', 20)],
            'items.*.assetable_type' => ['required', 'in:App\Models\Ruangan,App\Models\Barang'],
            'items.*.assetable_id' => ['required', 'exists:ruangans,id', 'exists:barangs,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.mulai_at' => ['required', 'date_format:Y-m-d H:i', 'after_or_equal:today'],
            'items.*.selesai_at' => ['required', 'date_format:Y-m-d H:i', 'after:items.*.mulai_at'],

            // Langkah 5: Tata tertib
            'tata_tertib' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis.required' => 'Jenis pengajuan wajib dipilih.',
            'jenis.in' => 'Jenis pengajuan tidak valid. Pilih "tunggal" atau "event".',

            'nama_kegiatan.required_if' => 'Nama kegiatan wajib diisi untuk pengajuan event.',
            'unit_penyelenggara.required_if' => 'Unit/ormawa penyelenggara wajib diisi untuk pengajuan event.',
            'jumlah_peserta.integer.required_if' => 'Jumlah peserta wajib berupa angka untuk pengajuan event.',
            'pj_nama.required_if' => 'Nama PJ wajib diisi.',
            'pj_phone.regex' => 'Nomor WA harus format Indonesia (+628xx atau 08xx).',
            'pj_phone.max' => 'Nomor terlalu panjang.',
            'lampiran_proposal.required_if' => 'Lampiran proposal wajib untuk pengajuan event.',
            'lampiran_proposal.max' => 'Ukuran file maksimal 5 MB.',
            'lampiran_proposal.mimes' => 'Proposal harus berformat PDF, DOC, atau DOCX.',

            'mulai_at.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'selesai_at.after' => 'Tanggal selesai harus setelah tanggal mulai.',

            'items.min' => 'Minimal 1 item wajib ditambahkan.',
            'items.max' => 'Maksimal {{ max }} item per pengajuan.',

            'items.*.assetable_type.required' => 'Tipe aset wajib diisi.',
            'items.*.assetable_id.exists' => 'Aset yang dipilih tidak ditemukan.',
            'items.*.jumlah.required' => 'Jumlah wajib diisi.',
            'items.*.jumlah.min' => 'Jumlah minimal 1.',
            'items.*.mulai_at.required' => 'Mulai jadwal wajib diisi.',
            'items.*.mulai_at.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'items.*.selesai_at.required' => 'Selesai jadwal wajib diisi.',
            'items.*.selesai_at.after' => 'Waktu selesai harus setelah waktu mulai.',

            'tata_tertib.required' => 'Centang tata tertib untuk melanjutkan.',
        ];
    }
}