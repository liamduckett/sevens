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
        $suits = Suits::cases();
        $ranks = Ranks::cases();

        foreach($suits as $suit) {
            foreach ($ranks as $rank) {
                $this->cards[] = "$rank->value of $suit->value";
            }
        }

        shuffle($this->cards);

        $this->hands = $this->splitIntoHands(players: 4);
    }

    /**
     * @param int $players
     * @return array<Hand>
     */
    private function splitIntoHands(int $players): array
    {
        $cardChunks = array_chunk($this->cards, 52 / $players);

        return array_map(
            callback: fn(array $cardChunk) => new Hand($cardChunk),
            array: $cardChunks,
        );
    }
}
