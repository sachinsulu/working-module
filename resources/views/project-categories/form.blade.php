@extends('layouts.app')

@section('title', $category->exists ? 'Edit Project Category' : 'Create Project Category')

@section('content')
<div class="max-w-2xl mx-auto rounded-2xl border border-slate-800 bg-slate-900 overflow-hidden flex flex-col animate-fadeIn">

    <div class="h-14 flex items-center justify-between px-6 border-b border-slate-800 shrink-0">
        <h3 class="text-sm font-semibold text-white flex items-center gap-2.5">
            <span class="w-2 h-2 rounded-full bg-indigo-500 {{ $category->exists ? '' : 'animate-pulse' }}"></span>
            <span>{{ $category->exists ? 'Edit Project Category' : 'Create Project Category' }}</span>
        </h3>
        <a href="{{ route('admin.project-categories.index') }}" class="w-7 h-7 rounded-lg border border-slate-700 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition text-base leading-none">
            ✕
        </a>
    </div>

    <form
        action="{{ $category->exists ? route('admin.project-categories.update', $category) : route('admin.project-categories.store') }}"
        method="POST"
        class="flex flex-col flex-1"
        x-data="formValidator({
            title: @js(old('title', $category->title ?? ''))
        }, {
            title: [{ type: 'required' }, { type: 'max', value: 255 }]
        })"
        @submit="submit" novalidate
    >
        @csrf
        @if($category->exists)
            @method('PUT')
        @endif

        @if(isset($errors) && $errors->any())
            <div class="mx-6 mt-4 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-300 space-y-1">
                @foreach($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="flex-1 p-6 space-y-5">
            <div class="space-y-1.5">
                <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-widest block">Category Title <span class="text-indigo-400">*</span></label>
                <input name="title" type="text" x-model="title" required placeholder="e.g. Website, Branding, App..."
                    class="w-full h-10 bg-slate-950 border border-slate-700 rounded-xl px-3.5 text-sm text-slate-200 placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500/60 transition"
                    :class="{'border-red-500': errors.title}" />
                <template x-if="errors.title"><p class="text-[11px] text-rose-400 font-medium mt-0.5" x-text="errors.title"></p></template>
                @error('title') <p class="text-[11px] text-rose-400 font-medium mt-0.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="h-16 border-t border-slate-800 px-6 bg-slate-900/60 flex items-center justify-end gap-2 shrink-0">
            <a href="{{ route('admin.project-categories.index') }}" class="px-4 py-2 border border-slate-700 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-semibold transition">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 active:scale-[0.97] text-white rounded-xl text-xs font-semibold transition">
                {{ $category->exists ? 'Update Category' : 'Create Category' }}
            </button>
        </div>
    </form>
</div>

@include('partials.alpine-validation')
@endsection
