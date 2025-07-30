<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'content'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function gameTags() {
        return $this->belongsToMany(GameTag::class, 'post_game_tags', 'post_id', 'tag_id');
    }

    public function scopeWithRelations($query) {
        return $query->with(['user', 'gameTags', 'comments.user', 'likes']);
    }

    public function getRouteKeyName() {
        return 'id';
    }
}

