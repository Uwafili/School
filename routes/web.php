
<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GoogleAuthController;

Route::middleware('auth')->group(function(){
    Route::post('/navigation-role', [AuthController::class, 'switchNavigationRole'])->name('navigation.role');
    Route::post('/location', [\App\Http\Controllers\LocationController::class, 'update'])->name('location.update');
    Route::post('/orders/{order}/rating', [\App\Http\Controllers\StoreRatingController::class, 'store'])->name('order.rating');
    Route::post('/store/{store}/location', [\App\Http\Controllers\LocationController::class, 'store'])->name('store.location');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/google/logout', [GoogleAuthController::class, 'logout'])->name('google.logout');
    Route::view('/about', 'posts.about')->name('about');
    
    // Only one dashboard route for users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/live-riders', [DashboardController::class, 'liveRiderLocations'])->name('dashboard.live-riders');
    Route::get('/profile/edit', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::get('/orders/{order}/chat/{user}', [MessageController::class, 'chat'])->name('chat.show');
    Route::post('/orders/{order}/chat', [MessageController::class, 'send'])->name('chat.send');
    
    // Store Routes
    Route::get('/store-create', [StoreController::class, 'create'])->name('store.create');
    Route::get('/Shop', [StoreController::class, 'displayStore'])->name('Shop');
    Route::get('/store', [StoreController::class, 'displayStore'])->name('store.info');
    Route::get('/store/{store}', [StoreController::class, 'show'])->name('store.show');
    Route::post('/store', [StoreController::class, 'store'])->name('store');
    Route::get('/storedashboard', [StoreController::class,'Storedashboard'])->name('storedashboard');
    
    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/pay', [PaymentController::class, 'pay'])->name('payment.pay');

    Route::get('/bank', [PaymentController::class, 'bank'])->name('bank');
    Route::post('/bank/confirm', [PaymentController::class, 'confirmBankTransfer'])->name('bank.confirm');


Route::any('/pizza', [FoodController::class, 'pizza'])->name('food.pizza');
Route::any('/salad', [FoodController::class, 'salad'])->name('food.salad');
Route::any('/burger', [FoodController::class, 'burger'])->name('food.burger'); 
Route::any('/drinks', [FoodController::class, 'drinks'])->name('food.drinks');

Route::get('/food/view/{post}', [FoodController::class, 'view'])->name('food.view');


Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('add.cart');
Route::view('/cart','food.cart')->name('cart');




Route::post('/cart/increase/{id}', [CartController::class, 'increase'])->name('cart.increase');
Route::post('/cart/decrease/{id}', [CartController::class, 'decrease'])->name('cart.decrease');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');



Route::get('/rider', [RiderController::class, 'create'])->name('rider.create');
Route::post('/rider', [RiderController::class, 'store'])->name('rider.store');
Route::get('/rider/dashboard', [RiderController::class, 'dashboard'])->name('rider.dashboard');
Route::get('/rider/notifications', [RiderController::class, 'notifications'])->name('rider.notifications');
Route::post('/rider/notification/{notificationId}/read', [RiderController::class, 'markNotificationAsRead'])->name('notification.read');
Route::get('/rider/assigned-orders', [RiderController::class, 'assignedOrders'])->name('rider.assigned-orders');
Route::post('/rider/order/{orderId}/accept', [RiderController::class, 'acceptOrder'])->name('order.accept');
Route::post('/rider/order/{orderId}/bid', [RiderController::class, 'placeBid'])->name('order.bid');
Route::post('/rider/order/{orderId}/reject', [RiderController::class, 'rejectOrder'])->name('order.reject');
Route::post('/rider/availability', [RiderController::class, 'toggleAvailability'])->name('rider.availability');
Route::post('/rider/order/{orderId}/pickup', [RiderController::class, 'confirmPickup'])->name('rider.pickup');
Route::post('/rider/order/{orderId}/deliver', [RiderController::class, 'confirmDelivery'])->name('rider.deliver');
Route::get('/api/rider/unread-count', [RiderController::class, 'getUnreadCount'])->name('notification.unread-count');
    Route::get('/api/approval-status', [\App\Http\Controllers\ApprovalNotificationController::class, 'status'])->name('approval.status');


Route::post('/order/create', [StoreController::class, 'createOrder'])->name('order.create');
Route::post('/order/assign', [StoreController::class, 'assignRider'])->name('order.assign');
Route::post('/order/bid/{bidId}/accept', [StoreController::class, 'acceptBid'])->name('order.bid.accept');
Route::get('/order/{orderId}/view', [StoreController::class, 'viewOrder'])->name('order.view');
Route::post('/order/{orderId}/complete', [StoreController::class, 'completeOrder'])->name('order.complete');
Route::post('/order/{orderId}/cancel', [StoreController::class, 'cancelOrder'])->name('order.cancel');

});
Route::view('/', 'posts.index')->name('home');

Route::middleware('guest')->group(function(){
    Route::view('/register', 'Auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::view('/login', 'Auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    
    Route::get('/auth/{provider}/redirect', [GoogleAuthController::class, 'redirect'])->whereIn('provider', ['google', 'facebook'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [GoogleAuthController::class, 'callback'])->whereIn('provider', ['google', 'facebook'])->name('social.callback');
    Route::get('/auth/google/redirect', fn () => redirect()->route('social.redirect', 'google'))->name('google.redirect');
    Route::get('/auth/facebook/redirect', fn () => redirect()->route('social.redirect', 'facebook'))->name('facebook.redirect');
});

Route::middleware(['auth', 'admin'])->group(function(){
    Route::get('admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::POST('admin/Post', [PostController::class, 'store'])->name('posts.store');
    Route::get('/admin/Post/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/admin/Post/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/admin/Post/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    Route::get('/manage', [AuthController::class, 'manageUsers'])->name('manage.index');
    Route::delete('/admin/manage/{user}', [AuthController::class, 'destroy'])->name('user.destroy');
    
    
    Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');
    Route::get('/riders',[AuthController::class,'showrider'])->name('riders');
    Route::get('/riders/{rider}/edit', [AdminController::class, 'editRider'])->name('riders.edit');
    Route::put('/riders/{rider}', [AdminController::class, 'updateRider'])->name('riders.update');

     Route::get('/viewdetail/{id}',[AuthController::class,'viewdetail'])->name('viewdetail');

     Route::post('/Approve/Riders/{id}',[AuthController::class,'Approve'])->name('Approve');

     Route::post('/Reject/Riders/{id}',[AuthController::class,'Reject'])->name('Reject');

     // Store Approval Routes
     Route::get('/storeapprove', [AdminController::class, 'storeApprove'])->name('storeapprove');
     Route::post('/approve/store/{store}', [AdminController::class, 'approveStore'])->name('store.approve');
     Route::post('/reject/store/{store}', [AdminController::class, 'rejectStore'])->name('store.reject');
    Route::get('/storeapprove/{store}/edit', [AdminController::class, 'editStore'])->name('stores.edit');
    Route::put('/storeapprove/{store}', [AdminController::class, 'updateStore'])->name('stores.update');

     //  riders route
    


    










});





Route::get('/cookie-test', function () {
    $response = response('cookie test')
        ->withCookie(cookie(
            'test_cookie',
            '123',
            60,
            '/',
            null,
            true,
            true,
            false,
            'lax'
        ));

    dd($response->headers->all());

    return $response;
});


