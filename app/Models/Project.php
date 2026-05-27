<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $fillable = [
        'project_name',
        'client_id',
        'project_type',
        'agreement_date',
        'start_date',
        'end_date',
        'content',
        'logo_path',
        'brand_guidelines_path',
        'fact_sheet_path',
        'status',
    ];

    protected $casts = [
        'agreement_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'project_departments')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_department_teams')
            ->withPivot('department_id')
            ->withTimestamps();
    }
}
