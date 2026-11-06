<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BarcodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barang_id' => ['required', 'array', 'min:1', 'max:500'],
            'barang_id.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('barangs', 'id')->where(
                    fn ($query) => $query->where('is_active', true)
                ),
            ],
            'jumlah_cetak' => ['required', 'array'],
            'jumlah_cetak.*' => ['required', 'integer', 'min:1', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'barang_id.required' => 'Pilih minimal satu barang.',
            'barang_id.min' => 'Pilih minimal satu barang.',
            'barang_id.max' => 'Cetak maksimal 500 barang sekaligus.',
            'jumlah_cetak.*.required' => 'Jumlah barcode wajib diisi.',
            'jumlah_cetak.*.integer' => 'Jumlah barcode harus berupa angka bulat.',
            'jumlah_cetak.*.min' => 'Jumlah barcode minimal 1.',
            'jumlah_cetak.*.max' => 'Jumlah barcode maksimal 500 per barang.',
        ];
    }
}
