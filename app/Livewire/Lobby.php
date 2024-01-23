<?php

namespace App\Livewire;

use App\Events\GameStarted;
use App\Events\PlayerLeft;
use App\Storage\GameStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
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
    public array $players = [];

    public function mount(): void
    {
        $this->playerId = $this->getPlayerId();

        $this->code = $this->getCode();
        $gameStorage = new GameStorage(code: $this->code);
        $this->players = $gameStorage->players;

        $this->players = $gameStorage->addPlayerIfApplicable($this->playerId);
        $this->host = $gameStorage->isHost($this->playerId);
    }

    public function render(): View
    {
        $gameStorage = new GameStorage(code: $this->code);
        $this->players = $gameStorage->players;
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
        $this->players = array_diff($this->players, [$this->playerId]);
        Cache::put("games.$this->code.players", $this->players);
        PlayerLeft::dispatch();
        $this->redirect('/lobby');
    }

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
            $code = Str::random(length: 4);
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
