<?php

use App\Livewire\Executions\Form as ExecutionForm;
use App\Livewire\Executions\Index as ExecutionIndex;
use App\Livewire\Recipes\Form as RecipeForm;
use App\Livewire\Recipes\Index as RecipeIndex;
use App\Livewire\Targets\Form as TargetForm;
use App\Livewire\Targets\Index as TargetIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('targets', TargetIndex::class)->name('targets.index');
    Route::livewire('targets/create', TargetForm::class)->name('targets.create');
    Route::livewire('targets/{target}/edit', TargetForm::class)->name('targets.edit');

    Route::livewire('recipes', RecipeIndex::class)->name('recipes.index');
    Route::livewire('recipes/create', RecipeForm::class)->name('recipes.create');
    Route::livewire('recipes/{recipe}/edit', RecipeForm::class)->name('recipes.edit');

    Route::livewire('executions', ExecutionIndex::class)->name('executions.index');
    Route::livewire('executions/create', ExecutionForm::class)->name('executions.create');
});

require __DIR__.'/settings.php';
