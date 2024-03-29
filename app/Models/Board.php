<?php

namespace App\Models;

use App\Enums\Rank;
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

    public function cardIsPlayable(Card $card): bool
    {
        $startingCard = new Card(Suit::DIAMONDS, Rank::SEVEN);

        // when the board is empty only the starting card can be played
        if($this->isEmpty()) {
            return $card->toDto() === $startingCard->toDto();
        }

        // once the board has at least one card...
        return match($card->rank->value <=> 7) {
            // any seven that has not yet been played can be played
            0  => $this->missingSuit($card->suit),
            // if this card is below 7, then the number 1 above it must have been played
            -1 => $this->suit($card->suit)->lowest === $card->rank->value + 1,
            // if this card is above 7, then the number 1 below it must have been played
            1  => $this->suit($card->suit)->highest === $card->rank->value - 1,
        };
    }

    public function cardIsntPlayable(Card $card): bool
    {
        return ! $this->cardIsPlayable($card);
    }

    public function handIsPlayable(Hand $hand): bool
    {
        return array_reduce(
            array: $hand->cards,
            callback: fn(bool $carry, Card $card) => $carry or $this->cardIsPlayable($card),
            initial: false,
        );
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
