<?php

namespace Tests\Feature;

use App\Models\PortalResource;
use App\Models\User;
use Database\Seeders\RankSeeder;
use Database\Seeders\ResourceDocumentSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceDocumentsPageTest extends TestCase
{
    use RefreshDatabase;

    private function seedDocuments(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            ResourceDocumentSeeder::class,
        ]);
    }

    public function test_member_can_view_documents_library(): void
    {
        $this->seedDocuments();

        $user = User::factory()->create();
        $user->assignRole('member');

        $this->actingAs($user)
            ->get(route('resources.documents'))
            ->assertOk()
            ->assertSee('Documents', false)
            ->assertSee('Associate Welcome Packet', false)
            ->assertSee('Featured documents', false)
            ->assertSee('Compliance', false);
    }

    public function test_documents_can_be_filtered_by_category(): void
    {
        $this->seedDocuments();

        $user = User::factory()->create();
        $user->assignRole('member');

        $this->actingAs($user)
            ->get(route('resources.documents', ['category' => 'scripts']))
            ->assertOk()
            ->assertSee('Warm Market Introduction Script', false)
            ->assertDontSee('Associate Welcome Packet', false);
    }

    public function test_unpublished_documents_are_hidden(): void
    {
        $this->seedDocuments();

        PortalResource::query()->create([
            'title' => 'Hidden Internal Draft',
            'type' => 'document',
            'category' => 'general',
            'sort_order' => 99,
            'is_published' => false,
            'url' => 'https://example.com/hidden.pdf',
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');

        $this->actingAs($user)
            ->get(route('resources.documents'))
            ->assertOk()
            ->assertDontSee('Hidden Internal Draft', false);
    }

    public function test_authorized_roles_see_manage_documents_link(): void
    {
        $this->seedDocuments();

        foreach (['admin', 'agency-owner', 'certified-field-mentor'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->actingAs($user)
                ->get(route('resources.documents'))
                ->assertOk()
                ->assertSee('Manage documents', false);
        }
    }

    public function test_member_and_trainer_do_not_see_manage_documents_link(): void
    {
        $this->seedDocuments();

        foreach (['member', 'trainer'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->actingAs($user)
                ->get(route('resources.documents'))
                ->assertOk()
                ->assertDontSee('Manage documents', false);
        }
    }

    public function test_admin_can_export_documents_to_seeder_from_documents_page(): void
    {
        $this->seedDocuments();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        PortalResource::query()->create([
            'title' => 'Seeder Export Test',
            'description' => 'Exported from feature test.',
            'type' => 'document',
            'category' => 'guides',
            'sort_order' => 99,
            'content' => '<p>Export me</p>',
            'url' => null,
            'file_format' => 'PDF',
            'is_published' => true,
            'is_featured' => false,
        ]);

        $seederPath = database_path('seeders/ResourceDocumentSeeder.php');
        $originalSeeder = file_get_contents($seederPath);

        try {
            $this->actingAs($admin)
                ->post(route('resources.documents.update-seeder'))
                ->assertRedirect(route('resources.documents'))
                ->assertSessionHas('status', 'document-seeder-updated');

            $seeder = file_get_contents($seederPath);

            $this->assertStringContainsString('Seeder Export Test', $seeder);
            $this->assertStringContainsString('Export me', $seeder);
            $this->assertStringContainsString('PortalResource::query()->updateOrCreate', $seeder);
        } finally {
            file_put_contents($seederPath, $originalSeeder);
        }
    }

    public function test_cfm_cannot_update_document_seeder(): void
    {
        $this->seedDocuments();

        $cfm = User::factory()->create();
        $cfm->assignRole('certified-field-mentor');

        $this->actingAs($cfm)
            ->post(route('resources.documents.update-seeder'))
            ->assertForbidden();
    }

    public function test_super_admin_can_update_document_seeder(): void
    {
        $this->seedDocuments();

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $seederPath = database_path('seeders/ResourceDocumentSeeder.php');
        $originalSeeder = file_get_contents($seederPath);

        try {
            $this->actingAs($superAdmin)
                ->post(route('resources.documents.update-seeder'))
                ->assertRedirect(route('resources.documents'))
                ->assertSessionHas('status', 'document-seeder-updated');
        } finally {
            file_put_contents($seederPath, $originalSeeder);
        }
    }

    public function test_admin_sees_update_document_seeder_button(): void
    {
        $this->seedDocuments();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('resources.documents'))
            ->assertOk()
            ->assertSee('Update document seeder', false);
    }

    public function test_member_does_not_see_update_document_seeder_button(): void
    {
        $this->seedDocuments();

        $user = User::factory()->create();
        $user->assignRole('member');

        $this->actingAs($user)
            ->get(route('resources.documents'))
            ->assertOk()
            ->assertDontSee('Update document seeder', false);
    }

    public function test_super_admin_sees_update_document_seeder_button(): void
    {
        $this->seedDocuments();

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->get(route('resources.documents'))
            ->assertOk()
            ->assertSee('Update document seeder', false);
    }

    public function test_documents_page_defaults_to_table_view(): void
    {
        $this->seedDocuments();

        $user = User::factory()->create();
        $user->assignRole('member');

        $response = $this->actingAs($user)->get(route('resources.documents'));

        $response->assertOk();
        $this->assertStringContainsString("viewMode: 'table'", $response->getContent());
        $this->assertStringContainsString('x-show="viewMode === \'table\'"', $response->getContent());
        $response->assertSee('Format', false);
    }

    public function test_member_can_preview_html_document_content(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        $document = PortalResource::query()->create([
            'title' => 'Preview Guide',
            'type' => 'document',
            'category' => 'guides',
            'sort_order' => 1,
            'content' => '<p>Welcome to <strong>EFGTrack</strong>.</p>',
            'is_published' => true,
        ]);

        $this->actingAs($user)
            ->getJson(route('resources.documents.preview', $document))
            ->assertOk()
            ->assertJsonPath('has_rtf', true)
            ->assertJsonPath('has_pdf', false)
            ->assertJsonPath('default_view', 'rtf')
            ->assertJsonPath('title', 'Preview Guide');

        $this->actingAs($user)
            ->get(route('resources.documents', ['document' => $document->id]))
            ->assertOk()
            ->assertSee('View', false)
            ->assertSee('Document Preview', false)
            ->assertSee('Rich Text', false)
            ->assertSee('PDF', false);
    }

    public function test_preview_includes_both_rtf_and_pdf_when_available(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('resources/combo-guide.pdf', '%PDF-1.4 sample');

        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        $document = PortalResource::query()->create([
            'title' => 'Combo Guide',
            'type' => 'document',
            'category' => 'guides',
            'sort_order' => 1,
            'content' => '<p>Rich text body</p>',
            'file_path' => 'resources/combo-guide.pdf',
            'file_format' => 'PDF',
            'is_published' => true,
        ]);

        $this->actingAs($user)
            ->getJson(route('resources.documents.preview', $document))
            ->assertOk()
            ->assertJsonPath('has_rtf', true)
            ->assertJsonPath('has_pdf', true)
            ->assertJsonPath('default_view', 'rtf')
            ->assertJsonPath('pdf_url', route('resources.documents.view', $document));
    }

    public function test_member_can_view_pdf_inline_without_download(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('resources/sample-guide.pdf', '%PDF-1.4 sample');

        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        $document = PortalResource::query()->create([
            'title' => 'Inline PDF Guide',
            'type' => 'document',
            'category' => 'guides',
            'sort_order' => 1,
            'file_path' => 'resources/sample-guide.pdf',
            'file_format' => 'PDF',
            'is_published' => true,
        ]);

        $response = $this->actingAs($user)
            ->get(route('resources.documents.view', $document));

        $response->assertOk();
        $this->assertStringContainsString(
            'inline',
            (string) $response->headers->get('content-disposition')
        );
    }

    public function test_member_can_download_published_file_document(): void
    {
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
        Storage::disk('public')->put('resources/sample-guide.pdf', 'sample contents');

        $user = User::factory()->create();
        $user->assignRole('member');

        $document = PortalResource::query()->create([
            'title' => 'Downloadable Guide',
            'type' => 'document',
            'category' => 'guides',
            'sort_order' => 1,
            'file_path' => 'resources/sample-guide.pdf',
            'file_format' => 'PDF',
            'is_published' => true,
        ]);

        $this->actingAs($user)
            ->get(route('resources.documents.download', $document))
            ->assertOk()
            ->assertDownload('sample-guide.pdf');
    }
}
