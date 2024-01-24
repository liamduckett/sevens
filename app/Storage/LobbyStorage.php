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

    public function removePlayer(string $playerId): array
    {
        $this->players = array_diff($this->players, [$playerId]);
        Cache::put("games.$this->code.players", $this->players);
        PlayerLeft::dispatch();

        return $this->players;
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
