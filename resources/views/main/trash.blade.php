<x-layout>
    <div class="section-head">
        <a href="{{ route('main.index') }}" class="nav-link">Back to collection</a>

        <p class="eyebrow mt-4">The collection</p>
        <h2>Trash</h2>
        <p class="lead">Pieces removed from the collection.</p>
    </div>

    <ul class="card-grid">
        @foreach ($category as $item)
            <li>
                <x-card href="/main/{{ $item->id }}" :highlight="$item['rate'] > 70">
                    <div>
                        <h3>{{ $item->name }}</h3>
                        <p>{{ $item->category->name }}</p>
                    </div>
                </x-card>
            </li>
        @endforeach
    </ul>

    {{ $category->links() }}
</x-layout>
