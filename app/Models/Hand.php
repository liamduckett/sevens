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
        usort($this->cards, Card::compare());
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
