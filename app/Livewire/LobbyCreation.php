<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

class LobbyCreation extends Component
{
    public string $code;
    public int $playerCount = 4;

    public function mount(): void
    {
        do {
            $potentialCode = Str::random(length: 4);
        } while(Cache::has("games.$potentialCode.size"));
        // ensure code isnt already in use

        $this->code = $potentialCode;
    }

    public function render(): View
    {
        return view('livewire.lobby-creation');
    }

    public function createLobby(): void
    {
        Cache::put("games.$this->code.size", $this->playerCount);
        $this->redirect("/lobby?code=$this->code");
    }
}
