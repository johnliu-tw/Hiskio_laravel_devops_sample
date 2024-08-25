<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Post;

Schedule::call(function () {
    Post::where('is_published', false)
        ->where('published_at', '<=', now())
        ->update(['is_published' => true]);
    var_dump('schedule call finished');
})->everyMinute();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
