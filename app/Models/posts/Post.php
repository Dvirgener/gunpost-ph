<?php

namespace App\Models\posts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'category',
        'listing_type',
        'title',
        'slug',
        'description',
        'price',
        'buy_min_price',
        'buy_max_price',
        'is_negotiable',
        'condition',
        'location',
        'status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'is_featured',
        'views',
        'expires_at',
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
        'price' => 'decimal:2',
        'is_negotiable' => 'boolean',
        'is_featured' => 'boolean',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\user\User::class);
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\user\User::class, 'approved_by');
    }

    public function gun()
    {
        return $this->hasOne(\App\Models\posts\categories\Gun::class);
    }

    public function ammunition()
    {
        return $this->hasOne(\App\Models\posts\categories\Ammunition::class);
    }

    public function airsoft()
    {
        return $this->hasOne(\App\Models\posts\categories\Airsoft::class);
    }

    public function accessory()
    {
        return $this->hasOne(\App\Models\posts\categories\Accessory::class);
    }

    public function other()
    {
        return $this->hasOne(\App\Models\posts\categories\Other::class);
    }

    /**
     * Auto UUID + Slug
     */
    protected static function booted(): void
    {
        static::creating(function (Post $post) {

            if (empty($post->uuid)) {
                $post->uuid = (string) Str::uuid();
            }

            if (empty($post->slug) && ! empty($post->title)) {
                $post->slug = static::generateUniqueSlug($post->title);
            }
        });

        // Optional: update slug if title changes (remove if you want stable slugs)
        static::updating(function (Post $post) {
            if ($post->isDirty('title')) {
                $post->slug = static::generateUniqueSlug($post->title, $post->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (
            static::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Optional: Route model binding by UUID (nice for /posts/{post:uuid})
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function conversations()
    {
        return $this->hasMany(\App\Models\Conversation::class);
    }
}
