<?php

namespace App\Models;

use Livewire\Wireable;

final class BoardSuit implements Wireable
{
    public function __construct(public ?int $lowest, public ?int $highest) {}

    public static function make(?int $lowest = null, ?int $highest = null): self
    {
        return new self($lowest, $highest);
    }

    public function setMin(Card $card): void
    {
        $this->lowest = $card->rank->value;
    }

    public function setMax(Card $card): void
    {
        $this->highest = $card->rank->value;
    }

    public function setMinAndMax(Card $card): void
    {
        $this->lowest = $card->rank->value;
        $this->highest = $card->rank->value;
    }

    public function toLivewire(): array
    {
        return [
            'lowest' => $this->lowest,
            'highest' => $this->highest,
        ];
    }

    public static function fromLivewire($value): self
    {
        return new self(
            $value['lowest'],
            $value['highest'],
        );
    }
}
