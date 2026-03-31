<?php

namespace App\Models\posts\categories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ammunition extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id',
        'brand',
        'product_line',
        'caliber',
        'bullet_type',
        'grain',
        'case_material',
        'primer_type',
        'corrosive',
        'total_rounds',
        'boxes',
        'rounds_per_box',
        'lot_number',
        'sku',
        'upc',
        'condition',
        'reloads',
        'notes',
    ];

    protected $casts = [
        'corrosive' => 'boolean',
        'reloads' => 'boolean',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
