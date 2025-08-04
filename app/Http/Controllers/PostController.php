<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\User;




class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'title'=>['required','max:255',],
            'description'=>['required','max:255'],
            'price'=>['required','numeric','min:0'],
             'category'=>['required','in:pizza,burger,salad,drinks'],
            'image'=>['file', 'max:3000','mimes:jpeg,jpg,png,avif','nullable'],
        ]);
        $path=null;
        if($request->hasFile('image')){
          $path=Storage::disk('public')->put('out_imges',$request->file('image'));
        }
         
        Auth::user()->Post()->create([
           'title'=>$request->title,
           'description'=>$request->description,
           'price'=>$request->price,
           'category'=>$request->category,
           'image'=>$path,

        ]);
        return back()->with('succes','post created successfully.', 'post');

    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('delete','Post has been removed');
    }
}
