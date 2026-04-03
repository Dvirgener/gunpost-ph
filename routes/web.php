<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home.index');
})->name('home');

Route::livewire('posts', 'pages::posts.index')->name('posts');
Route::livewire('help', 'pages::help.index')->name('help');
Route::livewire('posts/{post}/view/{category}', 'pages::posts.view.index')->name('posts.view.category.index');

// These Routes are for Administrators use only.
Route::middleware(['auth', 'is_admin', 'verified'])->group(function () {

    Route::livewire('dashboard', 'pages::admin.dashboard.index')->name('dashboard');
    Route::livewire('dashboard/users', 'pages::admin.dashboard.users')->name('admin.users');
    Route::livewire('dashboard/posts', 'pages::admin.dashboard.posts')->name('admin.posts');
    Route::livewire('dashboard/orders', 'pages::admin.dashboard.orders.orders')->name('admin.orders');
    Route::livewire('dashboard/tickets', 'pages::admin.dashboard.tickets.tickets')->name('admin.tickets');

});

// These Routes are for any authenticated and verified user, but will check for post credits before allowing access to the create post page.

Route::middleware(['auth', 'verified'])->group(function () {

    Route::livewire('posts/create', 'pages::posts.create.index')->name('posts.create.index');

    Route::livewire('orders', 'pages::orders.index')->name('order');

    // routes for Gun CRUD operations. These will be used for creating, editing, and viewing gun posts. The {category} parameter will be used to determine which category of post is being created, edited, or viewed. The {post} parameter will be used to determine which post is being edited or viewed.
    Route::livewire('posts/create/gun', 'pages::posts.create.category.gun')->name('posts.create.category.gun');
    Route::livewire('posts/{post}/edit/gun', 'pages::posts.edit.category.gun')->name('posts.edit.category.gun');

    Route::livewire('posts/create/ammunition', 'pages::posts.create.category.ammunition')->name('posts.create.category.ammunition');
    Route::livewire('posts/{post}/edit/ammunition', 'pages::posts.edit.category.ammunition')->name('posts.edit.category.ammunition');

    Route::livewire('posts/create/airsoft', 'pages::posts.create.category.airsoft')->name('posts.create.category.airsoft');
    Route::livewire('posts/{post}/edit/airsoft', 'pages::posts.edit.category.airsoft')->name('posts.edit.category.airsoft');

    Route::livewire('posts/create/accessory', 'pages::posts.create.category.accessory')->name('posts.create.category.accessory');
    Route::livewire('posts/{post}/edit/accessory', 'pages::posts.edit.category.accessory')->name('posts.edit.category.accessory');

    Route::livewire('posts/create/others', 'pages::posts.create.category.others')->name('posts.create.category.others');
    Route::livewire('posts/{post}/edit/others', 'pages::posts.edit.category.others')->name('posts.edit.category.others');

    Route::livewire('posts/create/post/{category}', 'pages::posts.create.post')->name('posts.create');

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
