<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishDue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publica posts con publish_at <= now';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $due = Post::where('status','scheduled')->where('publish_at','<=',$now)->get();
        
        if ($due->isEmpty()) {
            $this->info('No hay posts programados para publicar');
            return self::SUCCESS;
        }

        foreach ($due as $p) {
            $p->update(['status'=>'published','published_at'=>$now]);
            $this->info("Publicado: {$p->title}");
            // TODO: Notificar redes/newsletter si quieres
        }

        $this->info("Total publicados: " . $due->count());
        return self::SUCCESS;
    }
}
