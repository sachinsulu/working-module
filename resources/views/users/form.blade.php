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

    @php
        if (auth()->user()->hasRole('dept head')) {
            $selectedRole = 'team';
            $departmentVal = 'auto-assigned';
        } else {
            $selectedRole = old('roles.0', $user->roles->first()?->name ?? '');
            $departmentVal = old('department', $user->department ?? '');
        }
    @endphp

    <form
        action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}"
        method="POST"
        class="flex flex-col flex-1"
        x-data="formValidator({
            name: @js(old('name', $user->name ?? '')),
            email: @js(old('email', $user->email ?? '')),
            employee_id: @js(old('employee_id', $user->employee_id ?? '')),
            contact_no: @js(old('contact_no', $user->contact_no ?? '')),
            status: @js(old('status', $user->status ?? 'active')),
            address: @js(old('address', $user->address ?? '')),
            selectedRole: @js($selectedRole),
            department: @js($departmentVal),
            password: '',
            password_confirmation: ''
        }, {
            name: [{ type: 'required' }, { type: 'max', value: 255 }],
            email: [{ type: 'required' }, { type: 'email' }, { type: 'max', value: 255 }],
            employee_id: [{ type: 'required' }, { type: 'max', value: 50 }],
            status: [{ type: 'required' }],
            department: { requiredIf: { field: 'selectedRole', value: 'team', message: 'Department is required for team members.' } },
            password: [
                { type: 'required', condition: () => !@js($user->exists) },
                { type: 'min', value: 6 }
            ],
            password_confirmation: [
                { type: 'confirmed', target: 'password' }
            ]
        })"
        @submit="submit" novalidate
    >
        @csrf
        @if($user->exists)
            @method('PUT')
        @endif

        <div class="flex-1 p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Full Name <span class="text-indigo-400">*</span></label>
                    <input name="name" type="text" x-model="name" required placeholder="John Doe"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition"
                        :class="{'border-red-500': errors.name}" />
                    <template x-if="errors.name"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.name"></p></template>
                    @error('name') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Email Address <span class="text-indigo-400">*</span></label>
                    <input name="email" type="email" x-model="email" required placeholder="john@example.com"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition"
                        :class="{'border-red-500': errors.email}" />
                    <template x-if="errors.email"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.email"></p></template>
                    @error('email') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Employee ID <span class="text-indigo-400">*</span></label>
                    <input name="employee_id" type="text" x-model="employee_id" required placeholder="EMP-001"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition"
                        :class="{'border-red-500': errors.employee_id}" />
                    <template x-if="errors.employee_id"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.employee_id"></p></template>
                    @error('employee_id') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Contact No.</label>
                    <input name="contact_no" type="text" x-model="contact_no" placeholder="+977 98XXXXXXXX"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition"
                        :class="{'border-red-500': errors.contact_no}" />
                    <template x-if="errors.contact_no"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.contact_no"></p></template>
                    @error('contact_no') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Status <span class="text-indigo-400">*</span></label>
                    <select name="status" x-model="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" :class="{'border-red-500': errors.status}">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <template x-if="errors.status"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.status"></p></template>
                    @error('status') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Address</label>
                <textarea name="address" x-model="address" rows="2" placeholder="123 Main St, Kathmandu"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition resize-none"
                    :class="{'border-red-500': errors.address}"></textarea>
                <template x-if="errors.address"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.address"></p></template>
                @error('address') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Assigned Role</label>
                @if(auth()->user()->hasRole('dept head'))
                    <select
                        name="roles[]"
                        class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-400 cursor-not-allowed"
                        disabled
                    >
                        <option value="team" selected>team</option>
                    </select>
                    <input type="hidden" name="roles[]" value="team">
                @else
                    <select
                        name="roles[]"
                        x-model="selectedRole"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition"
                    >
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $selectedRole === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                @endif
                <template x-if="errors.roles"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.roles"></p></template>
                @error('roles') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>

            @if(!auth()->user()->hasRole('dept head'))
                <div class="space-y-2" x-cloak x-show="selectedRole === 'team'">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Department</label>
                    <select name="department" x-model="department" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" :class="{'border-red-500': errors.department}">
                        <option value="">Select a department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->title }}">{{ $dept->title }}</option>
                        @endforeach
                    </select>
                    <template x-if="errors.department"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.department"></p></template>
                    @error('department') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="space-y-1 sm:col-span-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">
                    Password 
                    @if(!$user->exists) <span class="text-indigo-400">*</span> @else <span class="text-slate-500">(Leave blank to keep current)</span> @endif
                </label>
                <input name="password" type="password" x-model="password" placeholder="Min. 8 characters"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition"
                    :class="{'border-red-500': errors.password}" />
                <template x-if="errors.password"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.password"></p></template>
                @error('password') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1 sm:col-span-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Confirm Password</label>
                <input name="password_confirmation" type="password" x-model="password_confirmation" placeholder="Re-enter password"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition"
                    :class="{'border-red-500': errors.password_confirmation}" />
                <template x-if="errors.password_confirmation"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.password_confirmation"></p></template>
                @error('password_confirmation') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
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

@include('partials.alpine-validation')
@endsection
