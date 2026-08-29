<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarcodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'integer|exists:barangs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'barang_id.required' => 'Pilih Barang minimal satu.',
        ];
    }
}
