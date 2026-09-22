<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public const SESSION_KEY = 'cart_id';

    public function index(): View
    {
        $cart = $this->currentCart();

        return view('cart.index', [
            'cartItems' => $cart?->cartItems()->with('item.category')->get() ?? collect(),
        ]);
    }

    public function store(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'between:1,99'],
        ]);

        $this->currentCart(create: true)->addItem($item, $validated['quantity'] ?? 1);

        return redirect()
            ->route('cart.index')
            ->with('status', $item->name.' added to your cart.');
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'between:1,99'],
        ]);

        $this->currentCart()
            ?->cartItems()
            ->where('item_id', $item->id)
            ->update(['quantity' => $validated['quantity']]);

        return redirect()
            ->route('cart.index')
            ->with('status', 'Cart updated.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $this->currentCart()
            ?->cartItems()
            ->where('item_id', $item->id)
            ->delete();

        return redirect()
            ->route('cart.index')
            ->with('status', $item->name.' removed from your cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->currentCart()?->cartItems()->delete();

        return redirect()
            ->route('cart.index')
            ->with('status', 'Your cart is empty.');
    }

    public static function currentCart(bool $create = false): ?Cart
    {
        $cartId = session(self::SESSION_KEY);
        $cart = $cartId ? Cart::find($cartId) : null;

        if (! $cart && $create) {
            $cart = Cart::create(['user_id' => auth()->id()]);
            session([self::SESSION_KEY => $cart->id]);
        }

        return $cart;
    }
}
