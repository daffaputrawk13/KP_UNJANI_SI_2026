<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonelDokumen extends Model
{
    use HasFactory;

    protected $fillable = [
        'personel_id',
        'jenis_dokumen',
        'nama_file',
        'path',
        'diunggah_oleh',
    ];

    public function personel(): BelongsTo
    {
        return $this->belongsTo(Personel::class);
    }

    public function diunggahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}
