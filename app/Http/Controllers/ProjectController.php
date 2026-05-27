<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        return view('projects.index');
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $departments = Department::with('head')->orderBy('title')->get();
        $users = User::orderBy('name')->get();

        return view('projects.form', compact('clients','departments','users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:191',
            'client_id' => 'required|exists:clients,id',
            'project_type' => 'required|string',
            'agreement_date' => 'nullable|date',
            'start_date' => 'nullable|date|after_or_equal:agreement_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'content' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'departments' => 'required|array|min:1',
            'departments.*.id' => 'required|exists:departments,id',
            'departments.*.amount' => 'required|numeric|min:0',
            'teams' => 'nullable|array',
            'logo' => 'nullable|mimes:pdf|max:5120',
            'brand_guidelines' => 'nullable|mimes:pdf|max:5120',
            'fact_sheet' => 'nullable|mimes:pdf|max:5120',
        ]);

        $projectData = Arr::only($validated, [
            'project_name',
            'client_id',
            'project_type',
            'agreement_date',
            'start_date',
            'end_date',
            'content',
        ]);
        $projectData['status'] = $validated['status'] ?? 'active';

        DB::beginTransaction();
        try {
            $project = Project::create($projectData);

            // attach departments with amounts
            $attach = [];
            foreach ($validated['departments'] as $dept) {
                $attach[$dept['id']] = ['amount' => $dept['amount']];
            }
            $project->departments()->sync($attach);

            // attach team members (project_department_teams)
            if ($request->filled('teams')) {
                $teamAttach = [];
                foreach ($request->input('teams') as $departmentId => $userIds) {
                    foreach ($userIds as $userId) {
                        $teamAttach[] = [
                            'project_id' => $project->id,
                            'department_id' => $departmentId,
                            'user_id' => $userId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($teamAttach)) {
                    DB::table('project_department_teams')->insert($teamAttach);
                }
            }

            // handle uploads
            $basePath = "projects/{$project->id}";
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->storeAs($basePath, 'logo.pdf', 'public');
                $project->logo_path = $path;
            }
            if ($request->hasFile('brand_guidelines')) {
                $path = $request->file('brand_guidelines')->storeAs($basePath, 'brand_guidelines.pdf', 'public');
                $project->brand_guidelines_path = $path;
            }
            if ($request->hasFile('fact_sheet')) {
                $path = $request->file('fact_sheet')->storeAs($basePath, 'fact_sheet.pdf', 'public');
                $project->fact_sheet_path = $path;
            }

            $project->save();

            DB::commit();

            return redirect()->route('admin.projects.edit', ['project' => $project->id])->with('message', "Project '{$project->project_name}' created successfully.");
        } catch (\Throwable $e) {
            DB::rollBack();
            // cleanup files created in storage if any
            if (isset($project) && isset($basePath)) {
                Storage::disk('public')->deleteDirectory($basePath);
            }
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(Project $project)
    {
        $clients = Client::orderBy('name')->get();
        $departments = Department::with('head')->orderBy('title')->get();
        $users = User::orderBy('name')->get();

        // load pivots and selected teams
        $project->load('departments', 'teamMembers');

        return view('projects.form', compact('project','clients','departments','users'));
    }

    public function update(Request $request, Project $project)
    {
        \Illuminate\Support\Facades\Log::info('Upload debug before validation', [
            'request_logo' => $request->input('logo'),
            'has_logo_file' => $request->hasFile('logo'),
            'files' => $request->allFiles(),
            'php_files' => $_FILES ?? []
        ]);

        $validated = $request->validate([
            'project_name' => 'required|string|max:191',
            'client_id' => 'required|exists:clients,id',
            'project_type' => 'required|string',
            'agreement_date' => 'nullable|date',
            'start_date' => 'nullable|date|after_or_equal:agreement_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'content' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'departments' => 'required|array|min:1',
            'departments.*.id' => 'required|exists:departments,id',
            'departments.*.amount' => 'required|numeric|min:0',
            'teams' => 'nullable|array',
            'logo' => 'nullable|mimes:pdf|max:5120',
            'brand_guidelines' => 'nullable|mimes:pdf|max:5120',
            'fact_sheet' => 'nullable|mimes:pdf|max:5120',
        ]);

        $projectData = Arr::only($validated, [
            'project_name',
            'client_id',
            'project_type',
            'agreement_date',
            'start_date',
            'end_date',
            'content',
        ]);
        $projectData['status'] = $validated['status'] ?? $project->status;

        DB::beginTransaction();
        try {
            $project->update($projectData);

            // sync departments
            $attach = [];
            foreach ($validated['departments'] as $dept) {
                $attach[$dept['id']] = ['amount' => $dept['amount']];
            }
            $project->departments()->sync($attach);

            // rebuild team assignments
            DB::table('project_department_teams')->where('project_id', $project->id)->delete();
            if ($request->filled('teams')) {
                $teamAttach = [];
                foreach ($request->input('teams') as $departmentId => $userIds) {
                    foreach ($userIds as $userId) {
                        $teamAttach[] = [
                            'project_id' => $project->id,
                            'department_id' => $departmentId,
                            'user_id' => $userId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($teamAttach)) {
                    DB::table('project_department_teams')->insert($teamAttach);
                }
            }

            // handle uploads (replace existing)
            $basePath = "projects/{$project->id}";
            if ($request->hasFile('logo')) {
                if ($project->logo_path) {
                    Storage::disk('public')->delete($project->logo_path);
                }
                $path = $request->file('logo')->storeAs($basePath, 'logo.pdf', 'public');
                $project->logo_path = $path;
            }
            if ($request->hasFile('brand_guidelines')) {
                if ($project->brand_guidelines_path) {
                    Storage::disk('public')->delete($project->brand_guidelines_path);
                }
                $path = $request->file('brand_guidelines')->storeAs($basePath, 'brand_guidelines.pdf', 'public');
                $project->brand_guidelines_path = $path;
            }
            if ($request->hasFile('fact_sheet')) {
                if ($project->fact_sheet_path) {
                    Storage::disk('public')->delete($project->fact_sheet_path);
                }
                $path = $request->file('fact_sheet')->storeAs($basePath, 'fact_sheet.pdf', 'public');
                $project->fact_sheet_path = $path;
            }

            $project->save();

            DB::commit();

            return redirect()->route('admin.projects.edit', ['project' => $project->id])->with('message', "Project '{$project->project_name}' updated successfully.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Project $project)
    {
        // remove files
        if ($project->logo_path || $project->brand_guidelines_path || $project->fact_sheet_path) {
            Storage::disk('public')->deleteDirectory("projects/{$project->id}");
        }
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }
}
