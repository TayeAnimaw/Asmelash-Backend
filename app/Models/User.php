<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function productionReports()
    {
        return $this->hasMany(ProductionReport::class);
    }
    public function troubles()
    {
        return $this->hasMany(Trouble::class);
    }
    public function fleetReports()
    {
        return $this->hasMany(FleetReport::class);
    }
    public function qualityTests()
    {
        return $this->hasMany(QualityTest::class);
    }
    public function qualityReports()
    {
        return $this->hasMany(QualityReport::class);
    }
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
