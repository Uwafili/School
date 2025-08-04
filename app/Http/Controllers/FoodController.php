<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class FoodController extends Controller
{
   public function show($type){
    $foods = [
        'pizza' => [
        [
            'name' => 'Margherita',
            'description' => 'Classic cheese & tomato',
            'amount' => 10.99,
            'image' => 'margherita.jpg'
        ],
        [
            'name' => 'Pepperoni',
            'description' => 'Pepperoni & cheese',
            'amount' => [amount],
            'image' => 'pepperoni.jpg'
        ],
    ],
        'burger' => [
            ['name' => 'Beef Burger', 'description' => 'Juicy beef patty with cheese'],
            ['name' => 'Chicken Burger', 'description' => 'Grilled chicken with lettuce'],
        ],
        'salad' => [
            ['name' => 'Caesar Salad', 'description' => 'Romaine, croutons, parmesan'],
            ['name' => 'Greek Salad', 'description' => 'Feta, olives, cucumber'],
        ],
    ];

    if (!isset($foods[$type])) {
        abort(404);
    }

    return view('food', [
        $type . 's' => $foods[$type], // e.g., 'pizzas' => [...]
    ]);
}
   
}
