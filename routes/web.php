<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;

Route::get('/', function (Illuminate\Http\Request $r) {
    // DEBUG: Sin base de datos por ahora
    return view('home-debug', ['posts' => collect(), 'pinned' => collect()]);
})->name('home');

Route::get('/blog', function () {
    $posts = Post::published()->orderByDesc('publish_at')->paginate(12);
    return view('blog.index', compact('posts'));
})->name('blog.index');

Route::get('/blog/{post:slug}', function (Post $post) {
    abort_unless($post->status === 'published', 404);
    
    // SEO meta (SEOTools)
    SEOMeta::setTitle($post->meta_title ?: $post->title);
    SEOMeta::setDescription($post->meta_description ?: Str::limit(strip_tags($post->excerpt ?? ''),160));
    SEOMeta::setCanonical($post->canonical_url ?: url()->current());
    OpenGraph::setTitle($post->title)->setDescription($post->meta_description)->setUrl(url()->current());
    
    if ($post->image_url) {
        OpenGraph::addImage($post->image_url);
    }
    
    return view('blog.show', compact('post'));
})->name('posts.show'); // Cambiar a posts.show para consistencia

Route::get('/test', function () {
    try {
        $posts = Post::published()->take(10)->get();
        $tags = ['movilidad','tránsito','señalización','Bogotá','TransMilenio','seguridad vial'];
        $kpis = [
            ['label' => 'Posts', 'value' => Post::count(), 'color' => '#8B5CF6'],
            ['label' => 'Publicados', 'value' => Post::published()->count(), 'color' => '#00E5FF'],
            ['label' => 'Estado', 'value' => 'OK', 'color' => '#39FF14'],
        ];
        
        return view('home-msn', [
            'hero' => $posts->first(),
            'pinned' => collect(),
            'grid' => $posts->skip(1),
            'kpis' => $kpis,
            'tags' => $tags,
            'q' => ''
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/portal', function () {
    $latest = Post::published()->orderByDesc('publish_at')->limit(6)->get();
    $kpis = [
        ['label'=>'Total posts', 'val'=>Post::count(), 'color'=>'#8B5CF6'],
        ['label'=>'Automatización', 'val'=>'RSS + IA', 'color'=>'#00E5FF'],
        ['label'=>'Actualización', 'val'=>'24/7', 'color'=>'#39FF14'],
    ];
    return view('portal', compact('latest','kpis'));
});

// Proxy de imágenes con caché
Route::get('/img-proxy', [\App\Http\Controllers\ImgProxy::class, 'show'])->name('img.proxy');

// Ruta de salud para verificar servidor
Route::get('/healthz', fn() => 'ok');
