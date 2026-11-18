<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Paksadita Paksi',
                'slug' => 'paksadita-paksi',
                'tagline' => 'Platform web interaktif dan portofolio personal yang responsif dan modern.',
                'category' => 'full-stack',
                'description' => 'Paksadita Paksi adalah platform website interaktif yang dibangun dengan fokus pada kenyamanan penjelajahan, estetika visual yang elegan, dan performa tinggi di berbagai ukuran layar.',
                'architecture_notes' => 'Dioptimalkan dengan prinsip clean code, struktur komponen modular, dan kompresi aset untuk pemuatan super cepat.',
                'key_features' => [
                    'Desain responsif yang adaptif untuk smartphone, tablet, hingga desktop',
                    'Navigasi yang intuitif dengan hierarki informasi yang jelas',
                    'Waktu muat halaman instan dan performa optimal',
                    'Tampilan visual modern yang memanjakan mata',
                ],
                'tech_stack' => ['Laravel', 'PHP', 'Tailwind CSS', 'JavaScript'],
                'github_url' => null,
                'demo_url' => 'https://mrizqys.web.id',
                'system_metrics' => 'Live & aktif di mrizqys.web.id',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'LogPulse',
                'slug' => 'logpulse-realtime-monitor',
                'tagline' => 'High-throughput exception aggregation and real-time log telemetry for distributed Laravel services.',
                'category' => 'full-stack',
                'description' => 'LogPulse ingests exception traces from distributed microservices via Redis pub/sub, batches payload writes to PostgreSQL with timescaled partitioning, and broadcasts filtered alert payloads to connected frontend dashboards using WebSockets.',
                'architecture_notes' => 'Implemented Redis pipeline buffering to withstand traffic spikes of 35,000 log events per second without dropping packets. The dashboard uses Alpine.js with virtual list scrolling for render performance.',
                'key_features' => [
                    'Sub-50ms ingestion pipeline with Redis streams and bulk worker processing',
                    'Zero-overhead PHP client library with non-blocking UDP fallback',
                    'Real-time live tail dashboard with regex filtering and log level grouping',
                    'Webhook integration with PagerDuty, Slack, and Discord alert channels',
                ],
                'tech_stack' => ['Laravel 12', 'PostgreSQL', 'Redis Streams', 'WebSockets', 'Tailwind CSS', 'Alpine.js'],
                'github_url' => 'https://github.com/example/logpulse',
                'demo_url' => 'https://logpulse.example.dev',
                'system_metrics' => 'Benchmarked at 35,000 events/sec on a single c6g.large instance',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'SchemaCraft',
                'slug' => 'schemacraft-database-designer',
                'tagline' => 'Visual database schema modeler and bidirectional Laravel migration generator.',
                'category' => 'dev-tool',
                'description' => 'SchemaCraft inspects existing database tables or raw migration files, constructs an interactive canvas of entity relationships, and produces clean, PSR-12 compliant migration files, Eloquent models, and model factories in real time.',
                'architecture_notes' => 'Built with an AST parser using nikic/php-parser to parse migration method chains directly into internal relational node graphs. The interactive canvas employs SVG vector transforms with collision-free bezier routing.',
                'key_features' => [
                    'Bidirectional synchronization between visual canvas and Laravel migration files',
                    'Foreign key cascade validation and circular reference detection',
                    'Instant export of Eloquent models with typed relationship methods',
                    'Dark and light theme canvas with SVG and PNG schema export',
                ],
                'tech_stack' => ['PHP 8.5', 'Laravel 12', 'Tailwind CSS', 'TypeScript', 'Canvas API'],
                'github_url' => 'https://github.com/example/schemacraft',
                'demo_url' => 'https://schemacraft.example.dev',
                'system_metrics' => 'Parsed schemas with 120+ tables in under 180ms',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'QueueMaster',
                'slug' => 'queuemaster-horizon-profiler',
                'tagline' => 'Job execution profiler and latency telemetry extension for Laravel queue workers.',
                'category' => 'backend-api',
                'description' => 'QueueMaster instruments Laravel queue events to record job lifecycle metrics, memory footprints, and database query counts per execution, pinpointing bottlenecks and runaway workers before they impact availability.',
                'architecture_notes' => 'Listens to JobProcessing and JobProcessed core events with low-overhead memory sampling. Telemetry snapshots are compacted via exponential histogram buckets and stored in SQLite/PostgreSQL.',
                'key_features' => [
                    'Granular duration, memory delta, and query count tracking per job class',
                    'Automated anomaly detection for jobs exceeding P99 duration thresholds',
                    'Safe worker pause and selective retry command center',
                    'Exportable performance reports in JSON and Prometheus scrape format',
                ],
                'tech_stack' => ['Laravel 12', 'PHP 8.5', 'PostgreSQL', 'Prometheus Exporter', 'Tailwind CSS'],
                'github_url' => 'https://github.com/example/queuemaster',
                'demo_url' => 'https://queuemaster.example.dev',
                'system_metrics' => 'Less than 1.2% CPU overhead under continuous job load',
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'DocuFlow',
                'slug' => 'docuflow-secure-documents',
                'tagline' => 'Multi-tenant contract approval system with cryptographic audit logging and PDF stamping.',
                'category' => 'full-stack',
                'description' => 'DocuFlow manages structured multi-party document review and digital signature workflows. Every signature event is timestamped, cryptographically hashed, and verified against an immutable audit log.',
                'architecture_notes' => 'Implemented multi-tenant tenant isolation using row-level security and schema scoping. Document generation runs in sandboxed queue jobs with PDF signature flattening.',
                'key_features' => [
                    'Multi-party sequential and parallel signer workflows',
                    'Cryptographic SHA-256 document fingerprinting and tamper detection',
                    'Automated PDF certificate stamping with signature placement coordinates',
                    'Role-based permissions with fine-grained audit log export',
                ],
                'tech_stack' => ['Laravel 12', 'PostgreSQL', 'Tailwind CSS', 'Alpine.js', 'FPDF/FPDI'],
                'github_url' => 'https://github.com/example/docuflow',
                'demo_url' => 'https://docuflow.example.dev',
                'system_metrics' => 'Generated and certified 10,000+ legal PDFs with zero hash mismatches',
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'ForgePulse',
                'slug' => 'forgepulse-server-heartbeat',
                'tagline' => 'Lightweight server monitoring daemon and alert dispatcher built with zero external dependencies.',
                'category' => 'dev-tool',
                'description' => 'A standalone PHP CLI binary that collects CPU, disk I/O, memory pressure, and socket connections, dispatching HMAC-signed telemetry beacons to a centralized Laravel control plane.',
                'architecture_notes' => 'Engineered as a single self-contained PHP script running as a systemd daemon with under 14MB resident memory. Supports secure mutual TLS beaconing.',
                'key_features' => [
                    'Zero external dependencies beyond vanilla PHP CLI',
                    'Configurable disk threshold warnings and silent auto-recovery scripts',
                    'Fast heartbeat failover detection within 15 seconds',
                    'Built-in systemd and supervisor installation wrappers',
                ],
                'tech_stack' => ['PHP 8.5 CLI', 'Laravel 12', 'Tailwind CSS', 'SQLite', 'systemd'],
                'github_url' => 'https://github.com/example/forgepulse',
                'demo_url' => 'https://forgepulse.example.dev',
                'system_metrics' => 'Sub-14MB memory footprint on active systemd worker',
                'is_featured' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'RestCraft',
                'slug' => 'restcraft-contract-validator',
                'tagline' => 'Automated OpenAPI specification generator and route contract validator for Laravel APIs.',
                'category' => 'backend-api',
                'description' => 'RestCraft analyzes FormRequests, Eloquent resources, and route annotations to produce accurate OpenAPI 3.1 specifications while validating incoming payloads and mock responses against strict JSON schemas.',
                'architecture_notes' => 'Integrates into the Laravel test suite with custom PHPUnit assertions, ensuring production routes never deviate from their documented API contract schemas.',
                'key_features' => [
                    'Automatic OpenAPI 3.1 generation from FormRequest validation rules',
                    'PHPUnit test assertions for request and response contract compliance',
                    'Interactive Swagger UI and Redoc documentation endpoints',
                    'TypeScript interface exporter for frontend API client typing',
                ],
                'tech_stack' => ['PHP 8.5', 'Laravel 12', 'OpenAPI 3.1', 'PHPUnit', 'TypeScript'],
                'github_url' => 'https://github.com/example/restcraft',
                'demo_url' => 'https://restcraft.example.dev',
                'system_metrics' => 'Validates full route schemas in under 45ms per test case',
                'is_featured' => false,
                'sort_order' => 6,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['slug' => $project['slug']],
                $project
            );
        }
    }
}
