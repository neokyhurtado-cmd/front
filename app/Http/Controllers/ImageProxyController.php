<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageProxyController extends Controller
{
    public function proxy(Request $request)
    {
        $url = $request->query('url');
        
        // Validar que la URL sea válida y segura
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->fallbackImage();
        }

        // Lista blanca de dominios permitidos
        $allowedDomains = [
            'news.googleusercontent.com',
            'lh3.googleusercontent.com', 
            'images.unsplash.com',
            'via.placeholder.com'
        ];

        $domain = parse_url($url, PHP_URL_HOST);
        if (!in_array($domain, $allowedDomains)) {
            return $this->fallbackImage();
        }

        try {
            // Intentar obtener la imagen externa
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; PanoramaAPI/1.0)',
                    'Referer' => '',
                ])
                ->get($url);

            if ($response->successful()) {
                $contentType = $response->header('Content-Type');
                
                // Validar que sea una imagen
                if (strpos($contentType, 'image/') !== 0) {
                    return $this->fallbackImage();
                }

                return response($response->body(), 200)
                    ->header('Content-Type', $contentType)
                    ->header('Cache-Control', 'public, max-age=3600')
                    ->header('Access-Control-Allow-Origin', '*');
            }
        } catch (\Exception $e) {
            Log::warning('Image proxy failed: ' . $e->getMessage(), ['url' => $url]);
        }

        return $this->fallbackImage();
    }

    private function fallbackImage()
    {
        // Devolver imagen placeholder
        $placeholderPath = public_path('images/placeholder.svg');
        
        if (file_exists($placeholderPath)) {
            return response()->file($placeholderPath, [
                'Cache-Control' => 'public, max-age=86400'
            ]);
        }

        // Si no existe placeholder, crear uno simple
        return $this->generatePlaceholder();
    }

    private function generatePlaceholder()
    {
        $svg = '<svg width="640" height="360" viewBox="0 0 640 360" xmlns="http://www.w3.org/2000/svg">
            <rect width="640" height="360" fill="#f3f4f6"/>
            <rect x="220" y="130" width="200" height="100" rx="8" fill="#e5e7eb"/>
            <rect x="230" y="140" width="160" height="4" rx="2" fill="#d1d5db"/>
            <rect x="230" y="150" width="120" height="4" rx="2" fill="#d1d5db"/>
            <rect x="230" y="160" width="140" height="4" rx="2" fill="#d1d5db"/>
            <circle cx="250" cy="190" r="16" fill="#d1d5db"/>
            <rect x="280" y="180" width="90" height="4" rx="2" fill="#d1d5db"/>
            <rect x="280" y="190" width="70" height="4" rx="2" fill="#d1d5db"/>
            <text x="320" y="270" text-anchor="middle" font-family="Arial" font-size="16" fill="#9ca3af">Análisis de Movilidad</text>
        </svg>';

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}