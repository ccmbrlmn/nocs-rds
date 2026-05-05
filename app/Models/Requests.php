<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Requests extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_name',
        'requested_employee',
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
        'note',
        'requested_by',
        'status',
        'personnel_name',
        'other_equipments',
        'decline_reason',
        'cancel_reason',
        'handled_by',
        'handled_at',
        
        'approved_at',
        'active_at',
        'returned_at',
        'retrieved_at',
    
    ];

    protected $casts = [
        'items' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'setup_date' => 'datetime',
        'handled_at' => 'datetime',
        
        'approved_at' => 'datetime',
        'active_at' => 'datetime',
        'returned_at' => 'datetime',
        'retrieved_at' => 'datetime',
    
    ];
    
    protected $appends = ['computed_status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by')->withTrashed();
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function handledByAdmin()
    {
        return $this->belongsTo(User::class, 'handled_by')->withTrashed();
    }

    /**
     * Computed status for calendar and user requests
     * Returns: Open, Active, Closed, Declined
     */
    public function getComputedStatusAttribute()
    {
        if ($this->status === 'Pending Return') {
            return 'Pending Return';
        }

        if ($this->status === 'Pending Retrieval') {
            return 'Pending Retrieval';
        }

        if ($this->status === 'Open') {
            return 'Open';
        }

        if ($this->status === 'Active') {
            return 'Active';
        }

        if ($this->status === 'Closed') {
            return 'Closed';
        }

        if ($this->status === 'Declined') {
            return 'Declined';
        }

        return $this->status;
    }
    
    public function items()
    {
        return $this->hasMany(\App\Models\RequestItem::class, 'request_id');
    }
    
    public function assetTransactions()
    {
        return $this->hasMany(\App\Models\AssetTransaction::class, 'request_id');
    }
    
    public function transactions()
    {
        return $this->hasMany(\App\Models\AssetTransaction::class, 'request_id');
    }
}

