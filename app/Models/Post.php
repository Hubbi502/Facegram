<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'caption'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function post_attachment(){
        return $this->hasMany(post_attachment::class);
    }
}
