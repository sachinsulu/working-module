@extends('layouts.app')

@section('title', $role->exists ? 'Edit Role' : 'Create Role')

@section('content')
<div class="max-w-2xl mx-auto glass-card rounded-3xl border border-slate-800/80 overflow-hidden flex flex-col animate-fadeIn">
    
    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-900/40 shrink-0">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 {{ $role->exists ? '' : 'animate-pulse' }}"></span>
            <span>{{ $role->exists ? 'Edit Role Details' : 'Configure New Role' }}</span>
        </h3>
        <a href="{{ route('admin.roles.index') }}" class="text-slate-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>
    </div>

    <form action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST" class="flex flex-col flex-1">
        @csrf
        @if($role->exists)
            @method('PUT')
        @endif

        <div class="flex-1 p-6 space-y-5">
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Role Identifier Name <span class="text-indigo-400">*</span></label>
                <input name="roleName" type="text" required placeholder="e.g. support specialist" 
                    value="{{ old('roleName', $role->name) }}"
                    {{ ($role->exists && $role->name === 'super admin') ? 'disabled' : '' }}
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition disabled:opacity-50 disabled:cursor-not-allowed" />
                @error('roleName') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-3 pt-3 border-t border-slate-800/60" x-data="{ selectedPermissions: {{ json_encode(old('selectedPermissions', $role->exists ? $role->permissions->pluck('name')->toArray() : [])) }} }">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Define Permission Grants</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($permissions as $perm)
                        <label class="relative flex items-center justify-between p-3 rounded-xl border border-slate-800 bg-slate-950/40 text-slate-400 hover:border-slate-700 hover:text-slate-200 cursor-pointer select-none transition duration-300"
                            :class="selectedPermissions.includes('{{ $perm->name }}') ? 'border-indigo-500 bg-indigo-500/5 text-indigo-200' : 'border-slate-800 bg-slate-950/40 text-slate-400'">
                            <span class="text-xs font-bold capitalize">{{ $perm->name }}</span>
                            <input type="checkbox" name="selectedPermissions[]" value="{{ $perm->name }}" x-model="selectedPermissions" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500/40 bg-slate-950 border-slate-800">
                        </label>
                    @endforeach
                </div>
                @error('selectedPermissions') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="h-20 border-t border-slate-800 px-6 bg-slate-900/40 flex items-center justify-end gap-3 shrink-0">
            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 border border-slate-800 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-bold transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-500/10 hover:shadow-indigo-500/20 active:scale-95 transition">
                {{ $role->exists ? 'Update Permissions' : 'Configure Role' }}
            </button>
        </div>
    </form>
</div>
@endsection
