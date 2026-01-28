<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requests extends Model
{
    use HasFactory;

    protected $table = 'request'; 

    protected $fillable = [
        'representative_name',
        'event_name',
        'purpose',
        'item_name',
        'item_number',
        'other_purpose',
        'start_date',
        'end_date',
        'setup_date',
        'setup_time',
        'location',
        'users',
        'requested_by',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
