<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'payment_phase',
        'net_payment',
        'start_date',
        'end_date',
        'description',
    ];

    protected $casts = [
        'net_payment' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the project that owns the revenue.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
