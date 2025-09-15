<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\MobilityNewsController;

class AbsolutizeTest extends TestCase
{
    public function test_absolutize_makes_relative_paths_absolute()
    {
        $c = new MobilityNewsController();
        $base = 'https://example.com/path/page.html';
        $this->assertEquals('https://example.com/path/image.jpg', $c->absolutize($base, 'image.jpg'));
        $this->assertEquals('https://example.com/other.png', $c->absolutize($base, '/other.png'));
        $this->assertEquals('https://cdn.example.com/a.jpg', $c->absolutize($base, 'https://cdn.example.com/a.jpg'));
    }
}
