<?php

namespace App\Models;

use Database\Factories\CartFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    /** @use HasFactory<CartFactory> */
    use HasFactory;

    protected $fillable = ['user_id'];

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Add a piece to the cart, stacking onto any quantity already there.
     */
    public function addItem(Item $item, int $quantity = 1): CartItem
    {
        $cartItem = $this->cartItems()->firstOrNew(['item_id' => $item->id]);
        $cartItem->quantity = min(($cartItem->quantity ?? 0) + $quantity, 99);
        $cartItem->save();

        return $cartItem;
    }

    /**
     * How many pieces the cart holds in total.
     */
    public function totalQuantity(): int
    {
        return (int) $this->cartItems()->sum('quantity');
    }
}
