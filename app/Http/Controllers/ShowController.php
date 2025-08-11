<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;


class ShowController extends Controller
{
    public function view($id){
    $post = Post::findOrFail($id);
    return redirect()->route('food.view', ['id' => $post->id]);

}
}
