<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $cart = CartController::currentCart();
        $cartItems = $cart?->cartItems()->with('item')->get() ?? collect();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('status', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = DB::transaction(function () use ($validated, $cart, $cartItems): Order {
            $order = Order::create($validated);

            foreach ($cartItems as $cartItem) {
                $order->orderItems()->create([
                    'item_id' => $cartItem->item_id,
                    'name' => $cartItem->item->name,
                    'quantity' => $cartItem->quantity,
                ]);
            }

            $cart->cartItems()->delete();

            return $order;
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Thank you — your order has been submitted.');
    }

    public function show(Order $order): View
    {
        $order->load('orderItems');

        return view('orders.show', ['order' => $order]);
    }
}
