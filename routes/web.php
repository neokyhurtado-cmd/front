<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

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
})->name('posts.show');

// Proxy de imágenes con caché
Route::get('/img-proxy', [\App\Http\Controllers\ImgProxy::class, 'show'])->name('img.proxy');

// Ruta de salud para verificar servidor
Route::get('/healthz', fn() => 'ok');

// Ruta de diagnóstico CSS
Route::get('/debug-css', function () {
    return view('debug-css');
});

// Ruta de prueba simple
Route::get('/test', function () {
    return 'ROUTE OK';
});

// Ruta de prueba Tailwind
Route::get('/test2', fn() => view('test2'));

// Dashboard para usuarios autenticados
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rutas de perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
