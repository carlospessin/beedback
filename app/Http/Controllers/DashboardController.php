<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $authenticatedUser = auth()->user();

        return Inertia::render('Dashboard', [
            'role_id' => $authenticatedUser->role_id, 
        ]);
    }
}
