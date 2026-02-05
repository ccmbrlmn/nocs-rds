<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Requests extends Model
{
    use HasFactory;

    protected $table = 'request'; 

    protected $fillable = [
        'representative_name',
        'event_name',
        'purpose',
        'items',
        'other_purpose',
        'start_date',
        'end_date',
        'setup_date',
        'setup_time',
        'location',
        'users',
        'requested_by',
        'status',
        'personnel_name',
        'other_equipments',
        'decline_reason',
        'cancel_reason',
        'handled_by',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    
        public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
    
        public function handledByAdmin()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
    
public function getComputedStatusAttribute()
{
    if ($this->status === 'Declined') {
        return 'Canceled';
    }

    $today = Carbon::today();

    if ($this->status === 'Closed') {
        return 'Closed';
    }

    if ($this->end_date && Carbon::parse($this->end_date)->endOfDay()->isPast()) {
        return 'Closed';
    }

    if ($this->setup_date && Carbon::parse($this->setup_date)->isPast() && $this->status !== 'Closed') {
        return 'Closed';
    }

    return $this->status ?: 'Open';
}


    

    
    
}

