<?php

namespace App\Models\posts\categories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id',
        'category',
        'brand',
        'model',
        'compatible_with',
        'mount_type',
        'size',
        'color',
        'material',
        'sku',
        'upc',
        'package_includes',
        'condition',
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

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
