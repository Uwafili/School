<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Rider;




class RiderController extends Controller
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
            'name' => ['required','max:255'],
            'email' => ['required','email','max:225'],
            'phone'=>['required','max:15','regex:/^\+?[0-9]{1,4}?[-. ]?([0-9]{1,3}[-. ]?){1,4}[0-9]{1,4}$/'],
            'license' => ['required','max:255',],
            'vehicle_number'=>['required','max:15','regex:/^\+?[0-9]{1,4}?[-. ]?([0-9]{1,3}[-. ]?){1,4}[0-9]{1,4}$/'],
            'vehicle'=>['required','max:255'],
            'image'=>['file','nullable','mimes:jpg,png,jpeg,avif','max:3000']
        ]);
        $path=null;
        if($request->hasFile('image')){
            $path=Storage::disk('public')->put('post_image', $request->file('image'));
        }
        $riders=Rider::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'license'=>$request->license,
            'vehicle_number'=>$request->vehicle_number,
            'vehicle'=>$request->vehicle,
            'image'=>$path, 
            "user_id"=>Auth::id()
            
        ]);
        return back()->with('success','Riders information has been submitted',['riders'=>$riders]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rider $rider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rider $rider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rider $rider)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rider $rider)
    {
        //
    }
}
