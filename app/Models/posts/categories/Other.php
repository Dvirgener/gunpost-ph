<?php

namespace App\Models\posts\categories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Other extends Model
{
    use HasFactory;
    protected $table = 'others';

    protected $fillable = [
        'post_id',
        'weapon_type',
        'subcategory',
        'intended_use',
        'brand',
        'model',
        'variant',
        'country_of_origin',
        'blade_type',
        'edge_type',
        'steel_type',
        'finish',
        'full_tang',
        'overall_length',
        'blade_length',
        'head_length',
        'handle_length',
        'length_unit',
        'weight',
        'weight_unit',
        'handle_material',
        'handle_color',
        'grip_texture',
        'is_folding',
        'opening_mechanism',
        'lock_type',
        'includes_sheath',
        'sheath_type',
        'carry_type',
        'condition',
        'has_box',
        'has_receipt',
        'package_includes',
        'notes',
    ];

    protected $casts = [
        'full_tang' => 'boolean',
        'overall_length' => 'decimal:2',
        'blade_length' => 'decimal:2',
        'head_length' => 'decimal:2',
        'handle_length' => 'decimal:2',
        'weight' => 'decimal:3',
        'is_folding' => 'boolean',
        'includes_sheath' => 'boolean',
        'has_box' => 'boolean',
        'has_receipt' => 'boolean',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
