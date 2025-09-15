<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MobilityNewsController;
use App\Models\NewsItem;
use Illuminate\Support\Facades\Log;

class MobilityRefresh extends Command
{
    protected $signature = 'mobility:refresh';
    protected $description = 'Refresh mobility feed, persist news_items and cache results';

    public function handle()
    {
        $this->info('Refreshing mobility feed...');

        try {
            $items = MobilityNewsController::fetchAndCache();

            foreach ($items as $it) {
                $domain = null;
                if (!empty($it['href'])) {
                    try { $domain = parse_url($it['href'], PHP_URL_HOST); } catch (\Throwable $__e) { $domain = null; }
                }

                NewsItem::updateOrCreate(
                    ['href' => $it['href']],
                    [
                        'title' => $it['title'] ?? '',
                        'tag' => $it['tag'] ?? null,
                        'image' => $it['image'] ?? null,
                        'domain' => $domain,
                        'published_at' => $it['publishedAt'] ?? null,
                        'fetched_at' => now(),
                    ]
                );
            }

            $this->info('Mobility feed refreshed: ' . count($items) . ' items');
            return 0;
        } catch (\Throwable $e) {
            Log::error('mobility:refresh failed: ' . $e->getMessage());
            $this->error('Failed: ' . $e->getMessage());
            return 1;
        }
    }
}
