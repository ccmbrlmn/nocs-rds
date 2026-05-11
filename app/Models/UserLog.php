<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Requests;
use App\Models\User;

class UserLog extends Model
{
    protected $fillable = [
        'user_id',
        'request_id',
        'action',
        'description',
        'is_read',
        'target_user_id',
        'target_user_name',
        'actor_name',
        'actor_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id', 'id')->withDefault();
    }

}

