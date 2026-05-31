<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\ProjectCategory;

class ProjectCategoryManagement extends Component
{
    #[Url(history: true)]
    public string $search = '';

    public function mount()
    {
        abort_unless(
            auth()->user()->can('view projects') ||
            auth()->user()->can('create projects') ||
            auth()->user()->can('edit projects') ||
            auth()->user()->can('delete projects'),
            403
        );
    }

    public function render()
    {
        $categories = ProjectCategory::query()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy('title')
            ->get();

        return view('livewire.admin.project-category-management', [
            'categories' => $categories,
        ]);
    }
}

