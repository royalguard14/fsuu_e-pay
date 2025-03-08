<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class DashboardController extends Controller
{
   public function developer()
    {
    $activityLogs = Activity::where('causer_id', '!=', Auth::id())
                        ->latest()
                        ->get();
// $activityLogs = Activity::latest()->get();

    $latestUsers = User::where('id', '!=', Auth::id())
                       ->latest()
                       ->take(8)
                       ->get();


    $visitorData = Visitor::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                          ->where('created_at', '>=', Carbon::now()->subDays(7))
                          ->groupBy('date')
                          ->orderBy('date', 'asc')
                          ->get();

    $dates = $visitorData->pluck('date')->toArray();
    $counts = $visitorData->pluck('count')->toArray();

    return view('dashboard.developer', compact('dates', 'counts','activityLogs','latestUsers'));
    }

    public function user()
    {
    
        return view('dashboard.user');
    }
}
