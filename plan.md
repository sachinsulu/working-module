## Plan: Project Module

Build a new admin Project module that follows the existing Laravel pattern used by clients and departments, with explicit storage for relational selections, live-derived department heads (not stored on project), department-scoped team checkboxes, a TinyMCE content editor, and three PDF-only uploads. The implementation should stay aligned with the current admin CRUD style rather than introducing a new architecture.

**Steps**
1. Define the project schema and relationships, depends on nothing.
   - Create the `projects` table with exact fields: `project_name`, `client_id`, `project_type`, `agreement_date`, `start_date`, `end_date`, `content`, `logo_path`, `brand_guidelines_path`, `fact_sheet_path`, and `status` (nullable enum or string, e.g. `active`/`inactive`, defaulting to `active`).
   - Add a `Project` model and relationships to `Client`, `Department`, and the existing user records used for team members.
   - Add two pivot tables: `project_departments` (stores `project_id`, `department_id`, `amount`) and `project_department_teams` (stores `project_id`, `department_id`, `user_id`).
   - The `amount` field is stored per selected department (not per service), as a line-item amount tied to each department assignment.
   - Department heads are **not stored on the project**. They are always derived live via `$project->departments()->with('head')`. If the dept head changes later, the current head is shown — this is the intended behaviour.
   - The `project_department_teams` pivot acts as the implicit team snapshot: it records exactly who was checked at save time and is only updated when an admin explicitly edits the project. No separate snapshot table is needed.

2. Add the admin route and controller layer, depends on step 1.
   - Create a `ProjectController` with the same CRUD flow used by the existing admin modules.
   - Wire resource routes in the admin route file with the same permission-gated pattern used elsewhere.
   - Validate `project_name`, `client_id`, `project_type`, `agreement_date`, `start_date`, `end_date`, `content`, `status`, selected departments, per-department `amount`, selected department teams, and the three PDF uploads.
   - Enforce a maximum file size of **5MB per PDF** for the logo, brand guidelines, and fact sheet uploads at both validation and ideally at the Nginx/server level.
   - Use a transaction for create and update so the project row, pivots, selected team assignments, and uploaded files stay consistent.

3. Build the project form UI, depends on steps 1 and 2.
   - Add fields for project name, client select, project type (enum select: e.g. `Branding`, `Digital`, `Print`, `Other`), agreement date, start date, end date, a `status` toggle (active/inactive), and a `content` textarea enhanced with TinyMCE.
   - Add a multi-select for departments. Beside each selected department, render an `amount` text input so each department carries its own amount.
   - Render a read-only department head field that lists the heads for all currently selected departments, derived live from the department records (not stored on project).
   - Under each selected department, render its team users as checkboxes so multiple team members can be selected from each department.
   - Add three upload controls limited to PDF files only (max 5MB each): logo, brand guidelines, and fact sheet.
   - **Edit mode pre-population**: when editing an existing project, restore all previously saved selections — selected departments with their stored amounts, derived department heads, per-department team checkbox states, and existing PDF file paths with a download link in place of the upload control. TinyMCE must receive the stored `content` value on load.

4. Add the listing and screen flow, depends on steps 1 and 2.
   - Create a Project index/list component that matches the existing module style if the app keeps a Livewire admin index pattern.
   - Show the main project fields, including client, dates, departments, status badge, and a toggle to switch status inline.
   - Add create, edit, show, and delete entry points using the same UX patterns already used in the repo.

5. Implement the relational behavior and editor behavior, depends on steps 1 through 3.
   - When departments change, load matching department heads (live from dept records) and team users from those departments only.
   - Show all dept heads simultaneously when multiple departments are selected (read-only, derived — not stored).
   - Keep the team checkbox groups separated by department so users can tell which team belongs to which department.
   - Keep the per-department amount input visible and bound to its department row; removing a department also removes its amount.
   - Initialize TinyMCE for the `content` field in the app JS bundle and preserve old form input on validation failures.
   - Enforce PDF-only upload validation (max 5MB) for all three documents and store the resulting paths under a consistent directory (e.g. `storage/app/public/projects/{id}/`).
   - On edit load, re-attach existing file paths so the user sees the current uploaded file without being forced to re-upload.

