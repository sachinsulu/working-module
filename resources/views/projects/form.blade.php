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
        action="{{ isset($project) ? url('admin/projects/' . $project->id) : route('admin.projects.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="flex flex-col flex-1"
    >
        @csrf
        @if(isset($project))
            @method('PUT')
        @endif

        @if(isset($errors) && $errors->any())
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
                    <input name="project_type" type="text" required placeholder="e.g. Website, Branding, App..."
                        value="{{ old('project_type', $project->project_type ?? '') }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
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
                    <input id="agreement_date" name="agreement_date" type="text" placeholder="Select date..."
                        value="{{ old('agreement_date', isset($project->agreement_date) ? $project->agreement_date->format('Y-m-d') : '') }}"
                        class="datepicker w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('agreement_date') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Start Date</label>
                    <input id="start_date" name="start_date" type="text" placeholder="Select date..."
                        value="{{ old('start_date', isset($project->start_date) ? $project->start_date->format('Y-m-d') : '') }}"
                        class="datepicker w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
                    @error('start_date') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">End Date</label>
                    <input id="end_date" name="end_date" type="text" placeholder="Select date..."
                        value="{{ old('end_date', isset($project->end_date) ? $project->end_date->format('Y-m-d') : '') }}"
                        class="datepicker w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition" />
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

                {{-- Department services map for JS --}}
                @php
                    $deptServicesMap = [];
                    foreach($departments as $dept) {
                        $deptServicesMap[$dept->id] = $dept->services->map(fn($s) => ['id' => $s->id, 'title' => $s->title])->values()->toArray();
                    }
                @endphp

                <div class="space-y-2" id="departments-container">
                    @foreach($departments as $index => $dept)
                        @php
                            $oldDepts  = old('departments', []);
                            $existing  = isset($project) ? $project->departments->firstWhere('id', $dept->id) : null;
                            $isChecked = !empty($oldDepts) ? collect($oldDepts)->contains('id', (string)$dept->id) : (bool)$existing;
                            $oldAmount = !empty($oldDepts) ? (collect($oldDepts)->firstWhere('id', (string)$dept->id)['amount'] ?? '') : ($existing ? $existing->pivot->amount : '');
                            $deptServices = $dept->services;
                        @endphp
                        <div
                            x-data="{ idx: {{ $index }}, initialCheck: {{ $isChecked ? 'true' : 'false' }} }"
                            x-init="selectedDepts[{{ $dept->id }}] = initialCheck"
                            class="flex flex-col gap-2 p-3 rounded-xl border transition"
                            :class="selectedDepts[{{ $dept->id }}] ? 'bg-indigo-500/5 border-indigo-500/25' : 'bg-slate-950 border-slate-800'"
                        >
                            {{-- Top row: checkbox + label + amount --}}
                            <div class="flex items-center gap-4">
                                <input
                                    type="checkbox"
                                    id="dept-{{ $dept->id }}"
                                    x-model="selectedDepts[{{ $dept->id }}]"
                                    class="w-4 h-4 accent-indigo-500 cursor-pointer shrink-0"
                                />
                                <label for="dept-{{ $dept->id }}" class="flex-1 text-sm text-slate-300 font-semibold cursor-pointer select-none">
                                    {{ $dept->title }}
                                    @if($dept->head)
                                        <span class="text-[10px] text-slate-500 ml-1">({{ $dept->head->name }})</span>
                                    @endif
                                </label>

                                <div class="flex items-center gap-2 shrink-0" x-show="selectedDepts[{{ $dept->id }}]" x-cloak>
                                    <input type="hidden" :name="`departments[${idx}][id]`" :disabled="!selectedDepts[{{ $dept->id }}]" value="{{ $dept->id }}">
                                    <label class="text-[10px] text-slate-500 uppercase tracking-wider">Amount</label>
                                    <input
                                        type="number"
                                        :name="`departments[${idx}][amount]`"
                                        :disabled="!selectedDepts[{{ $dept->id }}]"
                                        :required="selectedDepts[{{ $dept->id }}]"
                                        value="{{ $oldAmount !== '' ? $oldAmount : '0' }}"
                                        min="0"
                                        step="0.01"
                                        placeholder="0.00"
                                        class="w-28 bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition"
                                    />
                                </div>
                            </div>

                            {{-- Services for this department (shown when checked) --}}
                            @if($deptServices->isNotEmpty())
                                @php
                                    $selectedServiceIds = old('services',
                                        isset($project) ? $project->services->pluck('id')->toArray() : []
                                    );
                                @endphp
                                <div
                                    x-show="selectedDepts[{{ $dept->id }}]"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="pl-8 pt-1 space-y-1.5"
                                >
                                    <span class="text-[10px] text-slate-500 uppercase tracking-wider block mb-1">Services</span>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($deptServices as $service)
                                            @php $isServiceChecked = in_array($service->id, array_map('intval', (array)$selectedServiceIds)); @endphp
                                            <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border cursor-pointer transition select-none
                                                {{ $isServiceChecked
                                                    ? 'bg-indigo-500/15 border-indigo-500/40 text-indigo-300'
                                                    : 'bg-slate-900 border-slate-700 text-slate-400 hover:border-indigo-500/30 hover:text-slate-300' }}"
                                                x-data="{ checked: {{ $isServiceChecked ? 'true' : 'false' }} }"
                                                :class="checked
                                                    ? 'bg-indigo-500/15 border-indigo-500/40 text-indigo-300'
                                                    : 'bg-slate-900 border-slate-700 text-slate-400 hover:border-indigo-500/30 hover:text-slate-300'"
                                            >
                                                <input
                                                    type="checkbox"
                                                    name="services[]"
                                                    value="{{ $service->id }}"
                                                    x-model="checked"
                                                    {{ $isServiceChecked ? 'checked' : '' }}
                                                    class="w-3 h-3 accent-indigo-500 cursor-pointer"
                                                />
                                                <span class="text-[11px] font-medium">{{ $service->title }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- TEAM MEMBERS per department --}}
            <div class="space-y-3" x-show="Object.values(selectedDepts).some(v => v)" x-cloak>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Team Members </label>

                @foreach($departments as $dept)
                    @php
                        $assignedUsers = isset($project)
                            ? $project->teamMembers->where('pivot.department_id', $dept->id)->pluck('id')->toArray()
                            : [];
                        $deptUsers = collect($users)->filter(function($u) use ($dept) {
                            return strtolower(trim($u->department ?? '')) === strtolower(trim($dept->title ?? ''));
                        });
                    @endphp
                    <div class="rounded-xl border border-slate-800 bg-slate-950 overflow-hidden" x-show="selectedDepts[{{ $dept->id }}]" x-cloak>
                        <div class="px-4 py-2.5 bg-slate-900/60 border-b border-slate-800 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $dept->title }}</span>
                        </div>
                        <div class="p-3 grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @forelse($deptUsers as $user)
                                <label class="flex items-center gap-2 text-sm text-slate-300 cursor-pointer hover:text-white transition">
                                    <input
                                        type="checkbox"
                                        name="teams[{{ $dept->id }}][]"
                                        value="{{ $user->id }}"
                                        {{ in_array($user->id, $assignedUsers) ? 'checked' : '' }}
                                        class="w-3.5 h-3.5 accent-indigo-500"
                                    />
                                    <span class="text-xs truncate flex-1">
                                        {{ $user->name }}
                                    </span>
                                </label>
                            @empty
                                <div class="col-span-full text-xs text-slate-500 italic">No users found in this department.</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- PROJECT BRIEF (content) --}}
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Project Brief / Notes</label>
                <textarea id="project-content" name="content" rows="4" placeholder="Describe the project scope, objectives, or notes…"
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
                        <div class="space-y-1.5" x-data="{ fileName: '' }">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">{{ $label }}</label>
                            <label class="flex flex-col items-center justify-center gap-1.5 p-4 rounded-xl border-2 border-dashed cursor-pointer transition
                                {{ $existing ? 'border-emerald-500/30 bg-emerald-500/5' : 'border-slate-700 bg-slate-950 hover:border-indigo-500/40 hover:bg-indigo-500/5' }}"
                                :class="fileName ? '!border-indigo-500/40 !bg-indigo-500/5' : ''">
                                @if($existing)
                                    <svg x-show="!fileName" class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span x-show="!fileName" class="text-[10px] text-emerald-400 font-semibold">Uploaded — replace</span>
                                @else
                                    <svg x-show="!fileName" class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <span x-show="!fileName" class="text-[10px] text-slate-500">Click to upload PDF</span>
                                @endif
                                <svg x-cloak x-show="fileName" class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-cloak x-show="fileName" x-text="fileName" class="text-[10px] text-indigo-400 font-semibold truncate max-w-[150px] px-1"></span>
                                <input type="file" name="{{ $field }}" accept=".pdf" class="hidden" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" />
                            </label>
                            @if($existing)
                                <a href="{{ asset('storage/' . $existing) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-[10px] text-indigo-300 hover:text-indigo-200 font-semibold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12h3m4 0a8.966 8.966 0 01-2.64 6.36A8.966 8.966 0 0113 21a8.966 8.966 0 01-6.36-2.64A8.966 8.966 0 014 12a8.966 8.966 0 012.64-6.36A8.966 8.966 0 0113 3a8.966 8.966 0 016.36 2.64A8.966 8.966 0 0121 12z"/></svg>
                                    <span>{{ basename($existing) }}</span>
                                </a>
                            @endif
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
    document.addEventListener('DOMContentLoaded', function () {
        const agreementPicker = flatpickr("#agreement_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true,
            onChange: function (selectedDates) {
                startPicker.set('minDate', selectedDates[0] || null);
            }
        });
        const startPicker = flatpickr("#start_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true,
            onChange: function (selectedDates) {
                endPicker.set('minDate', selectedDates[0] || null);
                if (agreementPicker.selectedDates[0] && selectedDates[0] < agreementPicker.selectedDates[0]) {
                    startPicker.clear();
                }
            }
        });
        const endPicker = flatpickr("#end_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true
        });
    });

    function projectForm() {
        return {
            selectedDepts: {},
            initDepartments(existing) {
                // Alpine handles initialization via x-init
            }
        };
    }
</script>
@endsection

