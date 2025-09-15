<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ImgProxyController extends Controller
{
    // Optional whitelist; keep permissive if you prefer
    private array $allowedHosts = [
        'lh3.googleusercontent.com',
    ];

    public function show(Request $request)
    {
        $url = $request->query('url');
        if (!$url || !preg_match('#^https?://#i', $url)) {
            return response('invalid url', 400);
        }

        $host = parse_url($url, PHP_URL_HOST);
        // Configurable whitelist via .env
        $enforce = filter_var(env('IMG_PROXY_ENFORCE_WHITELIST', false), FILTER_VALIDATE_BOOLEAN);
        $allowed = array_filter(array_map('trim', explode(',', env('IMG_PROXY_ALLOWED_HOSTS', implode(',', $this->allowedHosts)))));
        Log::info('imgproxy request', ['host' => $host, 'enforce_whitelist' => $enforce]);
        if ($enforce && $host && !in_array($host, $allowed)) {
            return response('host not allowed', 403);
        }

        $key = 'img_proxy_' . md5($url);
        $cached = Cache::get($key);
        if ($cached) {
            try { Cache::increment('imgproxy.cache_hits'); } catch (\Throwable $__e) { }
            return response(base64_decode($cached['data']), 200)
                ->header('Content-Type', $cached['mime'])
                ->header('Cache-Control', 'public, max-age=86400');
        }

        try {
            $resp = Http::withOptions([ 'timeout' => 8, 'allow_redirects' => ['max' => 4] ])->get($url);
            if (!$resp->ok()) throw new \Exception('bad upstream');

            $mime = $resp->header('Content-Type');
            if (!$mime || !Str::startsWith($mime, 'image/')) throw new \Exception('not image');

            $data = $resp->body();
            $save = [ 'mime' => $mime, 'data' => base64_encode($data) ];
            Cache::put($key, $save, now()->addDay());
            try { Cache::increment('imgproxy.fetches'); } catch (\Throwable $__e) { }

            return response($data, 200)->header('Content-Type', $mime)->header('Cache-Control', 'public, max-age=86400');
        } catch (\Throwable $e) {
            $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AApEB6QbIu3kAAAAASUVORK5CYII=');
            try { Cache::increment('imgproxy.errors'); } catch (\Throwable $__e) { }
            return response($png, 200)->header('Content-Type', 'image/png')->header('Cache-Control', 'public, max-age=86400');
        }
    }
}
