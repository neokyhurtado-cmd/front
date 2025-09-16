<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MobilityNewsController;
use Illuminate\Support\Facades\Cache;

class MobilityRefreshImages extends Command
{
    protected $signature = 'mobility:refresh-images {--limit=100}';
    protected $description = 'Extrae y cachea imágenes de noticias en background';

    public function handle()
    {
        $limit = (int) $this->option('limit') ?: 100;
        $this->info("Running mobility:refresh-images (limit={$limit})");

        // Fetch and cache (fetchAndCache runs image extraction because we are in console)
        $items = MobilityNewsController::fetchAndCache();

        $count = count($items);
        $images = array_values(array_filter(array_map(fn($it) => $it['image'] ?? null, $items)));
        $imagesCount = count($images);

        $this->info("Fetched {$count} items; images resolved: {$imagesCount}");

        // Optional: show top 5 with images
        $show = array_slice($images, 0, 5);
        if (!empty($show)) {
            $this->line("Sample images:");
            foreach ($show as $s) {
                $this->line(" - {$s}");
            }
        }

        return 0;
    }
}
