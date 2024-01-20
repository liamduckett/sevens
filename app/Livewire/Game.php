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

    public function playCard(array $card, bool $attempt): void
    {
        // a bit hacky - workaround to remove the need to conditionally add wire:click="playCard"
        if($attempt === false) {
            return;
        }

        $card = Card::fromDto($card);

        if(! $this->board->cardIsPlayable($card)) {
            throw new \Exception("Unplayable card");
        }

        $this->board->play($card);

        // hand of current player
        $currentPlayerHand = $this->hands[$this->currentPlayer];
        $currentPlayerHand->removeCard($card);

        $this->currentPlayer = $this->nextPlayer();
    }

    public function nextPlayer(): int {
        // array indexes are 0 through (PLAYERS - 1)
        // if next player would be too high, loop back to 0
        return $this->currentPlayer + 1 === self::PLAYERS
            ? 0
            : $this->currentPlayer + 1;
    }
}
