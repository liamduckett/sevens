@php
    /** @var \App\Models\Deck $deck */
@endphp

<div>
    @foreach($deck->cards as $card)
        <div>{{ $card }}</div>
    @endforeach
</div>
