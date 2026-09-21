<x-layout>
    <div class="section-head flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('main.index') }}" class="nav-link">Back to collection</a>

            <p class="eyebrow mt-4">The collection</p>
            <h2>Trash</h2>
            <p class="lead">Pieces removed from the collection.</p>
        </div>

        @if ($category->isNotEmpty())
            <form method="POST" action="{{ route('main.trash.empty') }}"
                  onsubmit="return confirm('Delete every piece in the trash permanently? This cannot be undone.');">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-espresso">Empty trash</button>
            </form>
        @endif
    </div>

    <ul class="card-grid">
        @foreach ($category as $item)
            <li>
                <x-card href="/main/{{ $item->id }}" :highlight="$item['rate'] > 70">
                    <div>
                        <h3>{{ $item->name }}</h3>
                        <p>{{ $item->category->name }}</p>
                    </div>

                    <form method="POST" action="{{ route('main.force-delete', $item) }}"
                          onsubmit="return confirm('Delete {{ $item->name }} permanently? This cannot be undone.');">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-espresso">Delete permanently</button>
                    </form>
                </x-card>
            </li>
        @endforeach
    </ul>

    {{ $category->links() }}
</x-layout>
