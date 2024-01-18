@php
    /** @var array<\App\Models\Hand> $hands  */
@endphp

<div>
    @foreach($hands as $hand)
        <ul>
            @foreach($hand->cards as $card)
                <li>
                    {{ $card }}
                </li>
            @endforeach
        </ul>
    @endforeach
</div>
