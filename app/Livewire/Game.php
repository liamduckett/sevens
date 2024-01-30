<?php

namespace App\Livewire;

use App\Enums\Rank;
use App\Enums\Suit;
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
    #[Locked]
    public Hand $hand;
    #[Locked]
    public array $handCounts;
    #[Locked]
    public int $currentTurnPlayerId;
    #[Locked]
    public Board $board;
    #[Locked]
    public ?int $winner = null;
    #[Locked]
    public array $players;
    #[Locked]
    public string $code;
    #[Locked]
    public int $size;

    public function mount(): void
    {
        $this->code = Request::get('code');
        $this->players = Cache::get("games.$this->code.players") ?? [];
        $this->size = Cache::get("games.$this->code.size");
        $this->winner = Cache::get("games.$this->code.winner");

        if(count($this->players) !== $this->size) {
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
        return view('livewire.game');
    }

    public function getListeners(): array
    {
        return [
            'echo:lobby,TurnTaken' => 'render',
            'echo:lobby,GameWon' => 'getWinner',
        ];
    }

    public function getWinner(): void
    {
        $this->winner = Cache::get("games.$this->code.winner");
    }

    public function playCard(array $card, bool $attempt): void
    {
        // a bit hacky - workaround to remove the need to conditionally add wire:click="playCard"
        if($attempt === false) {
            return;
        }

        if($this->isntCurrentTurnPlayer()) {
            throw new \Exception("Not your turn");
        }

        if($this->hasWinner()) {
            throw new \Exception("Game already won");
        }

        $card = Card::fromDto($card);

        if($this->board->cardIsntPlayable($card)) {
            throw new \Exception("Unplayable card");
        }

        $this->board->play($card);
        $this->currentTurnPlayerHand()->removeCard($card);

        $this->handCounts[$this->playerKey()] -= 1;

        $this->checkForWinner();

        $this->nextPlayer();

        Log::info("[$this->code] {$this->playerId()} Played Card: $card");
    }

    public function knock(): void
    {
        if($this->isntCurrentTurnPlayer()) {
            throw new \Exception("Not your turn");
        }

        if($this->hasWinner()) {
            throw new \Exception("Game already won");
        }

        $hand = $this->currentTurnPlayerHand();

        if($this->board->handIsPlayable($hand)) {
            throw new \Exception("Playable hand");
        }

        $this->nextPlayer();

        Log::info("[$this->code] {$this->playerId()} Knocked");
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
        $hands = Cache::get("games.$this->code.hands");
        $this->hand = $hands[$this->playerKey()];

        $this->board = Cache::get("games.$this->code.board");
        $this->handCounts = Cache::get("games.$this->code.handCounts");
        $this->currentTurnPlayerId = Cache::get("games.$this->code.currentTurnPlayerId");
    }

    private function saveToCache(): void
    {
        // update hands...
        $hands = Cache::get("games.$this->code.hands");
        $hands[$this->playerKey()] = $this->hand;

        Cache::put("games.$this->code.board", $this->board);
        Cache::put("games.$this->code.hands", $hands);
        Cache::put("games.$this->code.handCounts", $this->handCounts);
        Cache::put("games.$this->code.currentTurnPlayerId", $this->currentTurnPlayerId);
    }

    private function setUp(): void
    {
        $this->board = Board::make();
        $hands = Deck::splitIntoHands(players: $this->size, names: $this->players);

        $this->handCounts = array_map(
            fn(Hand $hand) => count($hand->cards),
            $hands,
        );

        $this->currentTurnPlayerId = $this->determineFirstTurnPlayer($hands);

        Cache::put("games.$this->code.board", $this->board);
        Cache::put("games.$this->code.hands", $hands);
        Cache::put("games.$this->code.handCounts", $this->handCounts);
        Cache::put("games.$this->code.currentTurnPlayerId", $this->currentTurnPlayerId);

        Log::info("[$this->code] Set Up");
    }

    private function nextPlayer(): void {
        // array indexes are 0 through ($size - 1)
        // if next player would be too high, loop back to 1
        $this->currentTurnPlayerId = $this->currentTurnPlayerId === $this->size - 1
            ? 0
            : $this->currentTurnPlayerId + 1;

        $this->saveToCache();

        TurnTaken::dispatch();
    }

    private function checkForWinner(): void
    {
        if($this->hand->isEmpty()) {
            $this->winner = $this->playerKey();
            Cache::put("games.$this->code.winner", $this->winner);
            LobbyStorage::freePlayerIds($this->players);

            Log::info("[$this->code] $this->winner Won");

            GameWon::dispatch();
        }
    }

    private function isCurrentTurnPlayer(): bool
    {
        return $this->players[$this->currentTurnPlayerId] === $this->playerId();
    }

    private function isntCurrentTurnPlayer(): bool
    {
        return !$this->isCurrentTurnPlayer();
    }

    public function currentTurnPlayerHand(): Hand
    {
        return $this->hand;
    }

    private function determineFirstTurnPlayer(array $hands): int
    {
        return match($this->size) {
            4 => $this->playerWithStartingCard($hands),
            3 => $this->randomPlayer($hands),
        };
    }

    private function playerWithStartingCard(array $hands): int
    {
        $startingHand = array_filter(
            $hands,
            fn(Hand $hand) => $hand->hasStartingCard(),
        );

        return array_key_first($startingHand);
    }

    private function randomPlayer(array $hands): int
    {
        $startingCard = new Card(suit: Suit::DIAMONDS, rank: Rank::SEVEN);
        $this->board->play($startingCard);

        return array_rand($hands);
    }

    private function playerId(): string
    {
        return Session::get('playerId');
    }

    public function playerKey(): string
    {
        return array_search($this->playerId(), $this->players);
    }
}
