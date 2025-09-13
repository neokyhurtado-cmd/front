<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ScheduleDailyPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:schedule-daily {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Programa hasta 4 posts para hoy (08:00, 12:00, 16:00, 20:00)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : now();
        $slots = [8,12,16,20]; // horas locales
        $today = $date->copy()->startOfDay();

        $scheduled = 0;

        foreach ($slots as $h) {
            $slotAt = $today->copy()->setTime($h,0);
            $already = Post::where('publish_at',$slotAt)->exists();
            
            if ($already) {
                $this->info("Slot {$h}:00 ya ocupado");
                continue;
            }

            $candidate = Post::where('status','draft')
                ->whereNotNull('body')
                ->orderByDesc('fetched_at')
                ->first();

            if ($candidate) {
                $candidate->update(['status'=>'scheduled','publish_at'=>$slotAt]);
                $this->info("Programado: {$candidate->title} @ {$slotAt->toDateTimeString()}");
                $scheduled++;
            } else {
                $this->warn("No hay candidatos para el slot {$h}:00");
            }
        }

        $this->info("Total programados: $scheduled");
        return self::SUCCESS;
    }
}
