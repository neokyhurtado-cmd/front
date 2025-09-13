<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class RotateMonthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:rotate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archiva noticias publicadas hace > 60 días (no evergreen)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = now()->subDays(60);
        
        $n = Post::where('status','published')
            ->where('type','news')
            ->where('evergreen',false)
            ->where('published_at','<',$limit)
            ->update(['status'=>'archived']);
            
        $this->info("Posts archivados: $n");
        
        // También mostrar estadísticas
        $stats = [
            'total' => Post::count(),
            'published' => Post::where('status','published')->count(),
            'draft' => Post::where('status','draft')->count(),
            'scheduled' => Post::where('status','scheduled')->count(),
            'archived' => Post::where('status','archived')->count(),
        ];
        
        $this->table(['Estado', 'Cantidad'], [
            ['Total', $stats['total']],
            ['Publicados', $stats['published']],
            ['Borradores', $stats['draft']],
            ['Programados', $stats['scheduled']],
            ['Archivados', $stats['archived']],
        ]);
        
        return self::SUCCESS;
    }
}
