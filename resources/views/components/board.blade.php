@props(['board', 'hands', 'names'])

@php
    /** @var \App\Models\Board $board */
    /** @var array<\App\Models\Hand> $hands */
    /** @var array $names */
@endphp

<div class="flex gap-10 justify-between">
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

    <div class="self-end pb-4">
        @foreach($hands as $id => $hand)
            <div class="text-sm text-gray-700 font-semibold flex justify-between gap-4">
                <div>
                    {{ $names[$id] }}:
                </div>

                <div>
                    {{ $hand->count() }}
                </div>
            </div>
        @endforeach
    </div>
</div>
