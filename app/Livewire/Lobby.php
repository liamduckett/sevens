<?php

namespace App\Livewire;

use App\Events\GameStarted;
use App\Events\PlayerJoined;
use App\Events\PlayerLeft;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
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
        $this->players = Cache::get("players.$this->code") ?? [];

        Session::put('code', $this->code);

        if(!in_array($this->playerId, $this->players) and count($this->players) < 4) {
            $this->players[] = $this->playerId;
            Cache::put("players.$this->code", $this->players);
            PlayerJoined::dispatch();
        }

        $this->host = $this->players[0] === $this->playerId;
    }

    public function render(): View
    {
        $this->players = Cache::get("players.$this->code") ?? [];
        return view('livewire.lobby');
    }

    public function getListeners(): array
    {
        return [
            "echo:lobby,PlayerJoined" => 'reload',
            "echo:lobby,PlayerLeft" => 'reload',
        ];
    }

    public function reload(): void
    {
        $this->render();
    }

    public function leave(): void
    {
        $this->players = array_diff($this->players, [$this->playerId]);
        Cache::put("players.$this->code", $this->players);
        PlayerLeft::dispatch();
        $this->redirect('/lobby');
    }

    public function start(): void
    {
        GameStarted::dispatch();
    }

    private function getCode(): ?string
    {
        $code = Request::get('code') ?? Session::get('code');

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
