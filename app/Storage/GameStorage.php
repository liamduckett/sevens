<?php

namespace App\Storage;

use App\Events\PlayerJoined;
use App\Events\PlayerLeft;
use Illuminate\Support\Facades\Cache;
use Livewire\Wireable;

class GameStorage implements Wireable
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
