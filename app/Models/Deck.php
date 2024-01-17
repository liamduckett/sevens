<?php

namespace App\Models;

use App\Enums\Ranks;
use App\Enums\Suits;

final class Deck
{
    /** @var array<string> $cards  */
    public array $cards = [];
    /** @var array<array<string>> $cards  */
    public array $hands;

    public function __construct()
    {
        $players = 4;

        $suits = Suits::cases();
        $ranks = Ranks::cases();

        foreach($suits as $suit) {
            foreach ($ranks as $rank) {
                $this->cards[] = "$rank->value of $suit->value";
            }
        }

        shuffle($this->cards);

        $this->hands = array_chunk($this->cards, 52 / $players);
    }
}
