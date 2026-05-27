@extends('layouts.app')

@section('title', isset($project) ? 'Edit Project' : 'Create Project')

@section('content')
<div
    class="max-w-4xl mx-auto glass-card rounded-3xl border border-slate-800/80 overflow-hidden flex flex-col animate-fadeIn"
    x-data="projectForm()"
    x-init="initDepartments(@js(isset($project) ? $project->departments->map(fn($d) => ['id' => $d->id, 'amount' => $d->pivot->amount])->values() : []))"
>

    {{-- HEADER --}}
    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-900/40 shrink-0">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 {{ isset($project) ? '' : 'animate-pulse' }}"></span>
            <span>{{ isset($project) ? 'Edit Project: '.$project->project_name : 'Create New Project' }}</span>
        </h3>
        <a href="{{ route('admin.projects.index') }}" class="text-slate-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>
    </div>

    <form
        action="{{ isset($project) ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="flex flex-col flex-1"
    >
        @csrf
        @if(isset($project))
            @method('PUT')
        @endif

        @if($errors->any())
            <div class="mx-6 mt-4 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-300 space-y-1">
                @foreach($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="flex-1 p-6 space-y-6">

            {{-- ROW 1: Project Name + Client --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Project Name <span class="text-indigo-400">*</span></label>
                    <input name="project_name" type="text" required placeholder="My Awesome Project"
                        value="{{ old('project_name', $project->project_name ?? '') }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('project_name') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Client <span class="text-indigo-400">*</span></label>
                    <select name="client_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition">
                        <option value="">Select client…</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $project->client_id ?? '') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- ROW 2: Type + Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Project Type <span class="text-indigo-400">*</span></label>
                    <select name="project_type" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition">
                        <option value="">Select type…</option>
                        @foreach($projectTypes as $type)
                            <option value="{{ $type }}" {{ old('project_type', $project->project_type ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('project_type') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Status</label>
                    <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition">
                        <option value="active"  {{ old('status', $project->status ?? 'active') === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $project->status ?? '')       === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- ROW 3: Dates --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Agreement Date</label>
                    <input name="agreement_date" type="date"
                        value="{{ old('agreement_date', isset($project->agreement_date) ? $project->agreement_date->format('Y-m-d') : '') }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition [color-scheme:dark]" />
                    @error('agreement_date') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Start Date</label>
                    <input name="start_date" type="date"
                        value="{{ old('start_date', isset($project->start_date) ? $project->start_date->format('Y-m-d') : '') }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition [color-scheme:dark]" />
                    @error('start_date') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">End Date</label>
                    <input name="end_date" type="date"
                        value="{{ old('end_date', isset($project->end_date) ? $project->end_date->format('Y-m-d') : '') }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition [color-scheme:dark]" />
                    @error('end_date') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- DEPARTMENTS + AMOUNTS --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Departments <span class="text-indigo-400">*</span></label>
                    <span class="text-[10px] text-slate-500">Select at least one and set the allocated amount</span>
                </div>
                @error('departments') <p class="text-[11px] text-rose-400 font-medium">{{ $message }}</p> @enderror

                <div class="space-y-2" id="departments-container">
                    @foreach($departments as $index => $dept)
                        @php
                            $oldDepts  = old('departments', []);
                            $existing  = isset($project) ? $project->departments->firstWhere('id', $dept->id) : null;
                            $isChecked = !empty($oldDepts) ? collect($oldDepts)->contains('id', (string)$dept->id) : (bool)$existing;
                            $oldAmount = !empty($oldDepts) ? (collect($oldDepts)->firstWhere('id', (string)$dept->id)['amount'] ?? '') : ($existing ? $existing->pivot->amount : '');
                        @endphp
                        <div
                            x-data="{ checked: {{ $isChecked ? 'true' : 'false' }}, idx: {{ $index }} }"
                            class="flex items-center gap-4 p-3 rounded-xl border transition"
                            :class="checked ? 'bg-indigo-500/5 border-indigo-500/25' : 'bg-slate-950 border-slate-800'"
                        >
                            <input
                                type="checkbox"
                                id="dept-{{ $dept->id }}"
                                x-model="checked"
                                class="w-4 h-4 accent-indigo-500 cursor-pointer"
                            />
                            <label for="dept-{{ $dept->id }}" class="flex-1 text-sm text-slate-300 font-semibold cursor-pointer select-none">
                                {{ $dept->title }}
                                @if($dept->head)
                                    <span class="text-[10px] text-slate-500 ml-1">({{ $dept->head->name }})</span>
                                @endif
                            </label>

                            <div class="flex items-center gap-2 shrink-0" x-show="checked">
                                <input type="hidden" :name="`departments[${idx}][id]`" :disabled="!checked" value="{{ $dept->id }}">
                                <label class="text-[10px] text-slate-500 uppercase tracking-wider">Amount</label>
                                <input
                                    type="number"
                                    :name="`departments[${idx}][amount]`"
                                    :disabled="!checked"
                                    value="{{ $oldAmount }}"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="w-28 bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- TEAM MEMBERS per department --}}
            <div class="space-y-3">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Team Members <span class="text-slate-600">(optional)</span></label>

                @foreach($departments as $dept)
                    @php
                        $assignedUsers = isset($project)
                            ? $project->teamMembers->where('pivot.department_id', $dept->id)->pluck('id')->toArray()
                            : [];
                    @endphp
                    <div class="rounded-xl border border-slate-800 bg-slate-950 overflow-hidden">
                        <div class="px-4 py-2.5 bg-slate-900/60 border-b border-slate-800 text-xs font-bold text-slate-400 uppercase tracking-wider">
                            {{ $dept->title }}
                        </div>
                        <div class="p-3 grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($users as $user)
                                <label class="flex items-center gap-2 text-sm text-slate-300 cursor-pointer hover:text-white transition">
                                    <input
                                        type="checkbox"
                                        name="teams[{{ $dept->id }}][]"
                                        value="{{ $user->id }}"
                                        {{ in_array($user->id, $assignedUsers) ? 'checked' : '' }}
                                        class="w-3.5 h-3.5 accent-indigo-500"
                                    />
                                    <span class="text-xs truncate">{{ $user->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- PROJECT BRIEF (content) --}}
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Project Brief / Notes</label>
                <textarea name="content" rows="4" placeholder="Describe the project scope, objectives, or notes…"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition resize-none">{{ old('content', $project->content ?? '') }}</textarea>
                @error('content') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>

            {{-- FILE UPLOADS --}}
            <div class="space-y-3">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Documents <span class="text-slate-600">(PDF only, max 5MB each)</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    @foreach([
                        ['logo', 'Logo / Brand Mark', $project->logo_path ?? null],
                        ['brand_guidelines', 'Brand Guidelines', $project->brand_guidelines_path ?? null],
                        ['fact_sheet', 'Fact Sheet', $project->fact_sheet_path ?? null],
                    ] as [$field, $label, $existing])
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">{{ $label }}</label>
                            <label class="flex flex-col items-center justify-center gap-1.5 p-4 rounded-xl border-2 border-dashed cursor-pointer transition
                                {{ $existing ? 'border-emerald-500/30 bg-emerald-500/5' : 'border-slate-700 bg-slate-950 hover:border-indigo-500/40 hover:bg-indigo-500/5' }}">
                                @if($existing)
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-[10px] text-emerald-400 font-semibold">Uploaded — replace</span>
                                @else
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <span class="text-[10px] text-slate-500">Click to upload PDF</span>
                                @endif
                                <input type="file" name="{{ $field }}" accept=".pdf" class="hidden" />
                            </label>
                            @error($field) <p class="text-[11px] text-rose-400 font-medium">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                </div>
            </div>

        </div>

        {{-- FOOTER ACTIONS --}}
        <div class="h-20 border-t border-slate-800 px-6 bg-slate-900/40 flex items-center justify-end gap-3 shrink-0">
            <a href="{{ route('admin.projects.index') }}" class="px-4 py-2 border border-slate-800 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-bold transition">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-500/10 active:scale-95 transition">
                {{ isset($project) ? 'Update Project' : 'Create Project' }}
            </button>
        </div>
    </form>
</div>

<script>
    function projectForm() {
        return {
            initDepartments(existing) {
                // Pre-check checkboxes and amounts for existing project
                // Alpine handles this via x-data on each row
            }
        };
    }
</script>
@endsection
