<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationDashboardController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ActivityLogController;
use App\Models\ActivityLog;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

// Landing Page (Form)
Route::view('/form', 'home.index')->name('form');
Route::post('/leads', [RegistrationController::class, 'store'])->name('leads');


/*
|--------------------------------------------------------------------------
| Dashboard (Harus Login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('dashboard.index');
    })->name('dashboard');

    Route::view('/dashboard/leads', 'dashboard.index')->name('dashboard.index');

    // ✅ Halaman Activity Log (Vue page)
    Route::get('/dashboard/api/activity-logs', function (Request $request) {
    $q = ActivityLog::with('user')->latest();

    if ($request->filled('date')) {
        $q->whereDate('created_at', $request->date);
    } elseif ($request->filled('month') && $request->filled('year')) {
        $q->whereMonth('created_at', $request->month)
          ->whereYear('created_at', $request->year);
    } elseif ($request->filled('year')) {
        $q->whereYear('created_at', $request->year);
    }

    return $q->paginate(15)->withQueryString();
});

    // ✅ API Activity Log
    Route::get('/dashboard/api/activity-logs', [ActivityLogController::class, 'index']);

    // API Leads
    Route::get('/dashboard/api/leads', [RegistrationDashboardController::class, 'index']);
    Route::get('/dashboard/api/leads/{registration}', [RegistrationDashboardController::class, 'show']);
    Route::put('/dashboard/api/leads/{registration}', [RegistrationDashboardController::class, 'update']);
    Route::delete('/dashboard/api/leads/{registration}', [RegistrationDashboardController::class, 'destroy']);
});
// Captcha
    Route::get('/refresh-captcha', function () {
    return response()->json(['captcha'=> captcha_img('flat')]);
    });


require __DIR__.'/auth.php';
