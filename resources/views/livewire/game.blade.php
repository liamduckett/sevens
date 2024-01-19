@php
    /** @var array<\App\Models\Hand> $hands  */
    /** @var int $currentPlayer  */
@endphp

<div class="max-w-5xl mx-auto flex flex-col gap-20 py-10">
    @dump($board['diamonds'] ?? [])
    @dump($board['clubs'] ?? [])
    @dump($board['hearts'] ?? [])
    @dump($board['spades'] ?? [])

    @foreach($hands as $player => $hand)
        <ul class="flex flex-wrap gap-3 border-4 p-8 rounded-xl justify-center
            {{ $player === $currentPlayer ? 'border-blue-400' : 'border-transparent' }}">

            @foreach($hand->cards as $card)
                @php
                    $color = match($card->suit->color()) {
                        'red' => 'text-red-500',
                        'black' => 'text-black-500',
                    };

                    $playable = $player === $currentPlayer && $this->isPlayable($card);
                @endphp

                <div class="border-4 rounded-3xl p-2
                     {{ $playable ? 'border-green-500 cursor-pointer' : 'border-transparent'}}"
                     wire:click="playCard({{ json_encode($card->toDto()) }}, '{{ $playable }}')">

                    <div class="border-2 border-black rounded-3xl w-36 h-48 flex flex-col justify-between p-4
                         {{ $color }}">

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
                </div>
            @endforeach
        </ul>
    @endforeach
</div>
