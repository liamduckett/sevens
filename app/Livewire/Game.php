<?php

namespace App\Livewire;

use App\Models\Deck;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Game extends Component
{
    public function render(): View
    {
        $deck = new Deck;

        return view('livewire.game', [
            'deck' => $deck,
        ]);
    }
}
