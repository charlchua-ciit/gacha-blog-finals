<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PostGameTag extends Pivot
{
    protected $table = 'post_game_tag';
    public $timestamps = false;

    protected $fillable = ['post_id', 'tag_id'];
}

