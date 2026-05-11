<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EditedRequestNotification extends Mailable
{
    public $requestData;
    public $changes;

    public function __construct($requestData, $changes)
    {
        $this->requestData = $requestData;
        $this->changes = $changes;
    }

    public function build()
    {
        return $this->subject('Request Updated')
            ->view('emails.edited-request')
            ->with([
                'requestData' => $this->requestData,
                'changes' => $this->changes,
            ]);
    }
}
