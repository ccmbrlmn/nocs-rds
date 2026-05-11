<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetTransaction extends Model
{
    protected $fillable = [
        'asset_id',
        'request_id',
        'user_id',
        'status',
        'borrowed_at',
        'returned_at',
        'retrieved_at',
        'personnel_name',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function request()
    {
        return $this->belongsTo(\App\Models\Requests::class, 'request_id');
    }
}
