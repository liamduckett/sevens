<?php

namespace App\Models;

use App\Enums\Rank;
use App\Enums\Suit;

final class Deck
{
    /** @var array<Card> $cards  */
    public array $cards = [];

    public function __construct()
    {
        $suits = Suit::cases();
        $ranks = Rank::cases();

        foreach($suits as $suit) {
            foreach ($ranks as $rank) {
                $this->cards[] = new Card($suit, $rank);
            }
        }

        shuffle($this->cards);
    }

    /**
     * @param int $players
     * @param array<string> $names
     * @return array<Hand>
     */
    public static function splitIntoHands(int $players, array $names): array
    {
        $ids = array_keys($names);

        $deck = new Deck;

        $cardChunks = array_chunk($deck->cards, 52 / $players);
        $cardChunks = array_combine($ids, $cardChunks);

        return array_map(
            callback: fn(array $cardChunk) => new Hand($cardChunk),
            array: $cardChunks,
        );
    }
}
