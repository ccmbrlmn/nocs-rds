<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_name',
        'asset_tag',
        'asset_serial',
        'asset_model',
        'asset_category',
        'asset_status',
         'created_by',
    ];
    
    public function transactions()
    {
        return $this->hasMany(\App\Models\AssetTransaction::class);
    }
    
    public function latestTransaction()
    {
        return $this->hasOne(\App\Models\AssetTransaction::class)->latestOfMany();
    }
    
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function getComputedStatusAttribute()
    {
        $latest = $this->transactions()
            ->orderByDesc('id')
            ->first();

        if (!$latest || $latest->status === 'Returned') {
            return 'Available';
        }

        if ($latest->status === 'Borrowed') {
            return 'In Use';
        }

        return 'Available';
    }
}
