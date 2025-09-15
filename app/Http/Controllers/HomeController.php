<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Post; // descomenta si tienes modelo Post

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Obtener parámetros del request
        $startDate = $request->get('start', now()->format('Y-m-d'));
        $city = $request->get('city', 'Bogotá');
        $apiRequests = $request->get('requests', 23);

        // KPIs calculados basados en parámetros (ejemplo dinámico)
        $baseEffectiveness = 93;
        $baseTravel = rand(12, 19);
        $baseSaving = 23;

        // Ajustar KPIs basado en parámetros
        if ($apiRequests > 50) {
            $baseEffectiveness += 5;
            $baseSaving += 3;
        }

        $kpis = [
            'benefit'      => $baseTravel . '–' . ($baseTravel + 7) . '%',
            'effectiveness'=> $baseEffectiveness . '%',
            'saving'       => $baseSaving . '%',
        ];

        // Parámetros de sidebar
        $params = [
            'startdate' => $startDate,
            'city'      => $city,
            'requests'  => $apiRequests,
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