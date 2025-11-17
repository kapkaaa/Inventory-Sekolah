<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.inventaris_id' => 'required|exists:inventaris,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'tgl_pinjam' => 'required|date|after_or_equal:today',
            'tgl_estimasi_kembali' => 'required|date|after:tgl_pinjam',
            'catatan' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Minimal pilih satu barang untuk dipinjam.',
            'items.*.inventaris_id.required' => 'Barang harus dipilih.',
            'items.*.inventaris_id.exists' => 'Barang tidak ditemukan.',
            'items.*.jumlah.required' => 'Jumlah harus diisi.',
            'items.*.jumlah.min' => 'Jumlah minimal 1.',
            'tgl_pinjam.required' => 'Tanggal pinjam harus diisi.',
            'tgl_pinjam.after_or_equal' => 'Tanggal pinjam tidak boleh kurang dari hari ini.',
            'tgl_estimasi_kembali.required' => 'Tanggal estimasi kembali harus diisi.',
            'tgl_estimasi_kembali.after' => 'Tanggal kembali harus setelah tanggal pinjam.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            
            foreach ($items as $index => $item) {
                $inventaris = \App\Models\Inventaris::find($item['inventaris_id'] ?? null);
                
                if ($inventaris && !$inventaris->isAvailable($item['jumlah'] ?? 0)) {
                    $validator->errors()->add(
                        "items.{$index}.jumlah",
                        "Stok {$inventaris->nama_barang} tidak mencukupi. Tersedia: {$inventaris->jumlah_tersedia}"
                    );
                }
            }
        });
    }
}