<?php

namespace App\Models;

use App\Enums\Suit;
use Livewire\Wireable;

final class Board implements Wireable
{
    /** @var array<BoardSuit> $contents */
    public function __construct(public array $contents) {}

    public static function make(): self
    {
        $contents = [
            Suit::DIAMONDS->value => BoardSuit::make(),
            Suit::CLUBS->value => BoardSuit::make(),
            Suit::HEARTS->value => BoardSuit::make(),
            Suit::SPADES->value => BoardSuit::make(),
        ];

        return new self($contents);
    }

    public function play(Card $card): void
    {
        $boardSuit = $this->suit($card->suit);

        match($card->rank->value <=> 7) {
            0  => $boardSuit->setMinAndMax($card),
            -1 => $boardSuit->setMin($card),
            1  => $boardSuit->setMax($card),
        };
    }

    /**
     * @param Suit $suit
     * @return BoardSuit
     */
    public function suit(Suit $suit): BoardSuit
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
        return $this->suit($suit)->lowest === null;
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
