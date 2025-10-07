<?php

namespace App\Jobs;

use App\Mail\LoginNoticeMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLoginNoticeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public string $ipAddress;
    public string $userAgent;
    public string $loginTime;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $ipAddress, string $userAgent, string $loginTime)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->loginTime = $loginTime;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(
            new LoginNoticeMail($this->user, $this->ipAddress, $this->userAgent, $this->loginTime)
        );
    }
}
