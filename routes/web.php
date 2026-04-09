<?php

use App\Livewire\DocumentDetail;
use App\Livewire\DocumentList;
use App\Livewire\Scanner;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', DocumentList::class)->name('documents.index');
Route::get('/scan', Scanner::class)->name('scanner');
Route::get('/documents/{document}', DocumentDetail::class)->name('documents.show');
Route::get('/settings', Settings::class)->name('settings');
