<?php

namespace App\Enums;

enum Suit: string
{
    case DIAMONDS = 'diamonds';
    case CLUBS = 'clubs';
    case HEARTS = 'hearts';
    case SPADES = 'spades';

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
