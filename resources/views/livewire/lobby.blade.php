<div class="max-w-lg mx-auto">
    <div>Name: {{ $playerId }}</div>

    <div class="my-4">
        @foreach($lobbyStorage->players as $player)
            <div>
                Name:

                {{ $player }}
            </div>
        @endforeach

        @for($missingPlayer = 1; $missingPlayer <= 4 - count($lobbyStorage->players); $missingPlayer++)
            <div>
                Name:

                WAITING
            </div>
        @endfor
    </div>

    @if($lobbyStorage->playerIsntInGame($playerId))
        <x-button wire:click="join">
            Join
        </x-button>
    @endif

    @if($lobbyStorage->playerIsInGame($playerId))
        <x-button wire:click="leave">
            Leave
        </x-button>
    @endif

    @if($lobbyStorage->isHost($playerId) && count($lobbyStorage->players) === 4)
        <x-button wire:click="triggerStart">
            Start
        </x-button>
    @endif
</div>
