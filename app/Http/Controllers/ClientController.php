<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $clientsQuery = Client::query();

        if ($search) {
            $queryStr = '%' . $search . '%';
            $clientsQuery->where(function ($query) use ($queryStr) {
                $query->where('name', 'like', $queryStr)
                    ->orWhere('email', 'like', $queryStr)
                    ->orWhere('contact_no', 'like', $queryStr)
                    ->orWhere('address', 'like', $queryStr);
            });
        }

        $clients = $clientsQuery->latest()->paginate(10)->withQueryString();

        return view('clients.index', [
            'clients' => $clients,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $client = new Client();

        return view('clients.form', [
            'client' => $client,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'nullable|string',
            'contact_no' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $client = Client::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'address' => $validated['address'] ?? null,
            'contact_no' => $validated['contact_no'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.clients.index')->with('message', "Client '{$client->name}' created successfully.");
    }

    public function edit(Client $client)
    {
        return view('clients.form', [
            'client' => $client,
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($client->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'address' => 'nullable|string',
            'contact_no' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'address' => $validated['address'] ?? null,
            'contact_no' => $validated['contact_no'] ?? null,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $client->update($updateData);

        return redirect()->route('admin.clients.index')->with('message', "Client '{$client->name}' updated successfully.");
    }

    public function destroy(Client $client)
    {
        $name = $client->name;
        $client->delete();

        return redirect()->route('admin.clients.index')->with('message', "Client '{$name}' was deleted successfully.");
    }
}