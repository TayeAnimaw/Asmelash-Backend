<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreIssueVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'request_no',
        'project_id',
        'user_id',
        'receiver_name',
        'driver_name',
        'plate_no',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
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
        return $this->hasMany(StoreIssueVoucherItem::class);
    }
}
