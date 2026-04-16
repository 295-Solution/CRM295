<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationHistory extends Model
{
    protected $fillable = [
        'quotation_id',
        'nilai_penawaran',
        'hpp',
        'status',
        'changed_by',
        'catatan',
    ];

    protected $casts = [
        'nilai_penawaran' => 'decimal:2',
        'hpp' => 'decimal:2',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
