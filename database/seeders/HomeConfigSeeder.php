<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomeConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            // KPIs
            [
                'key' => 'kpi_1',
                'value' => json_encode(['label' => 'Aprobación PMT', 'value' => '92%', 'color' => '#8B5CF6']),
                'type' => 'json',
                'group' => 'kpis',
                'description' => 'KPI 1 para mostrar en el home'
            ],
            [
                'key' => 'kpi_2', 
                'value' => json_encode(['label' => 'Tiempo ahorrado', 'value' => '40%', 'color' => '#00E5FF']),
                'type' => 'json',
                'group' => 'kpis',
                'description' => 'KPI 2 para mostrar en el home'
            ],
            [
                'key' => 'kpi_3',
                'value' => json_encode(['label' => 'Proyectos', 'value' => '120', 'color' => '#39FF14']),
                'type' => 'json', 
                'group' => 'kpis',
                'description' => 'KPI 3 para mostrar en el home'
            ],
            
            // Tags/Chips
            [
                'key' => 'home_tags',
                'value' => json_encode(['movilidad', 'tránsito', 'señalización', 'Bogotá', 'TransMilenio', 'seguridad vial']),
                'type' => 'json',
                'group' => 'tags',
                'description' => 'Tags que aparecen como chips en el header del home'
            ],
            
            // Configuraciones generales
            [
                'key' => 'cache_duration',
                'value' => '5',
                'type' => 'number',
                'group' => 'performance',
                'description' => 'Duración del cache del home en minutos'
            ],
            [
                'key' => 'max_pinned_posts',
                'value' => '3',
                'type' => 'number', 
                'group' => 'content',
                'description' => 'Número máximo de posts destacados'
            ],
            [
                'key' => 'grid_posts_limit',
                'value' => '12',
                'type' => 'number',
                'group' => 'content',
                'description' => 'Número de posts en el grid normal'
            ],
        ];
        
        foreach ($configs as $config) {
            \App\Models\HomeConfig::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}
