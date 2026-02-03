<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'store_keeper_id',
        'status',
        'remarks',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function storeKeeper()
    {
        return $this->belongsTo(User::class, 'store_keeper_id');
    }

    public function items()
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }
}
