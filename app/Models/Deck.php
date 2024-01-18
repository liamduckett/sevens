<?php

namespace App\Models;

use App\Enums\Ranks;
use App\Enums\Suits;

final class Deck
{
    /** @var array<string> $cards  */
    public array $cards = [];
    /** @var array<Hand> $hands  */
    public array $hands = [];

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

        $hands = array_chunk($this->cards, 52 / $players);

        foreach($hands as $hand) {
            $this->hands[] = new Hand($hand);
        }
    }
}
