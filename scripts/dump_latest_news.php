<?php
require __DIR__ . '/../laravel-backend/vendor/autoload.php';
$app = require_once __DIR__ . '/../laravel-backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \App\Models\News::orderBy('published_at','desc')->take(10)->get(['id','title','url','description','published_at']);
foreach ($rows as $n) {
    $desc = $n->description ?? '';
    if (strlen($desc) > 80) $desc = substr($desc,0,77).'...';
    echo "{$n->id} | {$n->title} | ".($n->url?:'no-url')." | {$desc} | {$n->published_at}\n";
}
