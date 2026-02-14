<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('activity_log')) {
    function activity_log(string $action, string $description): void
    {
        try {
            ActivityLog::create([
                'user_id'   => Auth::id(),
                'action'    => $action,
                'description' => $description,
                'ip_address'  => request()?->ip(),
            ]);
        } catch (\Throwable $e) {
            // Supaya tidak bikin aplikasi crash kalau logging gagal
            logger()->error('Activity log failed: '.$e->getMessage());
        }
    }
}
