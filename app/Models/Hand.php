<?php

namespace App\Models;

use Livewire\Wireable;

final class Hand implements Wireable
{
    /** @var Array<string> $cards  */
    public function __construct(public array $cards) {}

    public function toLivewire(): array
    {
        return $this->cards;
    }

    public static function fromLivewire($value): self
    {
        return new self($value);
    }
}
