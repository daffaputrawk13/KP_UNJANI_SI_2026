<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkunMedsos extends Model
{
    use HasFactory;

    protected $table = 'akun_medsos';

    protected $fillable = [
        'satuan_id',
        'nama_akun',
        'platform',
        'username_platform',
        'url_profil',
        'foto_profil_path',
        'status',
    ];

    /**
     * Satuan pemilik akun media sosial ini.
     */
    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    /**
     * Seluruh postingan (draft, terjadwal, maupun sudah terbit) yang dibuat
     * lewat akun ini.
     */
    public function postingan(): HasMany
    {
        return $this->hasMany(Postingan::class);
    }
}
