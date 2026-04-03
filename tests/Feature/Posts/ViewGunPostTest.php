<?php

use App\Models\posts\categories\Airsoft;
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

    $post->gun()->update([
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
        ->assertSee('P599.99')
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

    $post->gun()->update([
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

    $post->gun()->update([]);

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

    $post->gun()->update([]);

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

    $post->gun()->update([]);

    $this->actingAs($user)
        ->get(route('posts.view.category.index', ['post' => $post, 'category' => 'gun']))
        ->assertStatus(200)
        ->assertSee('P100.00')
        ->assertSee('P250.00');
});

test('handles posts with minimal data', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category' => 'gun',
        'title' => 'Minimal Post',
        'price' => 100.00,
    ]);

    $post->gun()->update([]);

    $this->actingAs($user)
        ->get(route('posts.view.category.index', ['post' => $post, 'category' => 'gun']))
        ->assertStatus(200)
        ->assertSee('Minimal Post');
});

it('allows an authenticated user to view an airsoft post', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category' => 'airsoft',
        'listing_type' => 'sell',
        'title' => 'JAG AEG',
        'description' => 'Well maintained airsoft rifle',
        'price' => 350.00,
        'p_1' => 'photos/airsoft1.jpg',
    ]);

    $post->airsoft()->update([
        'brand' => 'G&G',
        'model' => 'GC16',
        'platform' => 'rifle',
        'power_source' => 'aeg',
        'fps' => 340,
    ]);

    $this->actingAs($user)
        ->get(route('posts.view.category.index', ['post' => $post, 'category' => 'airsoft']))
        ->assertStatus(200)
        ->assertSee('JAG AEG')
        ->assertSee('G&G')
        ->assertSee('340');
});

it('shows only non-blank fields in airsoft specifications', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category' => 'airsoft',
        'title' => 'Minimal Airsoft',
        'p_1' => 'photos/airsoft-minimal.jpg',
    ]);

    $post->airsoft()->update([
        'brand' => 'Tokyo Marui',
        'model' => null,
        'fps' => 320,
    ]);

    $this->actingAs($user)
        ->get(route('posts.view.category.index', ['post' => $post, 'category' => 'airsoft']))
        ->assertStatus(200)
        ->assertSee('Tokyo Marui')
        ->assertSee('320')
        ->assertDontSee('Model:');
});

it('airsoft factory includes all migration columns as attributes', function () {
    $airsoft = Airsoft::factory()->make();
    $expected = [
        'post_id', 'brand', 'model', 'series', 'platform', 'power_source', 'compatibility_platform', 'gearbox_version',
        'fps', 'joule', 'color', 'body_material', 'metal_body', 'blowback', 'battery_type', 'battery_connector',
        'gas_type', 'includes_magazines', 'magazine_count', 'magazine_type', 'package_includes', 'condition', 'notes',
    ];

    foreach ($expected as $attr) {
        expect(array_key_exists($attr, $airsoft->getAttributes()))->toBeTrue();
    }
});

it('gun factory includes all migration columns as attributes', function () {
    $gun = Gun::factory()->make();
    $expected = [
        'post_id', 'manufacturer', 'model', 'variant', 'series', 'country_of_origin',
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

it('accessory factory includes all migration columns as attributes', function () {
    $accessory = Accessory::factory()->make();
    $expected = [
        'post_id', 'category', 'brand', 'model', 'compatible_with', 'mount_type', 'size', 'color', 'material',
        'sku', 'upc', 'package_includes', 'condition', 'notes',
    ];

    foreach ($expected as $attr) {
        expect(array_key_exists($attr, $accessory->getAttributes()))->toBeTrue();
    }
});

it('ammunition factory includes all migration columns as attributes', function () {
    $ammunition = Ammunition::factory()->make();
    $expected = [
        'post_id', 'brand', 'product_line', 'caliber', 'bullet_type', 'grain', 'case_material', 'primer_type',
        'corrosive', 'total_rounds', 'boxes', 'rounds_per_box', 'lot_number', 'sku', 'upc', 'condition', 'reloads', 'notes',
    ];

    foreach ($expected as $attr) {
        expect(array_key_exists($attr, $ammunition->getAttributes()))->toBeTrue();
    }
});

it('other factory includes all migration columns as attributes', function () {
    $other = Other::factory()->make();
    $expected = [
        'post_id', 'weapon_type', 'subcategory', 'intended_use', 'brand', 'model', 'variant', 'country_of_origin',
        'blade_type', 'edge_type', 'steel_type', 'finish', 'full_tang', 'overall_length', 'blade_length', 'head_length',
        'handle_length', 'length_unit', 'weight', 'weight_unit', 'handle_material', 'handle_color', 'grip_texture',
        'is_folding', 'opening_mechanism', 'lock_type', 'includes_sheath', 'sheath_type', 'carry_type', 'condition',
        'has_box', 'has_receipt', 'package_includes', 'notes',
    ];

    foreach ($expected as $attr) {
        expect(array_key_exists($attr, $other->getAttributes()))->toBeTrue();
    }
});

it('post factory includes all migration columns as attributes', function () {
    $post = Post::factory()->make();

    $expected = [
        'uuid', 'user_id', 'category', 'listing_type', 'title', 'slug', 'description',
        'price', 'buy_min_price', 'buy_max_price', 'is_negotiable', 'condition', 'location',
        'status', 'approved_at', 'approved_by', 'rejection_reason', 'is_featured', 'views',
        'expires_at', 'p_1', 'p_2', 'p_3', 'p_4', 'p_5', 'p_6', 'p_7', 'p_8', 'p_9', 'p_10',
    ];

    foreach ($expected as $attr) {
        expect(array_key_exists($attr, $post->getAttributes()))->toBeTrue();
    }
});
