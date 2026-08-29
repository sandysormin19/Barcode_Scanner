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
            'nomor_transaksi' => 'required|string|max:255',
            'tanggal_keluar' => 'required|date',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'integer|exists:barangs,id',
            'qty' => 'required|array',
            'qty.*' => 'integer|min:1',
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
