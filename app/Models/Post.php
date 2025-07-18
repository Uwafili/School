<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
     use HasFactory;
    protected $fillable=[
            'title',
            'description',
            'price',
            'image',
            'category',
    ];
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;
    public function user(){
        return $this->belongsTo(Post::class);
    }
}
