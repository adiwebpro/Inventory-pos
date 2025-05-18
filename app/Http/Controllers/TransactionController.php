<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Cart;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'buyer') {
            return auth()->user()->transactions()->with('items.product')->get();
        }
        
        return Transaction::with('user', 'items.product')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $user = auth()->user();
        $carts = $user->carts()->with('product')->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }

        foreach ($carts as $cart) {
            if ($cart->product->stock < $cart->quantity) {
                return response()->json([
                    'message' => "Product {$cart->product->name} doesn't have enough stock"
                ], 400);
            }
        }

        $total = $carts->sum(function ($cart) {
            return $cart->product->price * $cart->quantity;
        });

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'total_amount' => $total,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
        ]);

        foreach ($carts as $cart) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'price' => $cart->product->price,
            ]);

            $cart->product->decrement('stock', $cart->quantity);
        }

        $user->carts()->delete();

        return response()->json($transaction->load('items.product'), 201);
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        return $transaction->load('user', 'items.product');
    }

    public function processPayment(Request $request, Transaction $transaction)
    {
        if (!in_array(auth()->user()->role, ['cashier', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $transaction->update(['status' => 'completed']);

        return response()->json([
            'message' => 'Payment processed successfully',
            'transaction' => $transaction->load('user', 'items.product'),
        ]);
    }
}