<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the dashboard.
     */
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'posts' => Post::count(),
            'requests' => rand(1200, 2000), // Simulated API requests
        ];

        return view('dashboard.index', compact('stats'));
    }
}
