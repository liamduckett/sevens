<?php

namespace App\Livewire;

use App\Events\GameStarted;
use App\Storage\LobbyStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Lobby extends Component
{
    // the input that gets turned into the playerId when valid
    public ?string $name;
    #[Locked]
    public string $playerId;
    #[Locked]
    public string $code;
    #[Locked]
    public LobbyStorage $lobbyStorage;

    public function mount(): void
    {
        $this->name = $this->getPlayerId();
        $this->code = $this->getCode();

        $this->lobbyStorage = new LobbyStorage(code: $this->code);
    }

    public function render(): View
    {
        $this->lobbyStorage->refresh();
        return view('livewire.lobby');
    }

    public function getListeners(): array
    {
        return [
            "echo:lobby,PlayerJoined" => 'reload',
            "echo:lobby,PlayerLeft" => 'reload',
            "echo:lobby,GameStarted" => 'start',
        ];
    }

    public function rules(): array
    {
        $existingPlayerIds = Cache::get('playerIds') ?? [];
        //dd($existingPlayerIds);

        return [
            'name' => ['required', 'string', 'between:2,4', Rule::notIn($existingPlayerIds)],
        ];
    }

    public function reload(): void
    {
        $this->render();
    }

    public function join(): void
    {
        $this->validate();
        $this->updatePlayerId(); // pass name here...
        $this->lobbyStorage->addPlayerIfApplicable($this->name);
    }

    // note this isnt a lifecycle hook, that would be 'updatedName'
    private function updatePlayerId(): void
    {
        // keep track of every play name in use to prevent duplicates
        $oldPlayerId = $this->getPlayerId();
        $playerIds = Cache::get('playerIds') ?? [];
        // remove player's old ID (if applicable)
        $playerIds = array_diff($playerIds, [$oldPlayerId]);
        // add player's new ID
        $playerIds[] = $this->name;

        Cache::put('playerIds', $playerIds);
        $this->playerId = $this->name;
        Session::put('playerId', $this->name);
    }

    public function leave(): void
    {
        // keep track of every play name in use to prevent duplicates
        $playerIds = Cache::get('playerIds') ?? [];
        // remove player's ID
        $playerIds = array_diff($playerIds, [$this->playerId]);

        Cache::put('playerIds', $playerIds);

        $this->lobbyStorage->removePlayer($this->name);
    }

    // this is done via an event (rather than just the method) to trigger for everyone
    public function triggerStart(): void
    {
        GameStarted::dispatch();
    }

    public function start(): void
    {
        $this->redirect("/game?code=$this->code");
    }

    private function getCode(): string
    {
        $code = Request::get('code');

        if(strlen($code) !== 4 or !ctype_alnum($code)) {
            throw new \Exception("Invalid Lobby Code");
        }

        return $code;
    }

    private function getPlayerId(): ?string
    {
        return Session::get('playerId');
    }
}
