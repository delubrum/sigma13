<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $to,
        public Mailable $mailable
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        logger('SendEmailJob executing', ['to' => $this->to]);

        Mail::to($this->to)->send($this->mailable);

        logger('SendEmailJob completed');
    }
}
