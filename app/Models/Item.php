<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'item_code',
        'category',
        'unit',
        'unit_price',
        'description',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    // Relationships
    public function grnItems()
    {
        return $this->hasMany(GrnItem::class);
    }

    public function stockBalances()
    {
        return $this->hasMany(StockBalance::class);
    }

    public function stockAdjustmentItems()
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }

    public function storeIssueVoucherItems()
    {
        return $this->hasMany(StoreIssueVoucherItem::class);
    }
}
