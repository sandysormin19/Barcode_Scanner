<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangMasukStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
        'nomor_transaksi' => 'required|string|max:255',
        'operator' => 'sometimes|string|max:255',
        'barang_id' => 'required|array|min:1',
        'barang_id.*' => 'integer|exists:barangs,id',
        'qty' => 'required|array',
        'qty.*' => 'integer|min:1',
        'tanggal_masuk' => 'required|date',
        'keterangan' => 'sometimes|string|max:1000',
        'serial_number' => 'sometimes|array',
        'serial_qty' => 'sometimes|array',
    ];
    }

    public function messages(): array
    {
        return [
            'nomor_transaksi.required' => 'Nomor transaksi wajib diisi.',
            'barang_id.required' => 'Pilih minimal satu barang.',
            'qty.*.min' => 'Qty minimal 1.',
        ];
    }
}
