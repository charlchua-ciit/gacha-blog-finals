<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PostGameTag extends Pivot
{
    protected $table = 'post_game_tags';
    public $timestamps = true;
    protected $fillable = ['post_id', 'tag_id'];

    public function post() {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function gameTag() {
        return $this->belongsTo(GameTag::class, 'tag_id');
    }
}

