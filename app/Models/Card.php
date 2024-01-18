<?php

namespace App\Models;

use App\Enums\Rank;
use App\Enums\Suit;
use Livewire\Wireable;
use Stringable;

final class Card implements Stringable, Wireable
{
    public function __construct(
        public Suit $suit,
        public Rank $rank,
    ) {}

    public function __toString(): string
    {
        return "{$this->rank->value} of {$this->suit->value}";
    }

    public function toLivewire(): array
    {
        return [
            'suit' => $this->suit,
            'rank' => $this->rank,
        ];
    }

    public static function fromLivewire($value): self
    {
        return new self(
            $value['suit'],
            $value['rank']
        );
    }
}
