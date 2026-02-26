<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home.index');
})->name('home');

Route::livewire('help', 'pages::help.index')->name('help');

// These Routes are for Administrators use only.
Route::middleware(['auth', 'is_admin', 'verified'])->group(function () {

    Route::livewire('dashboard', 'pages::admin.dashboard.index')->name('dashboard');

});

// These Routes are for any authenticated and verified user, but will check for post credits before allowing access to the create post page.

Route::middleware(['auth', 'verified'])->group(function () {

    Route::livewire('posts', 'pages::posts.index')->name('posts');
    Route::livewire('posts/create', 'pages::posts.create.index')->name('posts.create.index');
    Route::livewire('posts/create/{category}', 'pages::posts.create.category.index')->name('posts.create.category.index');

});

// BELOW ARE EMAIL VERIFICATION ROUTES, DO NOT DELETE OR EDIT UNLESS YOU KNOW WHAT YOU ARE DOING. THESE ARE REQUIRED FOR THE EMAIL VERIFICATION SYSTEM TO WORK PROPERLY.

Route::get('/email/verify', function () {
    return view('pages::auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

require __DIR__.'/settings.php';
