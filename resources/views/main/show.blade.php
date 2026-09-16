<x-layout>
    <div class="section-head">
        <p class="eyebrow">The collection</p>
        <h2>{{ $item->name }}</h2>
    </div>

    <p class="lead">{{ $item->caption }}</p>

    <p class="mt-4 text-[0.72rem] uppercase tracking-[0.16em] text-brass">Rating &mdash; {{ $item->rate }} / 100</p>

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
</x-layout>
