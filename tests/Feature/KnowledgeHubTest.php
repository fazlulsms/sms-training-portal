<?php

namespace Tests\Feature;

use App\Models\KnowledgeResource;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KnowledgeHubTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        Schema::dropIfExists('knowledge_resources');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('admin');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('knowledge_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('resource_type');
            $table->string('category');
            $table->string('standard_framework');
            $table->string('version')->nullable();
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->string('file_disk')->default('local');
            $table->string('file_path');
            $table->string('original_file_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function test_admin_can_create_resource_and_upload_supported_files(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $files = [
            ['source.pdf', 'application/pdf'],
            ['source.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            ['slides.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
            ['photo.png', 'image/png'],
            ['video.mp4', 'video/mp4'],
        ];

        foreach ($files as [$name, $mime]) {
            $response = $this->actingAs($admin)->post(route('knowledge-hub.store'), [
                ...$this->validPayload(),
                'title' => 'Resource '.$name,
                'file' => UploadedFile::fake()->create($name, 100, $mime),
            ]);

            $response->assertRedirect(route('knowledge-hub.index'));
            $response->assertSessionHasNoErrors();
        }

        $this->assertDatabaseCount('knowledge_resources', count($files));

        KnowledgeResource::all()->each(function (KnowledgeResource $resource) {
            Storage::disk('local')->assertExists($resource->file_path);
            $this->assertStringStartsWith('knowledge-hub/', $resource->file_path);
        });
    }

    public function test_search_and_category_filter_return_matching_resources(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->createResource(['title' => 'ISO 9001 Audit Evidence', 'category' => 'Quality Management']);
        $this->createResource(['title' => 'Safety Interview Notes', 'category' => 'Occupational Health & Safety']);

        $this->actingAs($admin)
            ->get(route('knowledge-hub.index', ['search' => 'Audit']))
            ->assertOk()
            ->assertSee('ISO 9001 Audit Evidence')
            ->assertDontSee('Safety Interview Notes');

        $this->actingAs($admin)
            ->get(route('knowledge-hub.index', ['category' => 'Occupational Health & Safety']))
            ->assertOk()
            ->assertSee('Safety Interview Notes')
            ->assertDontSee('ISO 9001 Audit Evidence');
    }

    public function test_admin_can_change_status_and_archive_resource(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $resource = $this->createResource(['status' => 'draft']);

        $this->actingAs($admin)
            ->put(route('knowledge-hub.update', $resource), [
                ...$this->validPayload(),
                'status' => 'approved',
            ])
            ->assertRedirect(route('knowledge-hub.show', $resource));

        $this->assertSame('approved', $resource->refresh()->status);
        $this->assertNotNull($resource->approved_at);

        $this->actingAs($admin)
            ->post(route('knowledge-hub.archive', $resource))
            ->assertRedirect(route('knowledge-hub.index'));

        $resource->refresh();
        $this->assertSame('archived', $resource->status);
        $this->assertNull($resource->approved_at);
        $this->assertNotNull($resource->archived_at);
    }

    public function test_trainer_sees_and_downloads_only_approved_resources(): void
    {
        $trainer = User::factory()->create(['role' => 'trainer']);
        $approved = $this->createResource(['title' => 'Approved Standard', 'status' => 'approved']);
        $draft = $this->createResource(['title' => 'Draft Standard', 'status' => 'draft']);

        $this->actingAs($trainer)
            ->get(route('knowledge-hub.index'))
            ->assertOk()
            ->assertSee('Approved Standard')
            ->assertDontSee('Draft Standard');

        $this->actingAs($trainer)
            ->get(route('knowledge-hub.download', $approved))
            ->assertOk();

        $this->actingAs($trainer)
            ->get(route('knowledge-hub.show', $draft))
            ->assertForbidden();

        $this->actingAs($trainer)
            ->get(route('knowledge-hub.create'))
            ->assertForbidden();
    }

    public function test_participant_has_no_knowledge_hub_access(): void
    {
        $participant = User::factory()->create(['role' => 'participant']);
        $resource = $this->createResource(['status' => 'approved']);

        $this->actingAs($participant)
            ->get(route('knowledge-hub.index'))
            ->assertForbidden();

        $this->actingAs($participant)
            ->get(route('knowledge-hub.download', $resource))
            ->assertForbidden();
    }

    private function createResource(array $overrides = []): KnowledgeResource
    {
        Storage::disk('local')->put('knowledge-hub/test/source.pdf', 'test file');

        return KnowledgeResource::create([
            ...$this->validPayload(),
            'file_disk' => 'local',
            'file_path' => 'knowledge-hub/test/source.pdf',
            'original_file_name' => 'source.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 9,
            'approved_at' => ($overrides['status'] ?? 'draft') === 'approved' ? now() : null,
            ...$overrides,
        ]);
    }

    private function validPayload(): array
    {
        return [
            'title' => 'ISO Guidance Resource',
            'resource_type' => 'Guidance Document',
            'category' => 'ISO Standards',
            'standard_framework' => 'ISO 9001:2015',
            'version' => '1.0',
            'status' => 'draft',
            'notes' => 'Approved source notes for future course development.',
        ];
    }
}
