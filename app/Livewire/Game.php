<?php

namespace App\Livewire;

use App\Enums\Rank;
use App\Enums\Suit;
use App\Models\Board;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Hand;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Game extends Component
{
    public const PLAYERS = 4;

    #[Locked]
    /** @var array<Hand> $hands  */
    public array $hands;
    public int $currentPlayer;
    public Board $board;

    public function mount(): void
    {
        $this->board = Board::make();
        $this->hands = Deck::splitIntoHands(players: self::PLAYERS);

        // the first player is the one with the 7 diamonds
        $startingHand = array_filter(
            $this->hands,
            fn(Hand $hand) => $hand->hasStartingCard(),
        );

        $this->currentPlayer = array_key_first($startingHand);
    }

    public function render(): View
    {
        return view('livewire.game');
    }

    public function isPlayable(Card $card): bool
    {
        $startingCard = new Card(Suit::DIAMONDS, Rank::SEVEN);

        // when the board is empty only the starting card can be played
        if($this->board->isEmpty()) {
            return $card->toDto() === $startingCard->toDto();
        }

        // once the board has at least one card...
        return match($card->rank->value <=> 7) {
            // any seven that has not yet been played can be played
            0  => $this->board->missingSuit($card->suit),
            // if this card is below 7, then the number 1 above it must have been played
            -1 => $this->board->suit($card->suit)['lowest'] === $card->rank->value + 1,
            // if this card is above 7, then the number 1 below it must have been played
            1  => $this->board->suit($card->suit)['highest'] === $card->rank->value - 1,
        };
    }

    public function playCard(array $card, bool $attempt): void
    {
        // a bit hacky - workaround to remove the need to conditionally add wire:click="playCard"
        if($attempt === false) {
            return;
        }

        $card = Card::fromDto($card);

        if(! $this->isPlayable($card)) {
            throw new \Exception("Unplayable card");
        }

        $this->board->contents[$card->suit->value]['lowest'] = min(
            $card->rank->value,
            $this->board->suit($card->suit)['lowest'] ?? PHP_INT_MAX,
        );

        $this->board->contents[$card->suit->value]['highest'] = max(
            $card->rank->value,
            $this->board->suit($card->suit)['highest'] ?? 0,
        );
    }
}
