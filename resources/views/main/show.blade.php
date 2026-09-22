<x-layout>
    <div class="section-head">
        <p class="eyebrow">The collection</p>
        <h2>{{ $item->name }}</h2>
    </div>

    <p class="lead">{{ $item->caption }}</p>

    <p class="mt-4 text-[0.72rem] uppercase tracking-[0.16em] text-brass">Rating &mdash; {{ $item->rate }} / 100</p>

    <form method="POST" action="{{ route('cart.store', $item) }}" class="mt-8 flex flex-wrap items-end gap-4">
        @csrf

        <div class="field">
            <label for="quantity" class="field-label">Quantity</label>
            <input type="number" id="quantity" name="quantity" value="1" min="1" max="99" class="input w-24" required>
        </div>

        <button type="submit" class="btn">Add to cart</button>
        @error('quantity')<p class="field-error">{{ $message }}</p>@enderror
    </form>

    <div class="mt-8 flex flex-wrap items-center gap-4">
        <a href="{{ route('main.edit', $item) }}" class="btn">Edit piece</a>
 
        <form method="POST" action="{{ route('main.destroy', $item) }}"
              onsubmit="return confirm('Remove this piece from the collection?');">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-espresso">Remove piece</button>
        </form>

        <a href="{{ route('main.index') }}" class="nav-link">Back to collection</a>


    </div>

    <div class="border-2 border-dashed bg-white px-4 pb-4 my-4 rounded">

        <h3>Category Info</h3>
        <p><strong>Category name:</strong> {{ $item->category->name }}</p> 
        <p><strong>Description:</strong> {{ $item->category->description}}</p> 
        <p><strong>About the category :</strong></p>
        <p>{{ $item->category->image }}</p>
    </div>
</x-layout>
