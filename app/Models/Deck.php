<?php

namespace App\Models;

final class Deck
{
    /** @var Array<string> $cards  */
    public array $cards;

    public function __construct()
    {
        $suits = ['diamonds', 'clubs', 'hearts', 'spades'];

        $ranks = [
            'ace',
            'two',
            'three',
            'four',
            'five',
            'six',
            'seven',
            'eight',
            'nine',
            'ten',
            'jack',
            'queen',
            'king',
        ];

        foreach($suits as $suit) {
            foreach ($ranks as $rank) {
                $this->cards[] = "$rank of $suit";
            }
        }
    }
}
