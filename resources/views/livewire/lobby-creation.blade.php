<div class="max-w-lg mx-auto flex flex-col gap-4 mt-10">
    <div class="flex gap-2">
        Code:
        <input type="text" wire:model="code"/>
    </div>

    <div class="flex flex-col gap-2">
        Players:

        <div>
            <label for="playerCount-4">4</label>
            <input type="radio" id="playerCount-4" wire:model="playerCount" name="playerCount" value="4"/>
        </div>

        <div>
            <label for="playerCount-3">3</label>
            <input type="radio" id="playerCount-3" wire:model="playerCount" name="playerCount" value="3"/>
        </div>
    </div>

    <x-button wire:click="createLobby">
        Create
    </x-button>
</div>
