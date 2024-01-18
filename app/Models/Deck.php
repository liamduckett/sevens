<?php

namespace App\Models;

use App\Enums\Ranks;
use App\Enums\Suits;

final class Deck
{
    /** @var array<Card> $cards  */
    public array $cards = [];

    public function __construct()
    {
        $suits = Suits::cases();
        $ranks = Ranks::cases();

        foreach($suits as $suit) {
            foreach ($ranks as $rank) {
                $this->cards[] = new Card($suit, $rank);
            }
        }

        shuffle($this->cards);
    }

    /**
     * @param int $players
     * @return array<Hand>
     */
    public static function splitIntoHands(int $players): array
    {
        $deck = new Deck;

        $cardChunks = array_chunk($deck->cards, 52 / $players);

        return array_map(
            callback: fn(array $cardChunk) => new Hand($cardChunk),
            array: $cardChunks,
        );
    }
}
