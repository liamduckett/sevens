<?php

namespace App\Livewire;

use App\Events\GameStarted;
use App\Storage\LobbyStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Lobby extends Component
{
    #[Locked]
    public bool $host;
    #[Locked]
    public string $playerId;
    #[Locked]
    public string $code;
    #[Locked]
    public LobbyStorage $lobbyStorage;

    public function mount(): void
    {
        $this->playerId = $this->getPlayerId();

        $this->code = $this->getCode();

        $this->lobbyStorage = new LobbyStorage(code: $this->code);
        $this->lobbyStorage->addPlayerIfApplicable($this->playerId);

        $this->host = $this->lobbyStorage->isHost($this->playerId);
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

    public function reload(): void
    {
        $this->render();
    }

    public function leave(): void
    {
        $this->lobbyStorage->removePlayer($this->playerId);
        $this->redirect('/lobby');
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

    private function getPlayerId(): string
    {
        $playerId = Session::get('playerId');

        if($playerId === null) {
            $playerId = Str::random(length: 4);
            Session::put('playerId', $playerId);
        }

        return $playerId;
    }
}
