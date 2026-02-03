<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'supplier_name',
        'purchase_order_no',
        'total_price',
        'details',
        'project_id',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(GrnItem::class);
    }
}
