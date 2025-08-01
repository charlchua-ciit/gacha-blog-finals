<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Follow extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    protected $table = 'follows';
    protected $fillable = ['follower_id', 'followee_id'];

    public function follower() {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function followee() {
        return $this->belongsTo(User::class, 'followee_id');
    }

    /**
     * Check if a user is following another user
     */
    public static function isFollowing($followerId, $followeeId) {
        return static::where('follower_id', $followerId)
                    ->where('followee_id', $followeeId)
                    ->exists();
    }

    /**
     * Create a follow relationship if it doesn't exist
     */
    public static function createFollow($followerId, $followeeId) {
        return static::firstOrCreate([
            'follower_id' => $followerId,
            'followee_id' => $followeeId
        ]);
    }

    /**
     * Remove a follow relationship if it exists
     */
    public static function removeFollow($followerId, $followeeId) {
        return static::where('follower_id', $followerId)
                    ->where('followee_id', $followeeId)
                    ->delete();
    }
}
