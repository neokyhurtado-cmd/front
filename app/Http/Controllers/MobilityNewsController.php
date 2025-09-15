<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MobilityNewsController extends Controller
{
    private function absolutize(string $base, string $maybe): string
    {
        if (str_starts_with($maybe, 'http://') || str_starts_with($maybe, 'https://')) return $maybe;
        $u = parse_url($base);
        $scheme = $u['scheme'] ?? 'https';
        $host = $u['host'] ?? null;
        $port = isset($u['port']) ? ':' . $u['port'] : '';
        $root = $scheme . '://' . ($host ?? '') . $port;

        if (str_starts_with($maybe, '//')) return $scheme . ':' . $maybe;
        if (str_starts_with($maybe, '/')) return $root . $maybe;

        $path = isset($u['path']) ? preg_replace('#/[^/]*$#', '/', $u['path']) : '/';
        return rtrim($root . $path, '/') . '/' . ltrim($maybe, '/');
    }

    private function extractImageFromHtml(string $url): ?string
    {
        try {
            $resp = Http::withOptions(['allow_redirects' => ['max' => 4], 'timeout' => 6])->get($url);
            if (!$resp->ok()) return null;
            $html = $resp->body();

            $candidates = [];
            // meta tags
            if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) $candidates[] = $m[1];
            if (preg_match('/<meta[^>]+name=["\']twitter:image(:src)?["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m2)) $candidates[] = $m2[count($m2)-1];
            if (preg_match('/<link[^>]+rel=["\']image_src["\'][^>]+href=["\']([^"\']+)["\']/i', $html, $m3)) $candidates[] = $m3[1];

            // srcset or data-srcset in meta/img tags
            if (preg_match_all('/<([^>]+)srcset=["\']([^"\']+)["\']/i', $html, $ss)) {
                foreach ($ss[2] as $s) {
                    $picked = $this->pickFromSrcset($s);
                    if ($picked) $candidates[] = $picked;
                }
            }
            if (preg_match_all('/<([^>]+)data-srcset=["\']([^"\']+)["\']/i', $html, $dss)) {
                foreach ($dss[2] as $s) {
                    $picked = $this->pickFromSrcset($s);
                    if ($picked) $candidates[] = $picked;
                }
            }

            // data-src / data-image / lazy attributes
            if (preg_match_all('/<img[^>]+(?:data-src|data-image|data-lazy|data-original)=["\']([^"\']+)["\'][^>]*>/i', $html, $dimgs)) {
                foreach ($dimgs[1] as $src) $candidates[] = $src;
            }

            // basic src images
            if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $imgs)) {
                foreach ($imgs[1] as $src) $candidates[] = $src;
            }

            // Normalize and pick first valid image (prefer largest by extension or srcset choice)
            foreach ($candidates as $cand) {
                $cand = trim(html_entity_decode($cand));
                if (empty($cand)) continue;
                $abs = $this->absolutize($resp->effectiveUri() ?? $url, $cand);
                if (preg_match('/\.(jpg|jpeg|png|webp)(\?.*)?$/i', $abs)) return $abs;
            }
        } catch (\Throwable $e) {
            return null;
        }
        return null;
    }

    private function pickFromSrcset(string $srcset): ?string
    {
        // Parse srcset and pick the candidate with largest width descriptor
        // Example: "a.jpg 100w, b.jpg 400w, c.jpg 800w"
        $parts = array_map('trim', explode(',', $srcset));
        $best = null; $bestScore = 0;
        foreach ($parts as $p) {
            if (preg_match('/^(\S+)(?:\s+(\d+)w)?(?:\s+(\d+(?:\.\d+)?)x)?$/i', $p, $m)) {
                $url = $m[1];
                $w = isset($m[2]) ? (int)$m[2] : 0;
                $d = isset($m[3]) ? (float)$m[3] : 0.0;
                $score = $w > 0 ? $w : intval($d * 1000);
                if ($score > $bestScore) { $bestScore = $score; $best = $url; }
            }
        }
        return $best;
    }
    public function index(Request $request)
    {
        // Ensure counters exist to avoid repeated creation SELECTs on the DB cache driver
        try {
            Cache::add('mobility.image.misses', 0, 86400);
            Cache::add('mobility.image.hits', 0, 86400);
        } catch (\Throwable $__e) {
            // noop: best-effort initialization
        }
        // Cachea 5 minutos para evitar rate limits (guardamos lista completa)
        $items = self::fetchAndCache();

        // Parámetros para filtrado/paginación
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(1, min(100, (int) $request->query('per_page', 12)));
        $q = trim((string) $request->query('q', ''));
        $tag = trim((string) $request->query('tag', ''));

        // Filtrado por búsqueda y etiqueta
        $filtered = $items;
        if ($q !== '') {
            $filtered = array_values(array_filter($filtered, function ($it) use ($q) {
                return stripos($it['title'] ?? '', $q) !== false || stripos($it['href'] ?? '', $q) !== false;
            }));
        }
        if ($tag !== '') {
            $tagUp = strtoupper($tag);
            $filtered = array_values(array_filter($filtered, function ($it) use ($tagUp) {
                return isset($it['tag']) && strtoupper($it['tag']) === $tagUp;
            }));
        }

        $total = count($filtered);
        $start = ($page - 1) * $perPage;
        $data = array_slice($filtered, $start, $perPage);

        $meta = [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
        ];

        return response()->json(['data' => $data, 'meta' => $meta]);
    }

    /**
     * Fetch feed, normalize items and cache result.
     * Public so it can be reused by commands/jobs.
     *
     * @return array
     */
    public static function fetchAndCache(): array
    {
        try {
            Cache::add('mobility.image.misses', 0, 86400);
            Cache::add('mobility.image.hits', 0, 86400);
        } catch (\Throwable $__e) { }

        return Cache::remember('mobility_news', 300, function () {
            $rssUrl = 'https://news.google.com/rss/search?q=movilidad%20Bogotá&hl=es-419&gl=CO&ceid=CO:es-419';

            $resp = Http::timeout(8)->get($rssUrl);
            if (!$resp->ok()) return [];

            $xml = @simplexml_load_string($resp->body());
            if (!$xml || !isset($xml->channel->item)) return [];

            $now = Carbon::now();
            $map = [];
            $idx = 0;

            foreach ($xml->channel->item as $item) {
                $title = (string) $item->title;
                $link  = (string) $item->link;
                $pub   = isset($item->pubDate) ? Carbon::parse((string)$item->pubDate) : $now;

                $lower = mb_strtolower($title, 'UTF-8');
                $tag = 'INFO';
                if (str_contains($lower, 'accidente') || str_contains($lower, 'choque')) $tag = 'INCIDENTE';
                elseif (str_contains($lower, 'obra') || str_contains($lower, 'mantenimiento') || str_contains($lower, 'cierre')) $tag = 'OBRAS';
                elseif (str_contains($lower, 'pmt') || str_contains($lower, 'desvío') || str_contains($lower, 'desvio')) $tag = 'ALERTA';
                elseif (str_contains($lower, 'servicio') || str_contains($lower, 'frecuencia')) $tag = 'SERVICIO';
                elseif (str_contains($lower, 'aviso') || str_contains($lower, 'comunicado')) $tag = 'AVISO';

                $minutesAgo = (int) round($pub->diffInMinutes($now));

                $image = null;
                if (isset($item->children('media', true)->content)) {
                    $attrs = $item->children('media', true)->content->attributes();
                    $image = (string) ($attrs['url'] ?? null);
                }

                if (!$image && isset($item->enclosure)) {
                    $encAttrs = $item->enclosure->attributes();
                    $image = (string) ($encAttrs['url'] ?? null);
                }

                if (!$image && filter_var($link, FILTER_VALIDATE_URL)) {
                    try {
                        // Use a short-lived client to attempt extraction; keep caller context
                        $ctl = new self();
                        $image = $ctl->extractImageFromHtml($link);
                    } catch (\Throwable $__e) {
                        $image = null;
                    }

                    if ($image) {
                        try { Cache::increment('mobility.image.hits'); } catch (\Throwable $__e) { }
                    } else {
                        try { Cache::increment('mobility.image.misses'); } catch (\Throwable $__e) { }
                    }
                }

                $map[] = [
                    'id'         => ++$idx,
                    'title'      => $title,
                    'href'       => $link,
                    'tag'        => $tag,
                    'minutesAgo' => $minutesAgo,
                    'publishedAt'=> $pub->toIso8601String(),
                    'source'     => 'google-news',
                    'image'      => $image,
                ];
            }

            foreach ($map as &$row) { $row['minutesAgo'] = (int) ($row['minutesAgo'] ?? 0); } unset($row);

            return collect($map)->sortBy('minutesAgo')->values()->all();
        });
    }
}
