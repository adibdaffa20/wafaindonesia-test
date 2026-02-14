<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        // aman: jangan bikin error kalau helper belum ada
        if (function_exists('activity_log')) {
            activity_log(
                'login',
                'User login: ' . ($event->user->email ?? $event->user->name ?? 'unknown')
            );
        }
    }
}
