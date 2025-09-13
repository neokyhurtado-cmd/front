<?php

namespace App\Services;

use FeedIo\FeedIo;
use FeedIo\Adapter\Http\Client;
use App\Models\Post;
use Illuminate\Support\Str;
use GuzzleHttp\Client as GuzzleClient;

class RssIngest 
{
    /** @return array<string,string> fuente=>url */
    public function feeds(): array 
    {
        return [
            'Google News - Movilidad Bogotá' => 'https://news.google.com/rss/search?q=movilidad+Bogota+SDM&hl=es-419&gl=CO&ceid=CO:es-419',
            'Google News - Cierres viales'   => 'https://news.google.com/rss/search?q=PMT+Bogota+cierres+viales&hl=es-419&gl=CO&ceid=CO:es-419',
            'Google News - Transporte público' => 'https://news.google.com/rss/search?q=TransMilenio+SITP+Bogota&hl=es-419&gl=CO&ceid=CO:es-419',
            'Google News - Señalización vial' => 'https://news.google.com/rss/search?q=señalizacion+vial+Bogota&hl=es-419&gl=CO&ceid=CO:es-419',
        ];
    }

    public function run(): int 
    {
        // Crear instancia de FeedIo manualmente
        $client = new Client(new GuzzleClient());
        $feedIo = new FeedIo($client);
        $count = 0;
        
        foreach ($this->feeds() as $source => $url) {
            try {
                $result = $feedIo->read($url);
                
                foreach ($result->getFeed() as $item) {
                    $title = trim($item->getTitle() ?? '');
                    $link  = (string) $item->getLink();
                    
                    if (!$title || !$link) continue;

                    $exists = Post::where('source_url',$link)->exists();
                    if ($exists) continue;

                    // Extraer imagen si existe
                    $imageUrl = null;
                    $media = $item->getValue('media:content');
                    if ($media && $media->getAttribute('url')) {
                        $imageUrl = $media->getAttribute('url');
                    }

                    Post::create([
                        'title'      => $title,
                        'type'       => 'news',
                        'status'     => 'draft',
                        'source'     => $source,
                        'source_url' => $link,
                        'excerpt'    => Str::limit(strip_tags((string)$item->getSummary()), 300),
                        'image_url'  => $imageUrl,
                        'tags'       => ['movilidad'],
                        'fetched_at' => now(),
                        'canonical_url' => $link,
                    ]);
                    $count++;
                }
            } catch (\Exception $e) {
                // Log error but continue with other feeds
                \Log::error("Error reading RSS feed {$source}: " . $e->getMessage());
                continue;
            }
        }
        
        return $count;
    }
}
