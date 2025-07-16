<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GameTag extends Model
{
    use HasFactory;
    
    protected $fillable = ['tag_name'];

    public function posts() {
        return $this->belongsToMany(Post::class, 'post_game_tag', 'tag_id', 'post_id');
    }
}

