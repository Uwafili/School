<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RiderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MessageController;




// Remove unused import

Route::middleware('auth')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::view('/about', 'posts.about')->name('about');

    // Only one dashboard route for users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::view('/Shop', 'enroll.Shop')->name('Shop');

    Route::post('/store', [StoreController::class, 'store'])->name('store');
    Route::get('/storedashboard', [StoreController::class,'Storedashboard'])->name('storedashboard');





Route::any('/pizza', [FoodController::class, 'pizza'])->name('food.pizza');
Route::any('/salad', [FoodController::class, 'salad'])->name('food.salad');
Route::any('/burger', [FoodController::class, 'burger'])->name('food.burger'); 
Route::any('/drinks', [FoodController::class, 'drinks'])->name('food.drinks');

Route::any('/food/view', [FoodController::class, 'view'])->name('food.view');


Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('add.cart');
Route::view('/cart','food.cart')->name('cart');




Route::post('/cart/increase/{id}', [CartController::class, 'increase'])->name('cart.increase');
Route::post('/cart/decrease/{id}', [CartController::class, 'decrease'])->name('cart.decrease');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
});

Route::view('/', 'posts.index')->name('home');

Route::middleware('guest')->group(function(){
    Route::view('/register', 'Auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::view('/login', 'Auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware(['auth', 'admin'])->group(function(){
    Route::get('admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::POST('admin/Post', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/admin/Post/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::view('/manage','admin.manage')->name('manage');
    Route::get('/manage', [AuthController::class, 'manageUsers'])->name('manage');
    Route::delete('/admin/manage/{user}', [AuthController::class, 'destroy'])->name('user.destroy');


    Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');
    Route::get('/riders',[AuthController::class,'showrider'])->name('riders');

     Route::get('/viewdetail/{id}',[AuthController::class,'viewdetail'])->name('viewdetail');

     Route::post('/Approve/Riders/{id}',[AuthController::class,'Approve'])->name('Approve');

     Route::post('/Reject/Riders/{id}',[AuthController::class,'Reject'])->name('Reject');


     

    //  riders route
    Route::view('/rider', 'enroll.rider')->name('rider.create');
    Route::post('/rider', [RiderController::class, 'store'])->name('rider.store');













    // Send message (admin or user)
Route::post('/send-message', [MessageController::class, 'send'])->middleware('auth');

// View chat (admin or user)
Route::get('/chat/{id}', [MessageController::class, 'chat'])->middleware('auth');


});