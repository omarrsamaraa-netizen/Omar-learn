<x-layout>
    <div class="section-head">
        <a href="{{ route('main.index') }}" class="nav-link">Back to collection</a>

        <p class="eyebrow mt-4">Order #{{ $order->id }}</p>
        <h2>Order confirmed</h2>
        <p class="lead">Placed {{ $order->created_at->format('j F Y') }} for {{ $order->customer_name }}.</p>
    </div>

    <ul class="mt-8 flex flex-col gap-4">
        @foreach ($order->orderItems as $orderItem)
            <li class="card flex-row flex-wrap items-center justify-between gap-6">
                <h3>{{ $orderItem->name }}</h3>
                <p>&times; {{ $orderItem->quantity }}</p>
            </li>
        @endforeach
    </ul>

    @if ($order->note)
        <div class="section-head mt-16">
            <p class="eyebrow">Your note</p>
        </div>

        <p class="lead mt-4">{{ $order->note }}</p>
    @endif
</x-layout>
