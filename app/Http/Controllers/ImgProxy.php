<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImgProxy extends Controller 
{
    public function show(Request $r)
    {
        $url = $r->query('u');
        abort_unless($url && filter_var($url, FILTER_VALIDATE_URL), 404);

        $key = 'imgcache/'.md5($url).'.bin';
        if (!Storage::disk('public')->exists($key)) {
            $res = Http::timeout(8)->get($url);
            abort_unless($res->ok(), 404);
            Storage::disk('public')->put($key, $res->body());
        }
        
        $content = Storage::disk('public')->get($key);
        $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $content);
        
        return response($content, 200, [
            'Content-Type' => $mime ?: 'image/jpeg',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}