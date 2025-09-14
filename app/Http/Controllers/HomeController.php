<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ajusta según tu modelo real
        $posts = \App\Models\Post::latest()->take(6)->get();
        return view('home', compact('posts'));
    }
}