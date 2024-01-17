@php
    /** @var \App\Models\Deck $deck */
@endphp

<div>
    @foreach($deck->hands as $hand)
        <ul>
            @foreach($hand as $card)
                <li>
                    {{ $card }}
                </li>
            @endforeach
        </ul>
    @endforeach
</div>
