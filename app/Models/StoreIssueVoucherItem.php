<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreIssueVoucherItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_issue_voucher_id',
        'item_id',
        'item_code',
        'description',
        'unit',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function storeIssueVoucher()
    {
        return $this->belongsTo(StoreIssueVoucher::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
