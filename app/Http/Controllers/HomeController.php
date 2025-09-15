<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Post; // descomenta si tienes modelo Post

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // KPIs de demo (luego los reemplazamos con datos reales)
        $kpis = [
            'benefit'      => '12–19%',
            'effectiveness'=> '93%',
            'saving'       => '23%',
        ];

        // Parámetros de sidebar (simples para empezar)
        $params = [
            'startdate' => now()->format('Y-m-d'),
            'city'      => 'Bogotá',
            'requests'  => 23,
        ];

        // Posts (usa tu fuente real; aquí dejo algo seguro)
        try {
            $posts = \App\Models\Post::latest()->limit(6)->get();
        } catch (\Exception $e) {
            // Fallback si no hay modelo Post o tabla
            $posts = collect([
                [
                    'title' => 'Movilidad Bogotá: se superaron las congestiones',
                    'category' => 'MOVILIDAD',
                    'url' => '#',
                ],
                [
                    'title' => 'Puntos de atención - Secretaría Distrital de Movilidad',
                    'category' => 'MOVILIDAD',
                    'url' => '#',
                ],
                [
                    'title' => 'Cierre en calle 100 con carrera 15 - Detalles',
                    'category' => 'MOVILIDAD',
                    'url' => '#',
                ],
            ]);
        }

        return view('home', compact('kpis','params','posts'));
    }
}