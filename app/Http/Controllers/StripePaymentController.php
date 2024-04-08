<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Stripe;
use App\Models\Payment;     

class StripePaymentController extends Controller
{
    public function stripe()
    {
        return view('stripe');
    }
    
    /**
     * Handle stripe payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    
        try {
            // Amount in USD
            $amount = 200; // $200 USD
    
            $charge = Stripe\Charge::create([
                "amount" => $amount * 100, // Convert amount to cents
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Test payment gateway from stripe " 
            ]);

            // Save payment information to database
            Payment::create([
                'amount' => $amount,
                'currency' => $charge->currency,
                'transaction_id' => $charge->id,
                'description' => $charge->description,
                // Add more fields as needed
            ]);
      
            Session::flash('success', 'Payment successful!');

            return back();
        } catch (\Exception $e) {
            // Handle payment failure
            Session::flash('error', $e->getMessage());
            return back();
        }
    }
}
