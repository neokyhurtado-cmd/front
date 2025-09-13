<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use Illuminate\Support\Str;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;

Route::get('/', function(){
    $posts = Post::published()->orderByDesc('published_at')->paginate(10);
    return view('blog.index', compact('posts'));
})->name('home');

Route::get('/blog/{slug}', function($slug){
    $post = Post::where('slug',$slug)->where('status','published')->firstOrFail();
    
    // SEO meta (SEOTools)
    SEOMeta::setTitle($post->meta_title ?: $post->title);
    SEOMeta::setDescription($post->meta_description ?: Str::limit(strip_tags($post->excerpt ?? ''),160));
    SEOMeta::setCanonical($post->canonical_url ?: url()->current());
    OpenGraph::setTitle($post->title)->setDescription($post->meta_description)->setUrl(url()->current());
    
    if ($post->image_url) {
        OpenGraph::addImage($post->image_url);
    }
    
    return view('blog.show', compact('post'));
})->name('post.show');
