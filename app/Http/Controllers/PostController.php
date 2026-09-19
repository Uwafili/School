<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
  


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
         
          Post::create([
              'user_id' => Auth::id(),
           'title'=>$request->title,
           'description'=>$request->description,
           'price'=>$request->price,
           'category'=>$request->category,
           'image'=>$path,

        ]);
        return back()->with('success', 'Post created successfully.');

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
        Gate::authorize('modify', $post);

        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('modify', $post);

        $validated = $request->validate([
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'in:pizza,burger,salad,drinks'],
            'image' => ['file', 'max:3000', 'mimes:jpeg,jpg,png,avif', 'nullable'],
        ]);

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            $validated['image'] = Storage::disk('public')->put('out_imges', $request->file('image'));
        }

        $post->update($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('modify', $post);
 
        if($post->image){
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return back()->with('delete', 'Your post was deleted');
    }
}
