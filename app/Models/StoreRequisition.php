<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreRequisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_no',
        'project_id',
        'status',
        'description',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * Get the project that owns the requisition.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the items for the requisition.
     */
    public function items(): HasMany
    {
        return $this->hasMany(StoreRequisitionItem::class);
    }

    /**
     * Generate a unique requisition number.
     */
    public static function generateRequisitionNo(): string
    {
        $latest = self::latest()->first();
        $number = $latest ? intval($latest->requisition_no) + 1 : 1;
        return str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
