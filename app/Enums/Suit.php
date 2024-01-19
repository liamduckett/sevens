<?php

namespace App\Enums;

enum Suit: string
{
    case DIAMONDS = 'diamonds';
    case CLUBS = 'clubs';
    case HEARTS = 'hearts';
    case SPADES = 'spades';

    /**
     * When sorting a hand, we don't want two same colored suits next to each other
     */
    public function order(): int
    {
        return match($this) {
            self::DIAMONDS => 1,
            self::CLUBS => 2,
            self::HEARTS => 3,
            self::SPADES => 4,
        };
    }

    public function symbol(): string
    {
        return match($this) {
            self::DIAMONDS => '♢',
            self::CLUBS => '♧',
            self::HEARTS => '♡',
            self::SPADES => '♤',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DIAMONDS => 'red',
            self::CLUBS => 'black',
            self::HEARTS => 'red',
            self::SPADES => 'black',
        };
    }
}
