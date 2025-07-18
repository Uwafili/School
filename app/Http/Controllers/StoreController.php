<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Http\Requests\UpdateStoreRequest;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Storage as Stroage;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;



class StoreController extends Controller
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
        $store=$request->validate([
             'stores'=>['required', 'max:255'],
             'owner'=>['required', 'max:255'],
             'email'=>['required','max:255', 'email'],
             'phone'=>['required', 'max:15', 'regex:/^\+?[0-9]{1,4}?[-. ]?([0-9]{1,3}[-. ]?){1,4}[0-9]{1,4}$/'],
             'address'=>['required', 'max:255'],
             'image'=>['file', 'nullable', 'mimes:jpg,png,jpeg,avif','max:3000'],
        ]);
        $path=null;
        if($request->hasFile('image')){
            $path=Stroage::disk('public')->put('post_image', $request->file('image'));
        }
        $store=Store::create([
            'stores'=> $request->stores,
            'owner' => $request->owner,
            'email' => $request->email,
            'phone' => $request->phone,
            'address'=> $request->address,
            'image'=> $path
        ]);
        if ($store) {
            return redirect()->route('home')->with('success', 'Store created successfully.');
        } else {
            return redirect()->back()->with('failed', 'Failed to create store.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        //
    }
}
