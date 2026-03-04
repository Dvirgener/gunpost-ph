<?php

use App\Models\posts\categories\Gun;
use App\Models\posts\Post;
use App\Models\user\User;

test('allows an authenticated user to view a gun post', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category' => 'gun',
        'listing_type' => 'sell',
        'title' => 'Glock 19 Gen 5',
        'description' => 'A reliable 9mm handgun',
        'price' => 599.99,
        'p_1' => 'photos/gun1.jpg',
        'p_2' => 'photos/gun2.jpg',
        'p_3' => 'photos/gun3.jpg',
    ]);

    Gun::factory()->create([
        'post_id' => $post->id,
        'manufacturer' => 'Glock',
        'model' => 'Glock 19',
        'platform' => 'handgun',
        'caliber' => '9mm',
        'capacity' => 15,
    ]);

    $this->actingAs($user)
        ->get(route('posts.view.category.index', ['post' => $post, 'category' => 'gun']))
        ->assertStatus(200)
        ->assertSee('Glock 19 Gen 5')
        ->assertSee('$599.99')
        ->assertSee('Glock')
        ->assertSee('9mm');
});

test('shows only non-blank fields in gun specifications', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category' => 'gun',
        'title' => 'Test Gun',
        'p_1' => 'photos/test.jpg',
    ]);

    Gun::factory()->create([
        'post_id' => $post->id,
        'manufacturer' => 'Test Manufacturer',
        'model' => null, // This should not appear
        'caliber' => '45 ACP',
    ]);

    $this->actingAs($user)
        ->get(route('posts.view.category.index', ['post' => $post, 'category' => 'gun']))
        ->assertStatus(200)
        ->assertSee('Test Manufacturer')
        ->assertSee('45 ACP')
        ->assertDontSee('Model:'); // Model field should not be rendered
});

test('displays main photo and thumbnails correctly', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category' => 'gun',
        'title' => 'Photo Test',
        'p_1' => 'photos/main.jpg',
        'p_2' => 'photos/thumb2.jpg',
        'p_3' => 'photos/thumb3.jpg',
        'p_4' => null, // Should not display
    ]);

    Gun::factory()->create(['post_id' => $post->id]);

    $this->actingAs($user)
        ->get(route('posts.view.category.index', ['post' => $post, 'category' => 'gun']))
        ->assertStatus(200)
        ->assertSeeHtml('photos/main.jpg')
        ->assertSeeHtml('photos/thumb2.jpg')
        ->assertSeeHtml('photos/thumb3.jpg');
});

test('displays seller information correctly', function () {
    $user = User::factory()->create([
        'first_name' => 'John',
        'email' => 'john@example.com',
    ]);
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category' => 'gun',
        'title' => 'Seller Test',
        'p_1' => 'photos/test.jpg',
    ]);

    Gun::factory()->create(['post_id' => $post->id]);

    $this->actingAs($user)
        ->get(route('posts.view.category.index', ['post' => $post, 'category' => 'gun']))
        ->assertStatus(200)
        ->assertSee('John')
        ->assertSee('john@example.com');
});

test('properly displays buy listing price range', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category' => 'gun',
        'listing_type' => 'buy',
        'buy_min_price' => 100.00,
        'buy_max_price' => 250.00,
    ]);

    Gun::factory()->create(['post_id' => $post->id]);

    $this->actingAs($user)
        ->get(route('posts.view.category.index', ['post' => $post, 'category' => 'gun']))
        ->assertStatus(200)
        ->assertSee('$100.00 - $250.00');
});

test('handles posts with minimal data', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category' => 'gun',
        'title' => 'Minimal Post',
        'price' => 100.00,
    ]);

    Gun::factory()->create(['post_id' => $post->id]);

    $this->actingAs($user)
        ->get(route('posts.view.category.index', ['post' => $post, 'category' => 'gun']))
        ->assertStatus(200)
        ->assertSee('Minimal Post');
});

it('gun factory includes all migration columns as attributes', function () {
    $gun = Gun::factory()->make();
    $expected = [
        'manufacturer', 'model', 'variant', 'series', 'country_of_origin',
        'platform', 'type', 'action',
        'caliber', 'capacity', 'barrel_length', 'overall_length', 'height', 'width', 'weight', 'weight_unit',
        'frame_material', 'slide_material', 'barrel_material', 'finish', 'color', 'grip_type',
        'stock_type', 'handguard_type', 'rail_type',
        'sight_type', 'optic_ready', 'optic_mount_pattern',
        'threaded_barrel', 'thread_pitch', 'muzzle_device_included', 'muzzle_device_type',
        'trigger_type', 'trigger_pull', 'trigger_pull_unit', 'has_manual_safety', 'has_firing_pin_safety',
        'sku', 'upc',
        'condition', 'round_count_estimate', 'has_box', 'has_receipt', 'has_documents', 'document_notes',
        'included_magazines', 'included_accessories', 'notes',
    ];

    foreach ($expected as $attr) {
        expect(array_key_exists($attr, $gun->getAttributes()))->toBeTrue();
    }
});
