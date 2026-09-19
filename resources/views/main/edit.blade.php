<x-layout>
    <div class="section-head">
        <p class="eyebrow">Archive</p>
        <h2>Edit &mdash; {{ $item->name }}</h2>
    </div>

    <form method="POST" action="{{ route('main.update', $item) }}" class="form">
        @csrf
        @method('PUT')

        {{-- item name --}}
        <div class="field">
            <label for="name" class="field-label">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}" class="input" required>
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        {{-- item rating --}}
        <div class="field">
            <label for="rate" class="field-label">Rating (0&ndash;100)</label>
            <input type="number" id="rate" name="rate" min="0" max="100" value="{{ old('rate', $item->rate) }}" class="input" required>
            @error('rate')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        {{-- item category --}}
        <div class="field">
            <label for="category_id" class="field-label">Category</label>
            <select id="category_id" name="category_id" class="input" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $item->category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        {{-- item caption --}}
        <div class="field">
            <label for="caption" class="field-label">Caption</label>
            <textarea id="caption" name="caption" rows="5" class="input" required>{{ old('caption', $item->caption) }}</textarea>
            @error('caption')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Save changes</button>
            <a href="{{ route('main.show', $item) }}" class="btn btn-espresso">Cancel</a>
        </div>
    </form>
</x-layout>
