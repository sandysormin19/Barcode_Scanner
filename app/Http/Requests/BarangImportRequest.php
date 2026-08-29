<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // max 10MB
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File import wajib diunggah.',
            'file.mimes' => 'Format file harus xlsx atau xls.',
        ];
    }
}
