<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AbsenConfirmationMail;

class SendAbsensiEmailJob implements ShouldQueue
{
    use Queueable;

    protected $email;

    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->email)->send(new AbsenConfirmationMail('https://server-samarent.com/confirm-absen?token=exampletoken'));
            Log::info('Absen confirmation email sent successfully to ' . $this->email);
        } catch (\Exception $e) {
            Log::error('Failed to send absen confirmation email to ' . $this->email . ': ' . $e->getMessage());
        }
    }
}
