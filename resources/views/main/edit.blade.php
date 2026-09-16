<x-layout>
    <div class="section-head">
        <p class="eyebrow">Archive</p>
        <h2>Edit &mdash; {{ $item->name }}</h2>
    </div>

    <form method="POST" action="{{ route('main.update', $item) }}" class="mt-8 flex max-w-2xl flex-col gap-6">
        @csrf
        @method('PUT')

        <div class="flex flex-col gap-2">
            <label for="name" class="text-[0.7rem] uppercase tracking-[0.16em] text-muted">Name</label>
            <input id="name" name="name" type="text" required
                   value="{{ old('name', $item->name) }}"
                   class="border border-hairline bg-surface px-4 py-3 text-ink focus:border-brass focus:outline-none">
            @error('name')<p class="text-sm text-red-800">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-2">
            <label for="rate" class="text-[0.7rem] uppercase tracking-[0.16em] text-muted">Rating (0&ndash;100)</label>
            <input id="rate" name="rate" type="number" min="0" max="100" required
                   value="{{ old('rate', $item->rate) }}"
                   class="border border-hairline bg-surface px-4 py-3 text-ink focus:border-brass focus:outline-none">
            @error('rate')<p class="text-sm text-red-800">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-2">
            <label for="caption" class="text-[0.7rem] uppercase tracking-[0.16em] text-muted">Caption</label>
            <textarea id="caption" name="caption" rows="5" required
                      class="border border-hairline bg-surface px-4 py-3 text-ink focus:border-brass focus:outline-none">{{ old('caption', $item->caption) }}</textarea>
            @error('caption')<p class="text-sm text-red-800">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <button type="submit" class="btn">Save changes</button>
            <a href="{{ route('main.show', $item) }}" class="btn btn-espresso">Cancel</a>
        </div>
    </form>
</x-layout>
