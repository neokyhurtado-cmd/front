<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;

Route::get('/', [App\Http\Controllers\HomeController::class,'index'])->name('home');

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

// Rutas eliminadas: /test y /portal ya no están disponibles
// Las vistas home-msn y portal fueron removidas en limpieza

// Proxy de imágenes con caché
Route::get('/img-proxy', [\App\Http\Controllers\ImgProxy::class, 'show'])->name('img.proxy');

// Rutas admin comentadas - vistas eliminadas en limpieza
// Route::view('/admin','admin.dashboard')->name('admin.dashboard');
// Route::view('/login','auth.login')->name('login');

// Ruta de salud para verificar servidor
Route::get('/healthz', fn() => 'ok');
