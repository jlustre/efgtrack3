<?php

namespace App\Livewire\Dashboard;

use App\Http\Controllers\TaskController;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class MyTasksModal extends Component
{
    public bool $show = false;

    #[On('open-my-tasks-modal')]
    public function open(): void
    {
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function render(TaskController $tasks): View
    {
        $payload = $this->show
            ? $tasks->openTasksByPriorityFor(auth()->user())
            : ['count' => 0, 'items' => []];

        return view('livewire.dashboard.my-tasks-modal', [
            'tasks' => $payload,
        ]);
    }
}