6. Add permissions, seeding, and navigation, depends on steps 1 through 5.
   - Add a `manage projects` permission to match the existing access-control scheme.
   - Seed the permission in `PermissionSeeder.php` alongside the existing permissions (`manage clients`, `manage users`, etc.) and assign it to the super admin role.
   - Update navigation or dashboards so the module is discoverable from the main admin UI.

**Relevant files**
- `app/Models/Project.php` — project model and relationships.
- `database/migrations/*_create_projects_table.php` — project storage schema including status column.
- `database/migrations/*_create_project_departments_table.php` — pivot with `department_id`, `project_id`, and `amount` per department.
- `database/migrations/*_create_project_teams_table.php` — pivot for selected team users per department.
- `app/Http/Controllers/ProjectController.php` — CRUD, validation (including 5MB PDF limit), upload handling, and transactional persistence.
- `app/Livewire/Admin/ProjectManagement.php` — project listing/search/pagination with status badge and inline toggle, if the module follows the existing Livewire admin structure.
- `resources/views/projects/form.blade.php` — create/edit form with TinyMCE, department multi-select + per-department amount, department heads (read-only), team checkboxes grouped by department, status toggle, and PDF uploads.
- `resources/views/livewire/admin/project-management.blade.php` — index/listing UI with status badge and toggle action.
- `resources/js/app.js` — TinyMCE initialization and any form wiring (department change events, per-department amount inputs, team checkbox toggling).
- `routes/admin.php` — resource route and middleware wiring.
- `database/seeders/PermissionSeeder.php` — add `manage projects` permission alongside existing ones.
- `app/Models/Client.php`, `app/Models/Department.php`, `app/Models/Service.php`, `app/Models/User.php` — reuse the existing relationships and naming conventions.

**Verification**
1. Run the targeted feature tests for project creation and update once they are added.
2. Run validation tests for department selection, per-department amounts, derived heads, department team checkboxes, TinyMCE content submission, PDF-only upload rejection, and the 5MB size limit rejection.
3. Manually verify the form behavior in the browser: department multi-select updates the heads and team checkboxes, each department shows its own amount field, removing a department clears its amount and team selections, and TinyMCE content is preserved on validation failure.
4. Verify edit mode: all previously saved departments, amounts, team selections, and file paths are correctly restored; existing PDFs show a download link without forcing re-upload.
5. Confirm the status toggle works on both the index listing (inline) and the edit form.
6. Confirm the generated files and route registration using the app's existing admin navigation and permission checks.

**Decisions**
- The module should follow the existing admin CRUD structure used by clients and departments.
- "Team" is interpreted as the existing user role `team`, since the repo does not contain a separate Team model or team module.
- **Department heads are not stored on the project.** They are derived live from `department->head` when needed. If the head changes after project creation, the current head is shown — acceptable behaviour.
- The `project_department_teams` pivot is the authoritative record of which team members were selected. It is written on create/update and only changes when an admin explicitly edits the project. This serves as an implicit audit trail of selected teams without a separate snapshot table.
- When multiple departments are selected, the department head field displays all current heads (read-only, derived).
- Department teams should be shown as checkboxes grouped by department, not as one flat list.
- **Amount is stored per department**, not per service. Each department assigned to the project carries its own amount line item.
- `project_type` should be a select/enum (not a free-text field) to ensure consistency across records.
- The `content` field uses TinyMCE.
- The three uploads are restricted to PDF files only, with a **maximum size of 5MB each**.
- Uploaded files are stored under `storage/app/public/projects/{id}/` for consistent path resolution.
- `status` is included in the initial migration as a string/enum column (e.g. `active`/`inactive`, default `active`) so no retrofit migration is needed later. It is toggleable from both the edit form and the index listing.
- Scope excludes redesigning the existing client or department modules; the work should reuse their current patterns rather than refactor them.

**Further Considerations**
1. If additional workflow statuses (e.g. `draft`, `on-hold`, `completed`) are needed beyond `active`/`inactive`, switch the column to an enum with those values before the first migration runs — retrofitting enums is more painful than strings.
2. If file versioning is needed (replacing a PDF and keeping the old one), a `project_documents` child table is a better fit than three path columns on the project row.
3. If strict head-at-creation-time audit is ever required (e.g. for contracts), add a `project_department_head_snapshots` table at that point. For now, live derivation is sufficient and correct.