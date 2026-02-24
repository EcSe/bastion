<?php

namespace App\Livewire\Executions;

use App\Models\Execution;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?string $statusFilter = null;

    public function filter(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function clearFilter(): void
    {
        $this->statusFilter = null;
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Execution::query()
            ->with(['recipe', 'target', 'requestedBy'])
            ->latest()
            ->orderByDesc('id');

        if (in_array($this->statusFilter, ['queued', 'running', 'success', 'failed', 'cancelled'], true)) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.executions.index', [
            'executions' => $query->paginate(10),
        ]);
    }
}
