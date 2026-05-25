@extends('layouts.app')

@section('title', $department->exists ? 'Edit Department' : 'Create Department')

@section('content')
<div class="max-w-2xl mx-auto glass-card rounded-3xl border border-slate-800/80 overflow-hidden flex flex-col animate-fadeIn">
    
    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-900/40 shrink-0">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 {{ $department->exists ? '' : 'animate-pulse' }}"></span>
            <span>{{ $department->exists ? 'Edit Department' : 'Create Department' }}</span>
        </h3>
        <a href="{{ route('admin.departments.index') }}" class="text-slate-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>
    </div>

    <form action="{{ $department->exists ? route('admin.departments.update', $department) : route('admin.departments.store') }}" method="POST" class="flex flex-col flex-1">
        @csrf
        @if($department->exists)
            @method('PUT')
        @endif

        <div class="flex-1 p-6 space-y-5">
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Department Title <span class="text-indigo-400">*</span></label>
                <input name="title" type="text" required placeholder="e.g. Marketing" 
                    value="{{ old('title', $department->title) }}"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                @error('title') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Department Head</label>
                <select name="head_user_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition">
                    <option value="">— None —</option>
                    @foreach($allDeptHeads as $head)
                        <option value="{{ $head->id }}" {{ old('head_user_id', $department->head_user_id) == $head->id ? 'selected' : '' }}>
                            {{ $head->name }}
                        </option>
                    @endforeach
                </select>
                @error('head_user_id') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="h-20 border-t border-slate-800 px-6 bg-slate-900/40 flex items-center justify-end gap-3 shrink-0">
            <a href="{{ route('admin.departments.index') }}" class="px-4 py-2 border border-slate-800 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-bold transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-500/10 hover:shadow-indigo-500/20 active:scale-95 transition">
                {{ $department->exists ? 'Update Department' : 'Create Department' }}
            </button>
        </div>
    </form>
</div>
@endsection
