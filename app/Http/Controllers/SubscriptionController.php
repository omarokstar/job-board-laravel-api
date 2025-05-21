<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
        ]);

        $user = $request->user();

        try {
            $user->newSubscription('default', 'price_1RQvoyEI022bdeAWIhhn63xC') 
                 ->create($request->payment_method);

            $user->is_premium = true;
            $user->save();

            return response()->json(['message' => 'Subscription successful']);
        } catch (IncompletePayment $e) {
            return response()->json([
                'redirect' => route('cashier.payment', [$e->payment->id, 'redirect' => '/'])
            ], 402);
        }
    }

    public function status(Request $request)
    {
        return response()->json([
            'is_premium' => $request->user()->subscribed('default'),
        ]);
    }
}
