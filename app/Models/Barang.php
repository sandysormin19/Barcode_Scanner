<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Barang extends Model
{
    protected $fillable = [
        'kategori_id',
        'kode_barang',
        'item_barang',
        'harga_satuan',
        'nama_barang',
        'unit',
        'barang_induk',
        'brand',
        'item_sablon',
        'proses',
        'handfeel',
        'pakai_no_seri',
        'paksa_no_seri',
        'tipe_seri_batch',
        'tipe_barang',
        'departemen',
        'is_active',

    ];

    /**
     * Tampilkan satuan sesuai kode item, termasuk untuk data lama.
     */
    public function getUnitAttribute($value): string
    {
        $itemBarang = strtoupper((string) ($this->attributes['item_barang'] ?? ''));

        if (str_contains($itemBarang, '[PC]')
            || str_contains($itemBarang, '[BS]')) {
            return 'pcs';
        }

        if (in_array(strtolower(trim((string) $value)), ['lusin', 'lsn'], true)) {
            return 'lsn';
        }

        return (string) $value;
    }

    public function kategori(): BelongsTo
     {
        return $this->belongsTo(Kategori::class);
     }

    public function stok()
     {
        return $this->hasOne(StokBarang::class);
     }

    public function barangMasukDetails(): HasMany
    {
        return $this->hasMany(BarangMasukDetail::class, 'barang_id');
    }

    /**
     * Kode untuk ditampilkan kepada pengguna; kode_barang tetap menjadi ID unik internal.
     */
    public function getKodeTampilanAttribute(): string
    {
        $barangInduk = trim((string) $this->barang_induk);
        $itemBarang = trim((string) $this->item_barang);

        if ($barangInduk !== '' && $itemBarang !== '') {
            return $barangInduk . '-' . $itemBarang;
        }

        $kodeInternal = trim((string) $this->kode_barang);

        // Data lama mungkin belum menyimpan barang_induk; lepaskan suffix item+hash.
        if ($itemBarang !== '') {
            $suffix = '-' . substr($itemBarang, 0, 10) . '-';
            $position = strrpos($kodeInternal, $suffix);

            if ($position !== false) {
                return substr($kodeInternal, 0, $position) . '-' . $itemBarang;
            }
        }

        return preg_replace('/-[a-f0-9]{16}$/i', '', $kodeInternal) ?? $kodeInternal;
    }

     public static function cariDenganKode(string $kodeBarang): ?self
     {
        return self::query()
        ->select([
            'id',
            'kode_barang',
            'barang_induk',
            'item_barang',
            'nama_barang',
            'unit',
            'pakai_no_seri',
        ])
        ->where('is_active', true)
        ->where(function ($query) use ($kodeBarang) {
            $query->where('kode_barang', $kodeBarang);

            if (ctype_digit($kodeBarang)) {
                $query->orWhere('id', (int) $kodeBarang);
            }
        })
        ->first();
        }
/**
 * @return array{barang: self, batch: string, serial_number: string}|null
 */

public static function cariDenganItemBarang(string $itemBarang): ?self
{
    return self::query()
        ->where('is_active', true)
        ->where('item_barang', $itemBarang)
        ->first();
}

/**
 * @return array{barang: self, serial_number: string}|null
 */
public static function parseBarcode(string $barcode): ?array
{
    $parts = explode('|', trim($barcode));

    if (count($parts) !== 2) {
        return null;
    }

    [$itemBarang, $serialNumber] = array_map('trim', $parts);

    if ($itemBarang === '' || $serialNumber === '') {
        return null;
    }

    $barang = self::cariDenganItemBarang($itemBarang);

    if ($barang === null) {
        return null;
    }

    $serialExists = BarangMasukSerial::query()
        ->where('serial_number', $serialNumber)
        ->whereHas('detail', function ($query) use ($barang): void {
            $query->where('barang_id', $barang->id);
        })
        ->exists();

    if (! $serialExists) {
        return null;
    }

    return [
        'barang' => $barang,
        'serial_number' => $serialNumber,
    ];
}


public function generateBarcode(): string
{
    $generator = new BarcodeGeneratorPNG();
    $value = trim((string) $this->item_barang);

    return base64_encode(
        $generator->getBarcode(
            $value,
            $generator::TYPE_CODE_128,
            2,
            70
        )
    );
}

}
