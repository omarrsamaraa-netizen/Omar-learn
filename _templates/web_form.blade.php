<x-layout>
    <h2>Add an item</h2>

    <form method="POST" action="{{ route('main.store') }}">
        @csrf

        <!-- item name -->
        <label for="name">Name:</label>
        <input
          type="text"
          id="name"
          name="name"
          value="{{ old('name') }}"
          required
        >

        <!-- item rating -->
        <label for="rate">Rating (0-100):</label>
        <input
          type="number"
          id="rate"
          name="rate"
          min="0"
          max="100"
          value="{{ old('rate') }}"
          required
        >

        <!-- item caption -->
        <label for="caption">Caption:</label>
        <textarea
          rows="5"
          id="caption"
          name="caption"
          required
        >{{ old('caption') }}</textarea>

        <!-- select a category -->
        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id" required>
            <option value="" disabled selected>Select a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <button type="submit">Add item</button>
    </form>
</x-layout>
