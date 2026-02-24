<?php

namespace App\Models\posts\categories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gun extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id',
        'manufacturer',
        'model',
        'variant',
        'series',
        'country_of_origin',
        'platform',
        'type',
        'action',
        'caliber',
        'capacity',
        'barrel_length',
        'overall_length',
        'height',
        'width',
        'weight',
        'weight_unit',
        'frame_material',
        'slide_material',
        'barrel_material',
        'finish',
        'color',
        'grip_type',
        'stock_type',
        'handguard_type',
        'rail_type',
        'sight_type',
        'optic_ready',
        'optic_mount_pattern',
        'threaded_barrel',
        'thread_pitch',
        'muzzle_device_included',
        'muzzle_device_type',
        'trigger_type',
        'trigger_pull',
        'trigger_pull_unit',
        'has_manual_safety',
        'has_firing_pin_safety',
        'sku',
        'upc',
        'condition',
        'round_count_estimate',
        'has_box',
        'has_receipt',
        'has_documents',
        'document_notes',
        'included_magazines',
        'included_accessories',
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
        'barrel_length' => 'decimal:2',
        'overall_length' => 'decimal:2',
        'height' => 'decimal:2',
        'width' => 'decimal:2',
        'weight' => 'decimal:3',
        'optic_ready' => 'boolean',
        'threaded_barrel' => 'boolean',
        'muzzle_device_included' => 'boolean',
        'trigger_pull' => 'decimal:2',
        'has_manual_safety' => 'boolean',
        'has_firing_pin_safety' => 'boolean',
        'has_box' => 'boolean',
        'has_receipt' => 'boolean',
        'has_documents' => 'boolean',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
