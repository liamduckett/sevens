@props(['card', 'playable'])

@php
    /** @var \App\Models\Card $card */
    /** @var bool $playable */

    $color = match($card->suit->color()) {
        'red' => 'text-red-500',
        'black' => 'text-black-500',
    };
@endphp

<div class="border-4 rounded-3xl p-2 {{ $playable ? 'border-green-500 cursor-pointer' : 'border-transparent'}}"
     wire:click="playCard({{ json_encode($card->toDto()) }}, '{{ $playable }}')">

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
</div>
