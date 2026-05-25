@extends('layouts.app')

@section('title', $user->exists ? 'Edit Member' : 'Add Member')

@section('content')
<div class="max-w-2xl mx-auto glass-card rounded-3xl border border-slate-800/80 overflow-hidden flex flex-col animate-fadeIn">
    
    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-900/40 shrink-0">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 {{ $user->exists ? '' : 'animate-pulse' }}"></span>
            <span>{{ $user->exists ? 'Edit Member Details' : 'Add New Member' }}</span>
        </h3>
        <a href="{{ route('admin.users.index') }}" class="text-slate-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>
    </div>

    <form action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" class="flex flex-col flex-1">
        @csrf
        @if($user->exists)
            @method('PUT')
        @endif

        <div class="flex-1 p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Full Name <span class="text-indigo-400">*</span></label>
                    <input name="name" type="text" required placeholder="John Doe" value="{{ old('name', $user->name) }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('name') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Email Address <span class="text-indigo-400">*</span></label>
                    <input name="email" type="email" required placeholder="john@example.com" value="{{ old('email', $user->email) }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('email') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Employee ID <span class="text-indigo-400">*</span></label>
                    <input name="employee_id" type="text" required placeholder="EMP-001" value="{{ old('employee_id', $user->employee_id) }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('employee_id') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Contact No.</label>
                    <input name="contact_no" type="text" placeholder="+977 98XXXXXXXX" value="{{ old('contact_no', $user->contact_no) }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('contact_no') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">
                        Password 
                        @if(!$user->exists) <span class="text-indigo-400">*</span> @else <span class="text-slate-500">(Leave blank to keep current)</span> @endif
                    </label>
                    <input name="password" type="password" placeholder="Min. 8 characters" {{ $user->exists ? '' : 'required' }}
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('password') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Address</label>
                <textarea name="address" rows="2" placeholder="123 Main St, Kathmandu"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition resize-none">{{ old('address', $user->address) }}</textarea>
                @error('address') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Assigned Role</label>
                <select name="roles[]" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition">
                    <option value="">Select a role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('roles.0', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('roles') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Department</label>
                <select name="department" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition">
                    <option value="">Select a department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->title }}" {{ old('department', $user->department) == $dept->title ? 'selected' : '' }}>{{ $dept->title }}</option>
                    @endforeach
                </select>
                @error('department') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="h-20 border-t border-slate-800 px-6 bg-slate-900/40 flex items-center justify-end gap-3 shrink-0">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-slate-800 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-bold transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-500/10 active:scale-95 transition">
                {{ $user->exists ? 'Update Member' : 'Create Member' }}
            </button>
        </div>
    </form>
</div>
@endsection
