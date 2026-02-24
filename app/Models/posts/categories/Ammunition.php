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
        'p_1',
        'p_2',
        'p_3',
        'p_4',
        'p_5',
        'p_6',
        'p_7',
        'p_8',
        'p_9',
        'p_10',
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
