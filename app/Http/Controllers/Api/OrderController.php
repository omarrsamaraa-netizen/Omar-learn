<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Admins see every order; customers see only their own.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = Order::query()
            ->unless($request->user()->isAdmin(), fn ($query) => $query->whereBelongsTo($request->user()))
            ->with('orderItems')
            ->latest()
            ->paginate(15);

        return OrderResource::collection($orders);
    }

    /**
     * Turn the user's cart into an order and empty the cart.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $cart = CartController::cartFor($request);
        $cartItems = $cart->cartItems()->with('item')->get();

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty.',
            ]);
        }

        $order = DB::transaction(function () use ($validated, $user, $cart, $cartItems): Order {
            $order = Order::create([
                'user_id' => $user->id,
                'customer_name' => $validated['customer_name'] ?? $user->name,
                'customer_email' => $validated['customer_email'] ?? $user->email,
                'note' => $validated['note'] ?? null,
            ]);

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

        return (new OrderResource($order->load('orderItems')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Order $order): OrderResource
    {
        abort_unless($request->user()->isAdmin() || $order->user_id === $request->user()->id, 404);

        return new OrderResource($order->load('orderItems'));
    }
}
