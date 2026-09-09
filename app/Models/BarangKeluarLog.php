<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangKeluarLog extends Model
{
    protected $fillable = [
        'barang_keluar_id',
        'status_from',
        'status_to',
        'keterangan',
        'user_id',
        'tanggal_perubahan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_perubahan' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function barangKeluar(): BelongsTo
    {
        return $this->belongsTo(BarangKeluar::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'System',
        ]);
    }
}
