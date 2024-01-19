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

    public static function compare(): callable
    {
        return function(Card $cardOne, Card $cardTwo) {
            // if different suits then do alphabetical suit
            // if same suit then compare int of rank

            return match($cardOne->suit === $cardTwo->suit) {
                true  => $cardOne->rank->value <=> $cardTwo->rank->value,
                false => $cardOne->suit->order() <=> $cardTwo->suit->order(),
            };
        };
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
