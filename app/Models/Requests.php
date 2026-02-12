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
        'handled_at',
    ];

    protected $casts = [
        'items' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'setup_date' => 'datetime',
        'handled_at' => 'datetime',
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

    /**
     * Computed status for calendar and user requests
     * Returns: Open, Active, Closed, Declined
     */
    public function getComputedStatusAttribute()
    {
        if (in_array($this->status, ['Declined', 'Closed'])) {
            return $this->status;
        }

        $now = Carbon::now();

if ($this->status === 'Active' && $this->setup_date && $this->setup_time) {
    $setupDate = $this->setup_date->format('Y-m-d'); 
    $setupDateTime = Carbon::parse("$setupDate {$this->setup_time}");

    if ($now->greaterThan($setupDateTime)) {
        return 'Closed';
    }
}

        return $this->status ?: 'Open';
    }
}

