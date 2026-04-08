<?php

namespace App\Http\Controllers;

use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        return view('check.checkout', [
            'amount' => $request->amount
        ]);
    }

    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'delivery_method' => 'required',
        ]);

       
        return redirect()->route('bank');
        // return back()->with('success','Payment Successful');
    }


}


