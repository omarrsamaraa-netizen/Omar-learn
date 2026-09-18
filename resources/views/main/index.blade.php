<x-layout>
    <div class="section-head">
        <p class="eyebrow">The collection</p>
        <h2>Available now</h2>
        <p class="lead">Be a gentleman.</p>
    </div>

    <ul class="card-grid">
        @foreach ($category as $item)
            <li>
                <x-card href="/main/{{ $item->id }}" :highlight="$item['rate'] > 70">
                    <h3>{{ $item->name }}</h3>
                </x-card>
            </li>
        @endforeach
    </ul>

    {{ $category->links() }}
</x-layout>
