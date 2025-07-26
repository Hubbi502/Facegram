<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    /** @use HasFactory<\Database\Factories\FollowFactory> */
    use HasFactory;

    protected $fillable = [
        "follower_id", "following_id", "is_accepted"
    ];
    public function Follower(){
        return $this->belongsTo(User::class);
    }
    public function Following(){
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'is_accepted'=>"boolean"
        ];
    }
}
