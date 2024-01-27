<?php

namespace App\Livewire;

use App\Events\GameWon;
use App\Events\TurnTaken;
use App\Models\Board;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Hand;
use App\Storage\LobbyStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Game extends Component
{
    public const int PLAYERS = 4;

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
        $this->code = Request::get('code');
        $this->names = Cache::get("games.$this->code.players") ?? [];

        if(count($this->names) !== self::PLAYERS) {
            throw new \Exception('Not enough players');
        }

        match($this->hasBeenSetup()) {
            true => $this->fetchFromCache(),
            false => $this->setUp(),
        };
    }

    public function render(): View
    {
        $this->fetchFromCache();
        $this->checkForWinner();
        return view('livewire.game');
    }

    public function getListeners(): array
    {
        return [
            'echo:lobby,TurnTaken' => 'reload',
            'echo:lobby,GameWon' => 'getWinner',
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

        if($this->isntCurrentPlayer()) {
            throw new \Exception("Not your turn");
        }

        if($this->hasWinner()) {
            throw new \Exception("Game already won");
        }

        $card = Card::fromDto($card);

        if(! $this->board->cardIsPlayable($card)) {
            throw new \Exception("Unplayable card");
        }

        $this->board->play($card);
        $this->currentPlayerHand()->removeCard($card);

        $this->checkForWinner();

        $this->nextPlayer();

        Log::info("[$this->code] " . Session::get('playerId') . " Played Card: $card");
    }

    public function knock(): void
    {
        if($this->isntCurrentPlayer()) {
            throw new \Exception("Not your turn");
        }

        if($this->hasWinner()) {
            throw new \Exception("Game already won");
        }

        $hand = $this->currentPlayerHand();

        if($this->board->handIsPlayable($hand)) {
            throw new \Exception("Playable hand");
        }

        $this->nextPlayer();

        Log::info("[$this->code] " . Session::get('playerId') . " Knocked");
    }

    public function currentPlayerHand(): Hand
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

    private function hasBeenSetup(): bool
    {
        return Cache::has("games.$this->code.board");
    }

    private function fetchFromCache(): void
    {
        $this->board = Cache::get("games.$this->code.board");
        $this->hands = Cache::get("games.$this->code.hands");
        $this->currentPlayerId = Cache::get("games.$this->code.currentPlayerId");
    }

    private function saveToCache(): void
    {
        Cache::put("games.$this->code.board", $this->board);
        Cache::put("games.$this->code.hands", $this->hands);
        Cache::put("games.$this->code.currentPlayerId", $this->currentPlayerId);
    }

    private function setUp(): void
    {
        $this->board = Board::make();
        $this->hands = Deck::splitIntoHands(players: self::PLAYERS, names: $this->names);

        // the first player is the one with the 7 diamonds
        $startingHand = array_filter(
            $this->hands,
            fn(Hand $hand) => $hand->hasStartingCard(),
        );

        $this->currentPlayerId = array_key_first($startingHand);

        $this->saveToCache();

        Log::info("[$this->code] Set Up");
    }

    private function nextPlayer(): void {
        // array indexes are 0 through (PLAYERS - 1)
        // if next player would be too high, loop back to 1
        $this->currentPlayerId = $this->currentPlayerId === self::PLAYERS - 1
            ? 0
            : $this->currentPlayerId + 1;

        $this->saveToCache();

        TurnTaken::dispatch();
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
            // game has been won, inform everyone else!
            $winners = array_keys($winners);
            $this->winner = $winners[0];
            Cache::put("game.$this->code.winner", $winners[0]);

            foreach($this->names as $playerId) {
                LobbyStorage::freePlayerId($playerId);
            }

            Log::info("[$this->code] " . Session::get('playerId') . " Won");

            GameWon::dispatch();
        }
    }

    public function getWinner(): void
    {
        $this->winner = Cache::get("game.$this->code.winner");
    }

    private function isCurrentPlayer(): bool
    {
        return $this->names[$this->currentPlayerId] === Session::get('playerId');
    }

    private function isntCurrentPlayer(): bool
    {
        return !$this->isCurrentPlayer();
    }
}
