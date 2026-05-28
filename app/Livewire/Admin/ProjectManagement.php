<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class ProjectManagement extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $filterStatus = '';

    #[Url(history: true)]
    public string $filterType = '';

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'filterStatus', 'filterType'])) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->search      = '';
        $this->filterStatus = '';
        $this->filterType   = '';
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $project = Project::find($id);
        if ($project) {
            $project->status = $project->status === 'active' ? 'inactive' : 'active';
            $project->save();
        }
    }

    public function render()
    {
        $projects = Project::with('client')
            ->when($this->search, fn ($q) => $q->where(fn ($sub) =>
                $sub->where('project_name', 'like', "%{$this->search}%")
                    ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$this->search}%"))
            ))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType,   fn ($q) => $q->where('project_type', $this->filterType))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.project-management', [
            'projects'     => $projects,
            'projectTypes' => Project::select('project_type')->distinct()->whereNotNull('project_type')->pluck('project_type')->filter()->toArray(),
        ]);
    }
}
