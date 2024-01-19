<?php

namespace App\Models;

use Livewire\Wireable;

final class Hand implements Wireable
{
    /** @var Array<Card> $cards  */
    public function __construct(public array $cards)
    {
        $this->sort();
    }

    public function sort(): void
    {
        usort($this->cards, function(Card $cardOne, Card $cardTwo) {
            // if different suits then do alphabetical suit
            // if same suit then compare int of rank

            return match($cardOne->suit === $cardTwo->suit) {
                true  => $cardOne->rank->value <=> $cardTwo->rank->value,
                false => $cardOne->suit->order() <=> $cardTwo->suit->order(),
            };
        });
    }

    public function toLivewire(): array
    {
        return $this->cards;
    }

    public static function fromLivewire($value): self
    {
        return new self($value);
    }
}
