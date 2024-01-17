<?php

namespace App\Models;

use App\Enums\Ranks;
use App\Enums\Suits;

final class Deck
{
    /** @var Array<string> $cards  */
    public array $cards = [];

    public function __construct()
    {
        $suits = Suits::cases();
        $ranks = Ranks::cases();

        foreach($suits as $suit) {
            foreach ($ranks as $rank) {
                $this->cards[] = "$rank->value of $suit->value";
            }
        }

        shuffle($this->cards);
    }
}
