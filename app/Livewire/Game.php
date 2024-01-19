<?php

namespace App\Livewire;

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
    public array $hands = [];
    public int $currentPlayer;

    public function mount(): void
    {
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
}
