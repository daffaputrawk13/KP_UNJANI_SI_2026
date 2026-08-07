<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DukunganTeknisLog extends Model
{
    protected $fillable = [
        'satuan_id',
        'satuan_tujuan_id',
        'user_id',
        'jenis_bantuan',
        'keterangan',
    ];

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    public function satuanTujuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'satuan_tujuan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
