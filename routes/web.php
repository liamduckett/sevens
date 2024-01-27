<?php

use App\Livewire\Game;
use App\Livewire\Lobby;
use App\Livewire\LobbyCreation;
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

Route::get('/', LobbyCreation::class);
Route::get('/lobby', Lobby::class);
Route::get('/game', Game::class);
