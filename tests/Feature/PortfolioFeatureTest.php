<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_portfolio_page_loads_successfully_with_projects(): void
    {
        $project = Project::factory()->create([
            'title' => 'Telemetry Hub',
            'category' => 'full-stack',
        ]);

        $response = $this->get(route('portfolio.index'));

        $response->assertOk();
        $response->assertSee('Telemetry Hub');
        $response->assertViewHas('projects');
        $response->assertViewHas('categories');
    }

    public function test_single_project_json_endpoint_returns_project_details(): void
    {
        $project = Project::factory()->create([
            'title' => 'Query Sentinel',
            'slug' => 'query-sentinel',
            'category' => 'backend-api',
        ]);

        $response = $this->getJson(route('portfolio.project.show', $project));

        $response->assertOk();
        $response->assertJsonPath('project.title', 'Query Sentinel');
        $response->assertJsonPath('project.slug', 'query-sentinel');
    }

    public function test_contact_form_stores_message_and_redirects(): void
    {
        $payload = [
            'name' => 'Jane Cooper',
            'email' => 'jane@example.com',
            'subject' => 'Full-Stack Architecture Inquiry',
            'message' => 'I would like to discuss a project involving high-throughput Laravel systems.',
        ];

        $response = $this->from(route('portfolio.index'))
            ->post(route('portfolio.contact.store'), $payload);

        $response->assertRedirect(route('portfolio.index').'#contact');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Jane Cooper',
            'email' => 'jane@example.com',
            'subject' => 'Full-Stack Architecture Inquiry',
        ]);
    }

    public function test_contact_form_handles_json_submission(): void
    {
        $payload = [
            'name' => 'Devon Lane',
            'email' => 'devon@example.com',
            'subject' => 'API Optimization Consultation',
            'message' => 'We are scaling our database queries and need architectural review.',
        ];

        $response = $this->postJson(route('portfolio.contact.store'), $payload);

        $response->assertCreated();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'devon@example.com',
        ]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->postJson(route('portfolio.contact.store'), []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_contact_form_rejects_honeypot_field(): void
    {
        $payload = [
            'name' => 'Bot Crawler',
            'email' => 'bot@example.com',
            'subject' => 'Spam subject',
            'message' => 'Spam message content longer than 10 chars',
            'company_hp' => 'Automated Spammer Inc',
        ];

        $response = $this->postJson(route('portfolio.contact.store'), $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['company_hp']);
        $this->assertDatabaseCount('contact_messages', 0);
    }
}
