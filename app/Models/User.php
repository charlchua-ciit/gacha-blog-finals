<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;

    protected $fillable = ['username', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];

    public function posts() {
        return $this->hasMany(Post::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    public function followers() {
        return $this->belongsToMany(User::class, 'follows', 'followee_id', 'follower_id');
    }

    public function following() {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followee_id');
    }

    public function getRouteKeyName() {
        return 'username';
    }
}
