@php
    /** @var array<\App\Models\Hand> $hands  */
    /** @var int $currentPlayer  */
@endphp

<div class="max-w-5xl mx-auto flex flex-col gap-20">
    @foreach($hands as $hand)
        <ul class="flex flex-wrap gap-4">
            @foreach($hand->cards as $card)
                @php
                    $color = match($card->suit->color()) {
                        'red' => 'text-red-500',
                        'black' => 'text-black-500',
                    }
                @endphp

                <div class="border-2 border-black rounded-3xl w-36 h-48 flex flex-col justify-between p-4 {{ $color }}">
                    <div class="text-left font-bold text-xl">
                        {{ $card->rank->symbol() }}
                    </div>

                    <div class="text-center font-bold text-5xl">
                        {{ $card->suit->symbol() }}
                    </div>

                    <div class="text-right font-bold text-xl">
                        {{ $card->rank->symbol() }}
                    </div>
                </div>
            @endforeach
        </ul>
    @endforeach
</div>
