<?php

namespace App\Livewire\Admin;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class ClientManagement extends Component
{
    use WithPagination;

    public function mount()
    {
        abort_unless(auth()->user()->can('manage clients'), 403);
    }

    #[Url(history: true)]
    public $search = '';

    public function updated($propertyName)
    {
        if ($propertyName === 'search') {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $client = Client::find($id);
        if ($client) {
            $client->status = $client->status === 'active' ? 'inactive' : 'active';
            $client->save();
        }
    }

    public function render()
    {
        $clients = Client::query()
            ->when($this->search, fn ($query) => $query->where(fn ($subQuery) =>
                $subQuery->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('contact_no', 'like', "%{$this->search}%")
                    ->orWhere('address', 'like', "%{$this->search}%")
            ))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.client-management', [
            'clients' => $clients,
        ]);
    }
}