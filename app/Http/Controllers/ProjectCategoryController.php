<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProjectCategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:create projects', only: ['create', 'store']),
            new Middleware('permission:edit projects', only: ['edit', 'update']),
            new Middleware('permission:delete projects', only: ['destroy']),
        ];
    }

    public function create()
    {
        $category = new ProjectCategory();

        return view('project-categories.form', compact('category'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:project_categories,title',
        ]);

        $category = ProjectCategory::create($validated);

        return redirect()->route('admin.project-categories.index')
            ->with('message', "Project Category '{$category->title}' created successfully.");
    }

    public function edit(ProjectCategory $projectCategory)
    {
        $category = $projectCategory;

        return view('project-categories.form', compact('category'));
    }

    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:project_categories,title,' . $projectCategory->id,
        ]);

        $projectCategory->update($validated);

        return redirect()->route('admin.project-categories.index')
            ->with('message', "Project Category '{$projectCategory->title}' updated successfully.");
    }

    public function destroy(ProjectCategory $projectCategory)
    {
        $title = $projectCategory->title;
        $projectCategory->delete();

        return redirect()->route('admin.project-categories.index')
            ->with('message', "Project Category '{$title}' was deleted successfully.");
    }
}
