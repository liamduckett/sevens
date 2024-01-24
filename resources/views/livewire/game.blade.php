@php
    /** @var array<\App\Models\Hand> $hands  */
    /** @var int $currentPlayerId  */
    /** @var \App\Models\Board $board  */
    /** @var ?int $winner */
    /** @var array $names */
@endphp

<div class="max-w-5xl mx-auto flex flex-col gap-10 py-10">
    <x-board :board="$board" :hands="$hands" :names="$names"/>

    @if($winner)
        <div class="text-green-700 font-semibold text-3xl text-center">
            {{ $names[$winner] }} has won!
        </div>
    @endif

    <div class="p-8">
        @php
            $hand = $this->currentPlayerHand();
            $isCurrentPlayer = $names[$currentPlayerId] === Session::get('playerId');
        @endphp

        <div class="pb-4 flex justify-center items-center gap-10">
            <div class="text-lg font-bold text-gray-700">
                {{ Session::get('playerId') }}

                it is {{ $names[$currentPlayerId] }}'s turn
            </div>

            <x-button wire:click.throttle.500ms="knock"
                      :disabled="$this->board->handIsPlayable($hand) || $this->hasWinner() || !$isCurrentPlayer">
                Knock
            </x-button>
        </div>

        <ul class="flex flex-wrap gap-3 rounded-xl justify-center">
            @foreach($hand->cards as $card)
                <x-card :card="$card" :playable="$this->board->cardIsPlayable($card) && $this->hasNoWinner() && $isCurrentPlayer"/>
            @endforeach
        </ul>
    </div>
</div>
