<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class post_attachment extends Model
{
    /** @use HasFactory<\Database\Factories\PostAttachmentFactory> */
    use HasFactory;
    protected $fillable = [
        "storage_path"
    ];

    public function post(){
        return $this->belongsTo(Post::class);
    }
}
