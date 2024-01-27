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
    public string $code;
    #[Locked]
    public LobbyStorage $lobbyStorage;

    public function mount(): void
    {
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
        $this->lobbyStorage->addPlayerIfApplicable($this->name);
    }

    public function leave(): void
    {
        $playerId = $this->getPlayerId();
        $this->lobbyStorage->removePlayer($playerId);
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
