<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'asset_type',
        'asset_name',
        'asset_code',
        'buy_rate',
        'sell_rate',
        'currency',
        'min_amount',
        'max_amount',
        'is_active',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'buy_rate' => 'decimal:8',
        'sell_rate' => 'decimal:8',
        'min_amount' => 'decimal:8',
        'max_amount' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active rates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by asset type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('asset_type', $type);
    }

    /**
     * Get rate by asset code.
     */
    public static function getByCode(string $code)
    {
        return static::where('asset_code', $code)->where('is_active', true)->first();
    }
}
