<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_client',
        'perusahaan',
        'no_hp',
        'email',
        'alamat',
        'sumber_lead',
        'status',
        'assigned_to',
        'notes',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }


    public function statusHistories(): HasMany
    {
        return $this->hasMany(LeadStatusHistory::class);
    }
}