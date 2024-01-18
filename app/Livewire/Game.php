<?php

namespace App\Livewire;

use App\Models\Deck;
use App\Models\Hand;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Game extends Component
{
    #[Locked]
    /** @var array<Hand> $hands  */
    public array $hands = [];

    public function render(): View
    {
        $this->hands = Deck::splitIntoHands(players: 4);

        return view('livewire.game');
    }
}
