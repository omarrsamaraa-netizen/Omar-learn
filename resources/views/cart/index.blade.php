<x-layout>
    <div class="section-head flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('main.index') }}" class="nav-link">Back to collection</a>

            <p class="eyebrow mt-4">Your selection</p>
            <h2>Cart</h2>
            <p class="lead">Pieces you are about to order.</p>
        </div>

        @if ($cartItems->isNotEmpty())
            <form method="POST" action="{{ route('cart.clear') }}"
                  onsubmit="return confirm('Empty your cart?');">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-espresso">Empty cart</button>
            </form>
        @endif 
    </div> 

    @if ($cartItems->isEmpty())
        <p class="lead mt-8">Your cart is empty.</p>

        <a href="{{ route('main.index') }}" class="btn mt-6">Browse the collection</a>
    @else
        <ul class="mt-8 flex flex-col gap-4">
            @foreach ($cartItems as $cartItem)
                <li class="card flex-row flex-wrap items-center justify-between gap-6">
                    <div>
                        <h3><a href="{{ route('main.show', $cartItem->item) }}">{{ $cartItem->item->name }}</a></h3>
                        <p>{{ $cartItem->item->category->name }}</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <form method="POST" action="{{ route('cart.update', $cartItem->item) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')

                            <label for="quantity-{{ $cartItem->item->id }}" class="field-label">Qty</label>
                            <input type="number" id="quantity-{{ $cartItem->item->id }}" name="quantity"
                                   value="{{ $cartItem->quantity }}" min="1" max="99" class="input w-20" required>

                            <button type="submit" class="btn">Update</button>
                        </form>

                        <form method="POST" action="{{ route('cart.destroy', $cartItem->item) }}">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-espresso">Remove</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="section-head mt-16">
            <p class="eyebrow">Checkout</p>
            <h2>Submit your order</h2>
        </div>

        <form method="POST" action="{{ route('orders.store') }}" class="form">
            @csrf

            <div class="field">
                <label for="customer_name" class="field-label">Your name</label>
                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" class="input" required>
                @error('customer_name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label for="customer_email" class="field-label">Email</label>
                <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" class="input" required>
                @error('customer_email')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label for="note" class="field-label">Note (optional)</label>
                <textarea id="note" name="note" rows="4" class="input">{{ old('note') }}</textarea>
                @error('note')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Submit order</button>
            </div>
        </form>
    @endif
</x-layout>
