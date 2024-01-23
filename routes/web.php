<?php

use App\Livewire\Game;
use App\Livewire\Lobby;
use App\Livewire\SinglePlayerGame;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/single', SinglePlayerGame::class);

Route::get('/game', Game::class);
Route::get('/start', Lobby::class);
