@props(['board'])

@php
    /** @var \App\Models\Board $board */
@endphp

<div class="flex gap-4 justify-center">
    @foreach($board->contents as $suit => $boardSuit)
        <div class="flex flex-col justify-center w-52">
            <div class="text-center text-2xl font-semibold pb-2 text-gray-700">
                {{ $suit }}
            </div>

            <div class="flex flex-col justify-center h-full items-center">
                @if($boardSuit->lowest !== null)
                    @php
                        $suit = \App\Enums\Suit::from($suit);
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
