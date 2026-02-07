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

    // Relationship to the user who requested
    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // Relationship to the handler
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    // Relationship for admin who handled
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
        // If already Declined or Closed, return as-is
        if (in_array($this->status, ['Declined', 'Closed'])) {
            return $this->status;
        }

        $now = Carbon::now();

        // If Active and setup_date & setup_time exist, check if it's past
        if ($this->status === 'Active' && $this->setup_date && $this->setup_time) {
            $setupDateTime = Carbon::parse($this->setup_date . ' ' . $this->setup_time);
            if ($now->greaterThan($setupDateTime)) {
                return 'Closed';
            }
        }

        // If Active and end_date exists, check if end date has passed
        if ($this->status === 'Active' && $this->end_date) {
            $endDateTime = Carbon::parse($this->end_date)->endOfDay();
            if ($now->greaterThan($endDateTime)) {
                return 'Closed';
            }
        }

        // Default to status or Open if null
        return $this->status ?: 'Open';
    }
}

