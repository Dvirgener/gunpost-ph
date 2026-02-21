<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home.index');
})->name('home');

Route::middleware(['auth','verified'])->group(function () {

    Route::livewire('dashboard', 'pages::admin.dashboard.index')->name('dashboard');

});

require __DIR__.'/settings.php';
