<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $fillable = [
        'user_id',
        'request_id',
        'action',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }
}

