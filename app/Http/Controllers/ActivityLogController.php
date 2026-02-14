<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $logs = ActivityLog::with('user')
            ->when($q, function ($query) use ($q) {
                $query->where('action', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%")
                      ->orWhereHas('user', function ($u) use ($q) {
                          $u->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                      });
            })
            ->latest()
            ->paginate(15);

        return response()->json($logs);
    }
}
