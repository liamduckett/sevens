<div class="max-w-lg mx-auto">
    @php
        $playerId = $this->getPlayerId();
    @endphp

    <div class="my-4">
        @foreach($lobbyStorage->players as $player)
            <div>
                Name:

                {{ $player }}
            </div>
        @endforeach

        @for($openSlot = 1; $openSlot <= $lobbyStorage->openSlots(); $openSlot++)
            <div>
                Name:

                WAITING
            </div>
        @endfor

        @if($lobbyStorage->isntFull())
            <div class="font-semibold text-green-700 text-sm mt-2">
                Waiting for {{ $lobbyStorage->openSlots() }} players...
            </div>
        @else
            <div class="font-semibold text-green-700 text-sm mt-2">
                Waiting for {{ $lobbyStorage->isHost($playerId) ? 'you' : $lobbyStorage->host() }} to start...
            </div>
        @endif
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

    @if($playerId)
        @if($lobbyStorage->playerIsInGame($playerId))
            <x-button wire:click="leave">
                Leave
            </x-button>
        @endif

        @if($lobbyStorage->isHost($playerId))
            <x-button wire:click="triggerStart" :disabled="$lobbyStorage->isntFull()">
                Start
            </x-button>
        @endif
    @endif
</div>
