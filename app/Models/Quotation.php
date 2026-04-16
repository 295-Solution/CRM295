<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'client_id',
        'tanggal_penawaran',
        'nomor_penawaran',
        'nama_projek',
        'nilai_penawaran',
        'hpp',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_penawaran' => 'date',
        'nilai_penawaran' => 'decimal:2',
        'hpp' => 'decimal:2',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function histories()
    {
        return $this->hasMany(QuotationHistory::class)->latest();
    }
}