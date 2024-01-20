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

        // TODO: a card should only be playable if it's in the hand of the current player
        if(! $this->board->cardIsPlayable($card)) {
            throw new \Exception("Unplayable card");
        }

        $this->board->play($card);
        $this->currentPlayerHand()->removeCard($card);

        $this->nextPlayer();
    }

    public function knock(): void
    {
        $hand = $this->currentPlayerHand();

        if($this->board->handIsPlayable($hand)) {
            throw new \Exception("Playable hand");
        }

        $this->nextPlayer();
    }

    public function currentPlayerHand(): Hand
    {
        return $this->hands[$this->currentPlayer];
    }

    private function nextPlayer(): void {
        // array indexes are 0 through (PLAYERS - 1)
        // if next player would be too high, loop back to 0
        $this->currentPlayer = $this->currentPlayer + 1 === self::PLAYERS
            ? 0
            : $this->currentPlayer + 1;
    }
}
