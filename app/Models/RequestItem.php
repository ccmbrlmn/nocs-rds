<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    protected $fillable = [
        'request_id',
        'item_name',
        'quantity',
    ];

    public function request()
    {
        return $this->belongsTo(\App\Models\Requests::class, 'request_id');
    }
}
