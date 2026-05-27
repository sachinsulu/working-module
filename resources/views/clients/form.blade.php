@extends('layouts.app')

@section('title', $client->exists ? 'Edit Client' : 'Create Client')

@section('content')
<div class="max-w-2xl mx-auto glass-card rounded-3xl border border-slate-800/80 overflow-hidden flex flex-col animate-fadeIn">

    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-900/40 shrink-0">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 {{ $client->exists ? '' : 'animate-pulse' }}"></span>
            <span>{{ $client->exists ? 'Edit Client Details' : 'Create New Client' }}</span>
        </h3>
        <a href="{{ route('admin.clients.index') }}" class="text-slate-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>
    </div>

    <form
        action="{{ $client->exists ? route('admin.clients.update', $client) : route('admin.clients.store') }}"
        method="POST"
        class="flex flex-col flex-1"
    >
        @csrf
        @if($client->exists)
            @method('PUT')
        @endif

        <div class="flex-1 p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Name <span class="text-indigo-400">*</span></label>
                    <input name="name" type="text" required placeholder="Jane Doe" value="{{ old('name', $client->name) }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('name') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Email Address <span class="text-indigo-400">*</span></label>
                    <input name="email" type="email" required placeholder="client@example.com" value="{{ old('email', $client->email) }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('email') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Number</label>
                    <input name="contact_no" type="text" placeholder="+1 555 123 4567" value="{{ old('contact_no', $client->contact_no) }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('contact_no') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Address</label>
                    <input name="address" type="text" placeholder="Street, city, country" value="{{ old('address', $client->address) }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('address') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Status <span class="text-indigo-400">*</span></label>
                    <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition">
                        <option value="active" {{ old('status', $client->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $client->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">
                        Password
                        @if(!$client->exists) <span class="text-indigo-400">*</span> @else <span class="text-slate-500">(Leave blank to keep current)</span> @endif
                    </label>
                    <input name="password" type="password" placeholder="Min. 6 characters" {{ $client->exists ? '' : 'required' }}
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('password') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Confirm Password</label>
                    <input name="password_confirmation" type="password" placeholder="Re-enter password"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('password_confirmation') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="h-20 border-t border-slate-800 px-6 bg-slate-900/40 flex items-center justify-end gap-3 shrink-0">
            <a href="{{ route('admin.clients.index') }}" class="px-4 py-2 border border-slate-800 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-bold transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-500/10 active:scale-95 transition">
                {{ $client->exists ? 'Update Client' : 'Create Client' }}
            </button>
        </div>
    </form>
</div>
@endsection