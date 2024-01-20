@php
    /** @var array<\App\Models\Hand> $hands  */
    /** @var int $currentPlayer  */
    /** @var \App\Models\Board $board  */
@endphp

<div class="max-w-5xl mx-auto flex flex-col gap-10 py-10">
    <div class="flex gap-4 justify-center">
        @foreach($board->contents as $name => $boardSuit)
            <div class="flex flex-col justify-center w-52">
                <div class="text-center text-2xl font-semibold pb-2">
                    {{ $name }}
                </div>

                <div class="flex flex-col justify-center h-full items-center">
                    @if($boardSuit->lowest !== null)
                        @php
                            $suit = \App\Enums\Suit::from($name);
                            $lowestRank = \App\Enums\Rank::from($boardSuit->lowest);
                            $highestRank = \App\Enums\Rank::from($boardSuit->highest);

                            $lowestCard = new \App\Models\Card($suit, $lowestRank);
                            $highestCard = new \App\Models\Card($suit, $highestRank);
                        @endphp

                        {{-- only show 2 cards, if they're not the same --}}
                        @if($lowestRank !== $highestRank)
                            <x-card :card="$highestCard"/>
                        @endif

                        <x-card :card="$lowestCard"/>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($this->winner)
        <div class="text-green-700 font-semibold text-3xl text-center">
            Player {{ $this->winner }} has won!
        </div>
    @endif

    <div class="p-8">
        @php
            $hand = $this->currentPlayerHand();
        @endphp

        @dump($this->board->handIsPlayable($hand))
        @dump($this->hasWinner())

        <div class="pb-4 flex justify-center">
            <x-button wire:click.throttle.500ms="knock"
                      :disabled="$this->board->handIsPlayable($hand) || $this->hasWinner()">
                Knock
            </x-button>
        </div>

        <ul class="flex flex-wrap gap-3 rounded-xl justify-center">
            @foreach($hand->cards as $card)
                <x-card :card="$card" :playable="$this->board->cardIsPlayable($card) && $this->hasNoWinner()"/>
            @endforeach
        </ul>
    </div>

{{--    @foreach($hands as $player => $hand)--}}
{{--        <ul class="flex flex-wrap gap-3 border-4 p-8 rounded-xl justify-center--}}
{{--            {{ $player === $currentPlayer ? 'border-blue-400' : 'border-transparent' }}">--}}

{{--            @foreach($hand->cards as $card)--}}
{{--                <x-card :card="$card" :playable="$player === $currentPlayer && $this->board->cardIsPlayable($card)"/>--}}
{{--            @endforeach--}}
{{--        </ul>--}}
{{--    @endforeach--}}
</div>
