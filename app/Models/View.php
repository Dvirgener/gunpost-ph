<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\posts\Post;

class View extends Model
{
    protected $fillable = ['post_id', 'viewer_ip', 'viewer_user_agent'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
