<?php

use App\Models\posts\categories\Accessory;
use App\Models\posts\Post;
use App\Models\user\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('accessory create page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('posts.create.category.accessory'));

    $response->assertOk();
});

test('accessory can be created', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('posts.store'), [
        'category' => 'accessory',
        'listing_type' => 'sell',
        'title' => 'Test Accessory',
        'description' => 'Test description for accessory',
        'price' => 100.00,
        'is_negotiable' => true,
        'condition' => 'new',
        'location' => 'Test City',
        'accessory_category' => 'optic',
        'brand' => 'Test Brand',
        'model' => 'Test Model',
        'compatible_with' => 'AR-15',
        'mount_type' => 'picatinny',
        'size' => '1 inch',
        'color' => 'Black',
        'material' => 'Aluminum',
        'sku' => 'TEST123',
        'upc' => '123456789012',
        'package_includes' => 'Accessory and manual',
        'condition' => 'new',
        'notes' => 'Test notes',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'category' => 'accessory',
        'title' => 'Test Accessory',
    ]);

    $post = Post::where('title', 'Test Accessory')->first();
    $this->assertDatabaseHas('accessories', [
        'post_id' => $post->id,
        'category' => 'optic',
        'brand' => 'Test Brand',
        'model' => 'Test Model',
    ]);
});

test('accessory view page can be rendered', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category' => 'accessory',
    ]);
    $accessory = Accessory::factory()->create(['post_id' => $post->id]);

    $response = $this->actingAs($user)->get(route('posts.view.category.index', [$post, 'accessory']));

    $response->assertOk();
});

test('accessory create page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('posts.create.category.accessory'));

    $response->assertOk();
});

test('accessory can be created', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('posts.store'), [
        'category' => 'accessory',
        'listing_type' => 'sell',
        'title' => 'Test Accessory',
        'description' => 'Test description for accessory',
        'price' => 100.00,
        'is_negotiable' => true,
        'condition' => 'new',
        'location' => 'Test City',
        'accessory_category' => 'optic',
        'brand' => 'Test Brand',
        'model' => 'Test Model',
        'compatible_with' => 'AR-15',
        'mount_type' => 'picatinny',
        'size' => '1 inch',
        'color' => 'Black',
        'material' => 'Aluminum',
        'sku' => 'TEST123',
        'upc' => '123456789012',
        'package_includes' => 'Accessory and manual',
        'condition' => 'new',
        'notes' => 'Test notes',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'category' => 'accessory',
        'title' => 'Test Accessory',
    ]);

    $post = Post::where('title', 'Test Accessory')->first();
    $this->assertDatabaseHas('accessories', [
        'post_id' => $post->id,
        'category' => 'optic',
        'brand' => 'Test Brand',
        'model' => 'Test Model',
    ]);
});

test('accessory view page can be rendered', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category' => 'accessory',
    ]);
    $accessory = Accessory::factory()->create(['post_id' => $post->id]);

    $response = $this->actingAs($user)->get(route('posts.view.category.index', [$post, 'accessory']));

    $response->assertOk();
});
