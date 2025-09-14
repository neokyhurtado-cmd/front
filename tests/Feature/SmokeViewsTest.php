<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

describe('Smoke Tests - Views', function () {
    
    test('home responde y contiene el wrapper', function () {
        $response = $this->get('/');
        
        $response->assertOk()
                ->assertSee('news-scope')
                ->assertSee('blog automatizado');
    });

    test('compilar vistas no falla', function () {
        // Limpiar primero
        \Artisan::call('view:clear');
        
        // Intentar compilar
        $code = \Artisan::call('view:cache');
        
        expect($code)->toBe(0);
        
        // Limpiar después del test
        \Artisan::call('view:clear');
    });

    test('rutas principales funcionan', function () {
        $routes = [
            '/',
            '/healthz',
            '/blog'
        ];
        
        foreach ($routes as $route) {
            $response = $this->get($route);
            expect($response->getStatusCode())->toBeLessThan(500);
        }
    });

    test('no hay directivas blade en partials', function () {
        $partialPath = resource_path('views/partials');
        
        if (!is_dir($partialPath)) {
            $this->markTestSkipped('No partials directory found');
        }
        
        $files = glob($partialPath . '/*.blade.php');
        $badDirectives = ['@extends', '@section', '@endsection', '@push', '@endpush'];
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            foreach ($badDirectives as $directive) {
                expect($content)->not->toContain($directive, 
                    "Partial {$file} contains forbidden directive: {$directive}");
            }
        }
    });
});