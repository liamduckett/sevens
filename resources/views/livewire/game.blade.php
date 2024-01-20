@php
    /** @var array<\App\Models\Hand> $hands  */
    /** @var int $currentPlayer  */
    /** @var \App\Models\Board $board  */
@endphp

<div class="max-w-5xl mx-auto flex flex-col gap-20 py-10">
    <div class="flex gap-4 justify-center">
        @foreach($board->contents as $name => $boardSuit)
            <div>
                <div>
                    {{ $name }}
                </div>

                <div class="flex flex-col justify-center">
                    <div>{{ $boardSuit->lowest ?? 'N/A' }}</div>
                    <div>{{ $boardSuit->highest ?? 'N/A' }}</div>
                </div>
            </div>
        @endforeach
    </div>

    @foreach($hands as $player => $hand)
        <ul class="flex flex-wrap gap-3 border-4 p-8 rounded-xl justify-center
            {{ $player === $currentPlayer ? 'border-blue-400' : 'border-transparent' }}">

            @foreach($hand->cards as $card)
                <x-card :card="$card" :playable="$player === $currentPlayer && $this->isPlayable($card)"/>
            @endforeach
        </ul>
    @endforeach
</div>
