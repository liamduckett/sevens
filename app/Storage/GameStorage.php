<?php

namespace App\Storage;

use App\Events\PlayerJoined;
use Illuminate\Support\Facades\Cache;

// TODO: replace Lobby $players with just the gamestorage
class GameStorage
{
    public array $players;

    public function __construct(public string $code)
    {
        $this->players = Cache::get("games.$this->code.players") ?? [];
    }

    public function addPlayerIfApplicable(string $playerId): array
    {
        if($this->playerCanJoin($playerId)) {
            $this->players = $this->addPlayer($playerId);
        }

        return $this->players;
    }

    public function isHost(string $playerId): bool
    {
        return $this->players[0] === $playerId;
    }

    private function addPlayer(string $playerId): array
    {
        $this->players[] = $playerId;
        Cache::put("games.$this->code.players", $this->players);
        PlayerJoined::dispatch();
        return $this->players;
    }

    private function playerCanJoin(string $playerId): bool
    {
        return $this->playerIsntInGame($playerId) && $this->isntFull();
    }

    private function playerIsntInGame(string $playerId): bool
    {
        return ! $this->playerIsInGame($playerId);
    }

    private function isntFull(): bool
    {
        return count($this->players) < 4;
    }

    private function playerIsInGame(string $playerId): bool
    {
        return in_array($playerId, $this->players);
    }
}
