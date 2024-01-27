<?php

namespace App\Storage;

use App\Events\PlayerJoined;
use App\Events\PlayerLeft;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Wireable;

class LobbyStorage implements Wireable
{
    public const int PLAYERS = 4;

    public function __construct(
        public string $code,
        public array $players = [],
        public int $size = 4,
    )
    {
        $this->players = Cache::get("games.$this->code.players") ?? [];
        $this->size = Cache::get("games.$this->code.size");
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

    public function host(): string
    {
        return $this->players[0];
    }

    public function isHost(string $playerId): bool
    {
        if(empty($this->players)) {
            return false;
        }

        return $this->host() === $playerId;
    }

    public function removePlayer(string $playerId): void
    {
        // REMOVE THEM FROM THE LOBBY
        $this->players = array_diff($this->players, [$playerId]);
        $this->players = array_values($this->players);

        // FREE UP THE NAME
        self::freePlayerId($playerId);

        // PERSIST
        Session::forget('playerId');
        Cache::put("games.$this->code.players", $this->players);

        PlayerLeft::dispatch();

        Log::info("[$this->code] Left: $playerId");
    }

    private function addPlayer(string $playerId): void
    {
        // keep track of every play name in use to prevent duplicates
        $playerIds = Cache::get('playerIds') ?? [];

        // ADD THEM TO THE LOBBY
        $this->players[] = $playerId;

        // RESERVE THE NAME
        $playerIds[] = $playerId;

        // PERSIST
        Session::put('playerId', $playerId);
        Cache::put('playerIds', $playerIds);
        Cache::put("games.$this->code.players", $this->players);

        PlayerJoined::dispatch();

        Log::info("[$this->code] Joined: $playerId");
    }

    public static function freePlayerId(string $playerId): void
    {
        // keep track of every play name in use to prevent duplicates
        $playerIds = Cache::get('playerIds') ?? [];

        // FREE UP THE NAME
        $playerIds = array_diff($playerIds, [$playerId]);
        $playerIds = array_values($playerIds);

        // PERSIST
        Cache::put('playerIds', $playerIds);
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

    private function isFull(): bool
    {
        return count($this->players) === $this->size;
    }

    public function isntFull(): bool
    {
        return !$this->isFull();
    }

    public function slotsOpen(): int
    {
        return $this->size - count($this->players);
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
