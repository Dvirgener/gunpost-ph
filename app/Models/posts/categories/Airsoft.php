<?php

namespace App\Models\posts\categories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airsoft extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id',
        'brand',
        'model',
        'series',
        'platform',
        'power_source',
        'compatibility_platform',
        'gearbox_version',
        'fps',
        'joule',
        'color',
        'body_material',
        'metal_body',
        'blowback',
        'battery_type',
        'battery_connector',
        'gas_type',
        'includes_magazines',
        'magazine_count',
        'magazine_type',
        'package_includes',
        'condition',
        'notes',
    ];

    protected $casts = [
        'joule' => 'decimal:2',
        'metal_body' => 'boolean',
        'blowback' => 'boolean',
        'includes_magazines' => 'boolean',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
