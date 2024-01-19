<?php

namespace App\Models;

use App\Enums\Rank;
use App\Enums\Suit;
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
        usort($this->cards, Card::compare());
    }

    public function hasStartingCard(): bool
    {
        $startingCard = new Card(Suit::DIAMONDS, Rank::SEVEN);

        return array_reduce(
            array: $this->cards,
            callback: fn(bool $carry, Card $card) => $carry or ($card->toDto() === $startingCard->toDto()),
            initial: false,
        );
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
