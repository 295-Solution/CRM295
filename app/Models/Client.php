<?php

namespace App\Models;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory;

    public const CUSTOM_BUSINESS_TYPE = 'ketik_sendiri';

    public const SOURCE_OPTIONS = [
        'relasi',
        'website',
        'sosmed',
        'flyering',
        'offline',
    ];

    public const BUSINESS_TYPE_OPTIONS = [
        'Komersial & Retail',
        'Residensial',
        'Pemerintahan & Institusi',
        'Fasilitas Publik & Sosial',
        'Industri',
    ];

    protected $fillable = [
        'nama',
        'perusahaan',
        'nomor_wa',
        'sumber_client',
        'jenis_bisnis',
    ];

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}
