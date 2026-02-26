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

    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
