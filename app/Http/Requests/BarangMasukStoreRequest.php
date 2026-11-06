<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Barang;

class BarangMasukStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_transaksi' => ['required', 'string', 'max:255'],
            'operator' => ['sometimes', 'string', 'max:255'],
            'barang_id' => ['required', 'array', 'min:1'],
            'barang_id.*' => ['required', 'integer', 'exists:barangs,id'],
            'qty' => ['required', 'array'],
            'qty.*' => ['required', 'integer', 'min:1'],
            'tanggal_masuk' => ['required', 'date'],
            'keterangan' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'serial_number' => ['sometimes', 'array'],
            'serial_number.*' => ['array'],
            'serial_number.*.*' => ['nullable', 'string', 'max:255'],
            'serial_qty' => ['sometimes', 'array'],
            'serial_qty.*' => ['array'],
            'serial_qty.*.*' => ['nullable', 'integer', 'min:1'],
            'serial_batch' => ['sometimes', 'array'],
            'serial_batch.*' => ['array'],
            'serial_batch.*.*' => ['nullable', 'string', 'max:255'],
        ];
    }


public function withValidator(Validator $validator): void
{
    $validator->after(function (Validator $validator): void {
        $barangIds = $this->input('barang_id', []);
        if (! is_array($barangIds) || $barangIds === []) {
            return;
        }

        $barangs = Barang::query()
            ->whereIn('id', array_values($barangIds))
            ->get(['id', 'pakai_no_seri'])
            ->keyBy('id');

        $requestedSerials = [];

        foreach (array_keys($barangIds) as $index) {
            $barang = $barangs->get($barangIds[$index] ?? null);

            if (! $barang || ! $barang->pakai_no_seri) {
                continue;
            }

            $numbers = $this->input("serial_number.{$index}", []);
            $quantities = $this->input("serial_qty.{$index}", []);
            $qtyBarang = (int) $this->input("qty.{$index}", 0);

            if (! is_array($numbers) || $numbers === []) {
                $validator->errors()->add("serial_number.{$index}", 'Nomor serial wajib diisi untuk barang ini.');
                continue;
            }

            $totalSerial = 0;

            foreach ($numbers as $serialIndex => $value) {
                $serial = trim((string) $value);
                $serialQty = filter_var($quantities[$serialIndex] ?? null, FILTER_VALIDATE_INT);

                if ($serial === '') {
                    $validator->errors()->add("serial_number.{$index}.{$serialIndex}", 'Nomor serial tidak boleh kosong.');
                    continue;
                }

                $serialKey = $barang->id . '|' . $serial;
                if (isset($requestedSerials[$serialKey])) {
                    $validator->errors()->add("serial_number.{$index}.{$serialIndex}", 'Nomor serial tidak boleh duplikat untuk item barang yang sama.');
                }
                $requestedSerials[$serialKey] = true;

                if ($serialQty === false || $serialQty <= 0) {
                    $validator->errors()->add("serial_qty.{$index}.{$serialIndex}", 'Qty serial harus lebih dari 0.');
                    continue;
                }

                $totalSerial += $serialQty;
            }

            if ($totalSerial !== $qtyBarang) {
                $validator->errors()->add("serial_qty.{$index}", 'Total qty serial harus sama dengan qty barang.');
            }
        }
    });
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
