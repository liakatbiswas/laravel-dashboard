<?php

namespace App\Http\Controllers;

use App\Models\AdmissionOnOff;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $showStats = (bool) $request->user()?->can('users.view');

        return view('dashboard', [
            'showStats' => $showStats,
            'totalUsers' => $showStats ? User::count() : null,
            'activeUsers' => $showStats ? User::where('is_active', true)->count() : null,
            'totalRoles' => $showStats ? Role::count() : null,
            'admissionOn' => AdmissionOnOff::isOn(),
        ]);
    }
}
