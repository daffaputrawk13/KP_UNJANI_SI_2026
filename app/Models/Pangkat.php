<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pangkat extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'urutan',
    ];

    public const KATEGORI_TAMTAMA = 'Tamtama';
    public const KATEGORI_BINTARA = 'Bintara';
    public const KATEGORI_PERWIRA = 'Perwira';

    public function personels(): HasMany
    {
        return $this->hasMany(Personel::class);
    }
}
