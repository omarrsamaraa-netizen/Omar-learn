<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return $this->cartResponse(self::cartFor($request));
    }

    public function store(Request $request, Item $item): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'between:1,99'],
        ]);

        $cart = self::cartFor($request);
        $cart->addItem($item, $validated['quantity'] ?? 1);

        return $this->cartResponse($cart);
    }

    public function update(Request $request, Item $item): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'between:1,99'],
        ]);

        $cart = self::cartFor($request);
        $cart->cartItems()
            ->where('item_id', $item->id)
            ->firstOrFail()
            ->update(['quantity' => $validated['quantity']]);

        return $this->cartResponse($cart);
    }

    public function destroy(Request $request, Item $item): JsonResponse
    {
        $cart = self::cartFor($request);
        $cart->cartItems()->where('item_id', $item->id)->delete();

        return $this->cartResponse($cart);
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = self::cartFor($request);
        $cart->cartItems()->delete();

        return $this->cartResponse($cart);
    }

    /**
     * API carts belong to the token's user rather than to the browser session.
     */
    public static function cartFor(Request $request): Cart
    {
        return $request->user()->cart()->firstOrCreate();
    }

    private function cartResponse(Cart $cart): JsonResponse
    {
        return (new CartResource($cart->load('cartItems.item')))->response()->setStatusCode(200);
    }
}
