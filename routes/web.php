<?php

use App\Support\Portfolio;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Portfolio $portfolio) {
    return view('welcome', [
        'p' => $portfolio,
        'story' => config('story'),
        'archive' => $portfolio->archive(),
    ]);
});
