<?php

namespace App\Livewire;

use App\Events\TurnTaken;
use App\Models\Board;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Hand;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Game extends Component
{
    public const PLAYERS = 4;

    #[Locked]
    /** @var array<Hand> $hands  */
    public array $hands;
    #[Locked]
    public int $currentPlayerId;
    #[Locked]
    public Board $board;
    #[Locked]
    public ?int $winner = null;
    #[Locked]
    public array $names = [];
    #[Locked]
    public string $code;

    public function mount(): void
    {
        // TODO: gracefully handle no code passed!
        $this->code = Request::get('code');
        $this->names = Cache::get("games.$this->code.players") ?? [];

        // TODO: neaten
        // if game has already been set up!
        if($hands = Cache::get("hands.$this->code")) {
            $this->hands = $hands;
            $this->currentPlayerId = Cache::get("currentPlayerId.$this->code");
            $this->board = Cache::get("board.$this->code");
        } else {
            $this->board = Board::make();
            $this->hands = Deck::splitIntoHands(players: self::PLAYERS, names: $this->names);

            // the first player is the one with the 7 diamonds
            $startingHand = array_filter(
                $this->hands,
                fn(Hand $hand) => $hand->hasStartingCard(),
            );

            $this->currentPlayerId = array_key_first($startingHand);

            Cache::put("hands.$this->code", $this->hands);
            Cache::put("currentPlayerId.$this->code", $this->currentPlayerId);
            Cache::put("board.$this->code", $this->board);
        }
    }

    public function render(): View
    {
        $this->hands = Cache::get("hands.$this->code");
        $this->currentPlayerId = Cache::get("currentPlayerId.$this->code");
        $this->board = Cache::get("board.$this->code");

        return view('livewire.game');
    }

    public function getListeners(): array
    {
        return [
            "echo:lobby,TurnTaken" => 'reload',
        ];
    }

    public function reload(): void
    {
        $this->render();
    }

    public function playCard(array $card, bool $attempt): void
    {
        // a bit hacky - workaround to remove the need to conditionally add wire:click="playCard"
        if($attempt === false) {
            return;
        }

        if($this->hasWinner()) {
            throw new \Exception("Game already won");
        }

        $card = Card::fromDto($card);

        // TODO: a card should only be playable if it's in the hand of the current player
        if(! $this->board->cardIsPlayable($card)) {
            throw new \Exception("Unplayable card");
        }

        $this->board->play($card);
        $this->currentPlayerHand()->removeCard($card);

        $this->checkForWinner();

        $this->nextPlayer();
    }

    public function knock(): void
    {
        if($this->hasWinner()) {
            throw new \Exception("Game already won");
        }

        $hand = $this->currentPlayerHand();

        if($this->board->handIsPlayable($hand)) {
            throw new \Exception("Playable hand");
        }

        $this->nextPlayer();
    }

    public function currentPlayerHand(): Hand
    {
        return $this->hands[$this->currentPlayerId];
    }

    public function myHand(): Hand
    {
        $hand = array_search(Session::get('playerId'), $this->names);

        return $this->hands[$hand];
    }

    public function hasWinner(): bool
    {
        return $this->winner !== null;
    }

    public function hasNoWinner(): bool
    {
        return ! $this->hasWinner();
    }

    private function nextPlayer(): void {
        // array indexes are 0 through (PLAYERS - 1)
        // if next player would be too high, loop back to 1
        $this->currentPlayerId = $this->currentPlayerId === self::PLAYERS - 1
            ? 0
            : $this->currentPlayerId + 1;

        TurnTaken::dispatch();

        Cache::put("hands.$this->code", $this->hands);
        Cache::put("currentPlayerId.$this->code", $this->currentPlayerId);
        Cache::put("board.$this->code", $this->board);
    }

    private function checkForWinner(): void
    {
        $winners = array_filter(
            $this->hands,
            fn(Hand $hand) => $hand->isEmpty(),
        );

        if(count($winners) > 1) {
            throw new \Exception("More than one winner");
        }

        if(count($winners) === 1) {
            $winners = array_keys($winners);
            $this->winner = $winners[0];
        }
    }
}
