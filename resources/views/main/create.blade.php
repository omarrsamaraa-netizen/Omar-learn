<x-layout>
    <div class="section-head">
        <p class="eyebrow">Archive</p>
        <h2>Add an item</h2>
    </div>

    <form  action="{{ route('main.store') }}" method="POST" class="form">
        @csrf

        {{-- item name --}}
        <div class="field">
            <label for="name" class="field-label">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" class="input" required>
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        {{-- item rating --}}
        <div class="field">
            <label for="rate" class="field-label">Rating (0&ndash;100)</label>
            <input type="number" id="rate" name="rate" min="0" max="100" value="{{ old('rate') }}" class="input" required>
            @error('rate')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        {{-- item category --}}
        <div class="field">
            <label for="category_id" class="field-label">Category</label>
            <select id="category_id" name="category_id" class="input" required>
                <option value="" disabled selected>Select a category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        {{-- item caption --}}
        <div class="field">
            <label for="caption" class="field-label">Caption</label>
            <textarea id="caption" name="caption" rows="5" class="input" required>{{ old('caption') }}</textarea>
            @error('caption')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Add item</button>
            <a href="{{ route('main.index') }}" class="btn btn-espresso">Cancel</a>
        </div>
    </form>
</x-layout>
