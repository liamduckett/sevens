<div class="max-w-lg mx-auto">
    <div>(Id: {{ $playerId }})</div>
    <div>Code: {{ $code }}</div>

    @foreach($lobbyStorage->players as $player)
        <div>
            Id:

            {{ $player }}
        </div>
    @endforeach

    @for($missingPlayer = 1; $missingPlayer <= 4 - count($lobbyStorage->players); $missingPlayer++)
        <div>
            Id:

            WAITING
        </div>
    @endfor

    <x-button wire:click="leave">
        Leave
    </x-button>

    @if($host && count($lobbyStorage->players) === 4)
        <x-button wire:click="triggerStart">
            Start
        </x-button>
    @endif
</div>
