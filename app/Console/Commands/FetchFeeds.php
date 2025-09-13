<?php

namespace App\Console\Commands;

use App\Services\RssIngest;
use App\Models\Post;
use App\Jobs\RewritePostJob;
use Illuminate\Console\Command;

class FetchFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feeds:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lee RSS y crea borradores; encola IA para reescritura';

    /**
     * Execute the console command.
     */
    public function handle(RssIngest $rss)
    {
        $this->info('Iniciando lectura de feeds RSS...');
        
        $n = $rss->run();
        $this->info("Nuevos borradores creados: $n");
        
        // encolar IA para todos los que no tienen body
        $pendingPosts = Post::where('status','draft')->pendingIA()->take(20);
        $count = 0;
        
        foreach ($pendingPosts->get() as $post) {
            RewritePostJob::dispatch($post->id);
            $count++;
        }
        
        $this->info("Jobs de IA encolados: $count");
        
        return self::SUCCESS;
    }
}
