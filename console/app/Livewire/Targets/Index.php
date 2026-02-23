<?php

namespace App\Livewire\Targets;

use App\Models\Target;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function deactivate(int $targetId): void
    {
        Target::query()->findOrFail($targetId)->update([
            'is_active' => false,
        ]);

        session()->flash('status', 'target-updated');
    }

    public function activate(int $targetId): void
    {
        Target::query()->findOrFail($targetId)->update([
            'is_active' => true,
        ]);

        session()->flash('status', 'target-updated');
    }

    public function render(): View
    {
        return view('livewire.targets.index', [
            'targets' => Target::query()
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    }
}
