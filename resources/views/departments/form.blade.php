@extends('layouts.app')

@section('title', $department->exists ? 'Edit Department' : 'Create Department')

@section('content')
@php
    $serviceRows = old('services', $department->exists
        ? $department->services->map(fn ($service) => ['id' => $service->id, 'title' => $service->title])->values()->toArray()
        : []);

    $serviceErrors = [];

    foreach ($errors->getMessages() as $key => $messages) {
        if (preg_match('/^services\.(\d+)\.title$/', $key, $matches)) {
            $serviceErrors[(int) $matches[1]] = $messages[0];
        }
    }

    if (empty($serviceRows)) {
        $serviceRows = [['id' => null, 'title' => '']];
    }
@endphp

<div class="max-w-2xl mx-auto rounded-2xl border border-slate-800 bg-slate-900 overflow-hidden flex flex-col animate-fadeIn">

    <div class="h-14 flex items-center justify-between px-6 border-b border-slate-800 shrink-0">
        <h3 class="text-sm font-semibold text-white flex items-center gap-2.5">
            <span class="w-2 h-2 rounded-full bg-indigo-500 {{ $department->exists ? '' : 'animate-pulse' }}"></span>
            <span>{{ $department->exists ? 'Edit Department' : 'Create Department' }}</span>
        </h3>
        <a href="{{ route('admin.departments.index') }}" class="w-7 h-7 rounded-lg border border-slate-700 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition text-base leading-none">
            ✕
        </a>
    </div>

    <form
        action="{{ $department->exists ? route('admin.departments.update', $department) : route('admin.departments.store') }}"
        method="POST"
        class="flex flex-col flex-1"
        x-data="{ serviceRows: @js($serviceRows), serviceErrors: @js($serviceErrors) }"
    >
        @csrf
        @if($department->exists)
            @method('PUT')
        @endif

        <div class="flex-1 p-6 space-y-5">
            <div class="space-y-1.5">
                <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-widest block">Department Title <span class="text-indigo-400">*</span></label>
                <input name="title" type="text" required placeholder="e.g. Marketing"
                    value="{{ old('title', $department->title) }}"
                    class="w-full h-10 bg-slate-950 border border-slate-700 rounded-xl px-3.5 text-sm text-slate-200 placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/60 transition" />
                @error('title') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-widest block">Department Head</label>
                <select name="head_user_id" class="w-full h-10 bg-slate-950 border border-slate-700 rounded-xl px-3.5 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/60 transition">
                    <option value="">— None —</option>
                    @foreach($allDeptHeads as $head)
                        <option value="{{ $head->id }}" {{ old('head_user_id', $department->head_user_id) == $head->id ? 'selected' : '' }}>
                            {{ $head->name }}
                        </option>
                    @endforeach
                </select>
                @error('head_user_id') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-3 pt-4 border-t border-slate-800">
                <div class="flex items-center justify-between gap-3">
                    <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-widest block">Services</label>
                    <button
                        type="button"
                        @click="serviceRows.push({ id: null, title: '' })"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-700 bg-slate-800/50 text-slate-400 hover:text-indigo-400 hover:border-indigo-500/50 hover:bg-indigo-500/10 text-xs font-semibold transition shrink-0"
                    >
                        + Add Row
                    </button>
                </div>

                @error('services') <p class="text-[11px] text-rose-400 font-medium">{{ $message }}</p> @enderror

                <template x-for="(service, index) in serviceRows" :key="index">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 px-3 py-2.5">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 space-y-1">
                                <label class="text-[11px] font-semibold text-slate-600 uppercase tracking-widest block">Title</label>
                                <input
                                    type="text"
                                    x-model="service.title"
                                    :name="`services[${index}][title]`"
                                    class="w-full h-9 bg-slate-900 border border-slate-700 rounded-lg px-3 text-sm text-slate-200 placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/60 transition"
                                />
                                <input type="hidden" x-model="service.id" :name="`services[${index}][id]`" />
                                <p class="text-[11px] text-rose-400 font-medium mt-0.5" x-show="serviceErrors[index]" x-text="serviceErrors[index]"></p>
                            </div>

                            <button
                                type="button"
                                @click="serviceRows.splice(index, 1); if (!serviceRows.length) serviceRows.push({ id: null, title: '' })"
                                class="w-8 h-8 rounded-lg border border-rose-500/30 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 hover:border-rose-500/50 hover:text-rose-300 transition flex items-center justify-center shrink-0 text-base leading-none"
                                aria-label="Remove row"
                            >
                                ✕
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="h-16 border-t border-slate-800 px-6 bg-slate-900/60 flex items-center justify-end gap-2 shrink-0">
            <a href="{{ route('admin.departments.index') }}" class="px-4 py-2 border border-slate-700 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-semibold transition">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 active:scale-[0.97] text-white rounded-xl text-xs font-semibold transition">
                {{ $department->exists ? 'Update Department' : 'Create Department' }}
            </button>
        </div>
    </form>
</div>
@endsection