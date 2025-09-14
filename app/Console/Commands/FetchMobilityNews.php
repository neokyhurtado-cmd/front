<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\Post;

class FetchMobilityNews extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Trae noticias de movilidad y crea borradores editables';

    public function handle(): int
    {
        $feeds = [
            'https://news.google.com/rss/search?q=movilidad+Bogotá+OR+tránsito+OR+TransMilenio+OR+señalización&hl=es-419&gl=CO&ceid=CO:es-419',
        ];

        $created = 0;

        foreach ($feeds as $url) {
            $xml = Http::timeout(30)->get($url)->body();
            if (! $xml) continue;

            $feed = @simplexml_load_string($xml);
            if (! $feed || ! isset($feed->channel->item)) continue;

            foreach ($feed->channel->item as $item) {
                $title = (string)$item->title;
                $link  = (string)$item->link;
                $guid  = (string)($item->guid ?? $link);

                if (Post::where('external_id', $guid)->orWhere('title',$title)->exists()) continue;

                $excerpt = Str::limit(strip_tags((string)$item->description), 220);
                $slug = Str::slug(Str::limit($title, 60, ''));

                Post::create([
                    'external_id' => $guid,
                    'title' => $title,
                    'slug'  => $slug,
                    'excerpt' => $excerpt,
                    'body' => "<p class='text-sm text-gray-500'>Fuente original: <a href=\"{$link}\" target=\"_blank\" rel=\"noopener\">{$link}</a></p>",
                    'status' => 'draft',
                    'source' => 'Google News',
                    'source_url' => $link,
                    'fetched_at' => now(),
                    'meta_title' => Str::limit($title, 60, ''),
                    'meta_description' => $excerpt,
                ]);

                $created++;
            }
        }

        $this->info("Borradores creados: {$created}");
        return Command::SUCCESS;
    }
}
