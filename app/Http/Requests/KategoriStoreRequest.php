<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KategoriStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('kategori')?->id ?? null;

        return [
            'nama_kategori' => 'required|max:100|unique:kategoris,nama_kategori' . ($id ? ",$id" : ''),
            'deskripsi' => 'nullable|max:255',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
        ];
    }
}
