<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function deactivate(int $recipeId): void
    {
        Recipe::query()->findOrFail($recipeId)->update([
            'is_active' => false,
        ]);

        session()->flash('status', 'recipe-updated');
    }

    public function activate(int $recipeId): void
    {
        Recipe::query()->findOrFail($recipeId)->update([
            'is_active' => true,
        ]);

        session()->flash('status', 'recipe-updated');
    }

    public function render(): View
    {
        return view('livewire.recipes.index', [
            'recipes' => Recipe::query()
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    }
}
