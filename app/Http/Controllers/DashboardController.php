<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        if (Auth::user()->is_role == 1) {
            return view('dashboard.admin_list');
        } else if (Auth::user()->is_role == 2) {
            return view('dashboard.user_list');
        }
    }
}
