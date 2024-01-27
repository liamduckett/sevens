@php
    /** @var array<\App\Models\Hand> $hands  */
    /** @var int $currentTurnPlayerId  */
    /** @var \App\Models\Board $board  */
    /** @var ?int $winner */
    /** @var array $players */
    /** @var string $code */
@endphp

<div class="max-w-5xl mx-auto flex flex-col gap-10 py-10">
    <x-board :board="$board" :hands="$hands" :names="$players"/>

    @php
        $name = Session::get('playerId');
        $hand = $this->currentTurnPlayerHand();
        $isCurrentPlayer = $this->isCurrentTurnPlayer();
        $notCurrentPlayer = ! $isCurrentPlayer;
    @endphp

    @if($winner !== null)
        <div class="text-green-700 font-semibold text-3xl text-center">
            {{ $players[$winner] === $name ? 'You have' : "$players[$winner] has" }} won!
        </div>
    @endif

    <div class="p-8">
        <div class="pb-4 flex justify-center items-center gap-10">
            <div class="text-lg font-bold text-gray-700">
                @if($this->hasWinner())
                    Game Over
                @else
                    {{ $isCurrentPlayer ? 'Your' : "$players[$currentTurnPlayerId]'s" }} turn
                @endif
            </div>

            <x-button wire:click.throttle.1s="knock"
                      :disabled="$this->board->handIsPlayable($hand) || $this->hasWinner() || $notCurrentPlayer">
                Knock
            </x-button>
        </div>

        <ul class="flex flex-wrap gap-3 rounded-xl justify-center">
            @foreach($hand->cards as $card)
                <x-card :card="$card"
                        :playable="$this->board->cardIsPlayable($card) && $this->hasNoWinner() && $isCurrentPlayer"/>
            @endforeach
        </ul>
    </div>
</div>
