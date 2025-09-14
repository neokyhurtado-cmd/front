<?php
require_once 'bootstrap/app.php';

use App\Models\Post;

// Activar los 3 posts más recientes como destacados
$posts = Post::latest()->take(3)->get();

foreach($posts as $post) {
    $post->update([
        'pinned' => true,
        'pinned_until' => now()->addDays(30)
    ]);
}

echo "Posts destacados activados: " . $posts->count() . "\n";
echo "Total posts pinned: " . Post::where('pinned', true)->count() . "\n";