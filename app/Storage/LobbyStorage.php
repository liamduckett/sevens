<?php

namespace App\Storage;

use App\Events\PlayerJoined;
use App\Events\PlayerLeft;
use Illuminate\Support\Facades\Cache;
use Livewire\Wireable;

class LobbyStorage implements Wireable
{
    public function __construct(
        public string $code,
        public array $players = [],
    )
    {
        $this->players = Cache::get("games.$this->code.players") ?? [];
    }

    public function refresh(): void
    {
        $this->players = Cache::get("games.$this->code.players") ?? [];
    }

    public function addPlayerIfApplicable(string $playerId): void
    {
        if($this->playerCanJoin($playerId)) {
            $this->addPlayer($playerId);
        }
    }

    public function isHost(string $playerId): bool
    {
        if(empty($this->players)) {
            return false;
        }

        return $this->players[0] === $playerId;
    }

    public function removePlayer(string $playerId): void
    {
        $this->players = array_diff($this->players, [$playerId]);
        $this->players = array_values($this->players);

        Cache::put("games.$this->code.players", $this->players);
        PlayerLeft::dispatch();
    }

    private function addPlayer(string $playerId): void
    {
        $this->players[] = $playerId;
        Cache::put("games.$this->code.players", $this->players);
        PlayerJoined::dispatch();
    }

    private function playerCanJoin(string $playerId): bool
    {
        return $this->playerIsntInGame($playerId) && $this->isntFull();
    }

    public function playerIsInGame(string $playerId): bool
    {
        return in_array($playerId, $this->players);
    }

    public function playerIsntInGame(string $playerId): bool
    {
        return ! $this->playerIsInGame($playerId);
    }

    private function isntFull(): bool
    {
        return count($this->players) < 4;
    }

    public function toLivewire(): array
    {
        return [
            'code' => $this->code,
            'players' => $this->players,
        ];
    }

    public static function fromLivewire($value): self
    {
        return new self(
            $value['code'],
            $value['players'],
        );
    }
}
