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
    ];

public function user() {
    return $this->belongsTo(User::class, 'requested_by');
}
    
    public function request()
{
    return $this->belongsTo(Requests::class, 'request_id', 'id')->withDefault();
}



}

