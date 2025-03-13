<?php

use App\Http\Controllers\PatreonController;
use Illuminate\Support\Facades\Route;

Route::get('/patreon', [PatreonController::class, 'index'])->name('patreon.index');
Route::patch('/patreon/update', [PatreonController::class, 'update'])->name('patreon.update');
Route::get('/last-updated', [PatreonController::class, 'lastUpdated'])->name('patreon.lastUpdated');
