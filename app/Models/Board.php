<?php

namespace App\Models;

use App\Enums\Suit;
use Livewire\Wireable;

final class Board implements Wireable
{
    public function __construct(public array $contents) {}

    public static function make(): self
    {
        $nullSuit = ['lowest' => null, 'highest' => null];

        $contents = [
            Suit::DIAMONDS->value => $nullSuit,
            Suit::CLUBS->value => $nullSuit,
            Suit::HEARTS->value => $nullSuit,
            Suit::SPADES->value => $nullSuit,
        ];

        return new self($contents);
    }

    /**
     * @param Suit $suit
     * @return array{lowest: ?int, highest: ?int}
     */
    public function suit(Suit $suit): array
    {
        return $this->contents[$suit->value];
    }

    public function isEmpty(): bool
    {
        return $this->missingSuit(Suit::DIAMONDS)
            && $this->missingSuit(Suit::CLUBS)
            && $this->missingSuit(Suit::HEARTS)
            && $this->missingSuit(Suit::SPADES);
    }

    public function missingSuit(Suit $suit): bool
    {
        return $this->contents[$suit->value]['lowest'] === null;
    }

    public function toLivewire(): array
    {
        return $this->contents;
    }

    public static function fromLivewire($value): self
    {
        return new self($value);
    }
}
