<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'tagline',
        'category',
        'description',
        'architecture_notes',
        'key_features',
        'tech_stack',
        'github_url',
        'demo_url',
        'system_metrics',
        'is_featured',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'key_features' => 'array',
            'tech_stack' => 'array',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'full-stack' => 'Full-Stack Web App',
            'backend-api' => 'Backend & API',
            'dev-tool' => 'Developer Tool',
            default => ucfirst(str_replace('-', ' ', $this->category)),
        };
    }
}
