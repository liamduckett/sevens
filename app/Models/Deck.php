<?php

namespace App\Models;

use App\Enums\Suits;

final class Deck
{
    /** @var Array<string> $cards  */
    public array $cards;

    public function __construct()
    {
        $suits = Suits::cases();

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
                $this->cards[] = "$rank of $suit->value";
            }
        }
    }
}
