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

    public function min(int $value): int
    {
        return min($value, $this->lowest ?? PHP_INT_MAX);
    }

    public function max(int $value): int
    {
        return max($value, $this->lowest ?? 0);
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
