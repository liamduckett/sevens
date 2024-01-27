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
        if(!in_array($players, [3, 4])) {
            throw new \Exception("Invalid number of players");
        }

        $ids = array_keys($names);

        $deck = new Deck;

        // if there are 3 players we need to:
        // 1. remove the seven of diamonds
        // 2. split the remaining 51 cards
        // 3. pick a random player to go first...

        if($players === 3) {
            $startingCard = new Card(Suit::DIAMONDS, Rank::SEVEN);

            $deck->cards = array_filter(
                array: $deck->cards,
                callback: fn(Card $card) => $card->toDto() !== $startingCard->toDto(),
            );
        }

        $cardsPerHand = count($deck->cards) / $players;
        $cardChunks = array_chunk($deck->cards, $cardsPerHand);
        $cardChunks = array_combine($ids, $cardChunks);

        return array_map(
            callback: fn(array $cardChunk) => new Hand($cardChunk),
            array: $cardChunks,
        );
    }
}
