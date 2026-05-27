<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_create_persists_pdf_uploads_and_shows_them_on_edit(): void
    {
        Storage::fake('public');
        $this->withoutMiddleware(PermissionMiddleware::class);

        $user = User::factory()->create([
            'name' => 'Project Admin',
            'email' => 'project-admin@example.com',
            'password' => bcrypt('password'),
        ]);
        Permission::findOrCreate('manage projects');
        $user->givePermissionTo('manage projects');

        $client = Client::create([
            'name' => 'Acme Client',
            'email' => 'acme-client@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $department = Department::create([
            'title' => 'Design',
            'head_user_id' => null,
        ]);

        $payload = [
            'project_name' => 'New Website',
            'client_id' => $client->id,
            'project_type' => 'Digital',
            'agreement_date' => '2026-05-27',
            'start_date' => '2026-05-28',
            'end_date' => '2026-06-30',
            'content' => 'Project brief content',
            'status' => 'active',
            'departments' => [
                ['id' => $department->id, 'amount' => 2500],
            ],
            'logo' => UploadedFile::fake()->create('logo.pdf', 50, 'application/pdf'),
            'brand_guidelines' => UploadedFile::fake()->create('brand-guidelines.pdf', 50, 'application/pdf'),
            'fact_sheet' => UploadedFile::fake()->create('fact-sheet.pdf', 50, 'application/pdf'),
        ];

        $response = $this->actingAs($user)->post(route('admin.projects.store'), $payload);

        $response->assertStatus(302);

        $project = Project::where('project_name', 'New Website')->firstOrFail();

        $response->assertRedirect(route('admin.projects.edit', ['project' => $project->id]));
        $response->assertSessionHas('message', "Project 'New Website' created successfully.");

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'logo_path' => "projects/{$project->id}/logo.pdf",
            'brand_guidelines_path' => "projects/{$project->id}/brand_guidelines.pdf",
            'fact_sheet_path' => "projects/{$project->id}/fact_sheet.pdf",
        ]);

        Storage::disk('public')->assertExists("projects/{$project->id}/logo.pdf");
        Storage::disk('public')->assertExists("projects/{$project->id}/brand_guidelines.pdf");
        Storage::disk('public')->assertExists("projects/{$project->id}/fact_sheet.pdf");

        $this->assertDatabaseHas('project_departments', [
            'project_id' => $project->id,
            'department_id' => $department->id,
            'amount' => 2500,
        ]);

        $editResponse = $this->actingAs($user)->get(route('admin.projects.edit', ['project' => $project->id]));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('logo.pdf', false);
        $editResponse->assertSee('brand-guidelines.pdf', false);
        $editResponse->assertSee('fact-sheet.pdf', false);
    }
}
