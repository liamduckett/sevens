<div class="max-w-lg mx-auto">
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

    @if(!$playerId || $lobbyStorage->playerIsntInGame($playerId))
        <div class="pb-2">
            <input wire:model.live="name" placeholder="Liam..."/>

            @error('name')
                <div class="text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>

        <x-button wire:click="join">
            Join
        </x-button>
    @endif

    @if($playerId && $lobbyStorage->playerIsInGame($playerId))
        <x-button wire:click="leave">
            Leave
        </x-button>
    @endif

    @if($playerId && $lobbyStorage->isHost($playerId) && count($lobbyStorage->players) === 4)
        <x-button wire:click="triggerStart">
            Start
        </x-button>
    @endif
</div>
