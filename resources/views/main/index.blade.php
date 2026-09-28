<x-layout>
    <div class="section-head flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow">The collection</p>
            <h2>Available now</h2>
            <p class="lead">Be a gentleman.</p>
        </div>

        @can('manage-collection')
            <a href="{{ route('main.trash') }}" class="btn btn-espresso">Trash</a>
        @endcan
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
