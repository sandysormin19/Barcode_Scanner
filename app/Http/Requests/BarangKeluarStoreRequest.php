<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangKeluarStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_transaksi' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:pending,packed,in_transit,delivered,cancelled'],
            'courier' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'tanggal_keluar' => ['required', 'date'],

            'barang_id' => ['required', 'array', 'min:1'],
            'barang_id.*' => ['required', 'integer', 'exists:barangs,id'],

            'qty' => ['required', 'array'],
            'qty.*' => ['required', 'integer', 'min:1'],

            'serial_number' => ['sometimes', 'array'],
            'serial_number.*' => ['sometimes', 'array'],
            'serial_number.*.*' => ['nullable', 'string', 'max:255'],

            'serial_qty' => ['sometimes', 'array'],
            'serial_qty.*' => ['sometimes', 'array'],
            'serial_qty.*.*' => ['nullable', 'integer', 'min:1'],

            'serial_batch' => ['sometimes', 'array'],
            'serial_batch.*' => ['sometimes', 'array'],
            'serial_batch.*.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_transaksi.required' => 'Nomor transaksi wajib diisi.',
            'status.in' => 'Status tidak valid.',
            'barang_id.required' => 'Pilih minimal satu barang.',
            'qty.*.min' => 'Qty minimal 1.',
        ];
    }
}
