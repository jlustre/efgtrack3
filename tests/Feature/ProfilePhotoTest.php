<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use App\Services\ProfilePhotoService;
use Database\Seeders\ProfileCompletionFieldSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_upload_update_and_remove_profile_photo_without_orphans(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['name' => 'Photo Member']);

        $first = UploadedFile::fake()->image('first.jpg', 400, 400);
        $this->actingAs($user)
            ->post(route('profile.photo.update'), ['photo' => $first])
            ->assertRedirect(route('profile.edit', ['tab' => 'profile']));

        $user->refresh();
        $firstPath = $user->profile?->profile_photo_path;
        $this->assertNotNull($firstPath);
        Storage::disk('public')->assertExists($firstPath);

        $photoUrl = $user->fresh(['profile'])->profilePhotoUrl();
        $this->assertNotNull($photoUrl);
        $this->assertStringContainsString('storage/profile-photos/', $photoUrl);

        $this->actingAs($user)
            ->get(route('profile.edit', ['tab' => 'profile']))
            ->assertOk()
            ->assertSee('Update Profile Photo', false)
            ->assertSee('Remove Photo', false)
            ->assertSee($photoUrl, false);

        $second = UploadedFile::fake()->image('second.jpg', 400, 400);
        $this->actingAs($user)
            ->post(route('profile.photo.update'), ['photo' => $second])
            ->assertRedirect(route('profile.edit', ['tab' => 'profile']));

        $user->refresh();
        $secondPath = $user->profile?->profile_photo_path;
        $this->assertNotSame($firstPath, $secondPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);

        $this->actingAs($user)
            ->delete(route('profile.photo.destroy'))
            ->assertRedirect(route('profile.edit', ['tab' => 'profile']));

        $user->refresh();
        $this->assertNull($user->profile?->profile_photo_path);
        Storage::disk('public')->assertMissing($secondPath);
    }

    public function test_profile_deletion_removes_stored_photo_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $path = UploadedFile::fake()->image('avatar.jpg')->store('profile-photos/'.$user->id, 'public');

        $profile = Profile::query()->create([
            'user_id' => $user->id,
            'profile_photo_path' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        app(ProfilePhotoService::class)->delete($user);

        Storage::disk('public')->assertMissing($path);
        $this->assertNull($profile->fresh()->profile_photo_path);
    }

    public function test_invalid_profile_photo_is_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('profile.photo.update'), [
                'photo' => UploadedFile::fake()->create('notes.txt', 100, 'text/plain'),
            ])
            ->assertSessionHasErrors('photo');
    }

    public function test_profile_photo_can_be_uploaded_from_dashboard_completion_modal(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('profile.photo.update'), [
                'photo' => UploadedFile::fake()->image('dashboard.jpg', 400, 400),
                'redirect_to' => 'dashboard',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('show_profile_completion_modal', true);

        $this->assertNotNull($user->fresh()->profile?->profile_photo_path);
    }

    public function test_profile_photo_can_be_uploaded_via_json_for_completion_modal(): void
    {
        Storage::fake('public');

        $this->seed([
            RolePermissionSeeder::class,
            ProfileCompletionFieldSeeder::class,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('profile.photo.update'), [
                'photo' => UploadedFile::fake()->image('dashboard.jpg', 400, 400),
                'redirect_to' => 'dashboard',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Your profile photo was updated.')
            ->assertJsonStructure([
                'photo_url',
                'profile_completion' => ['percent', 'is_complete', 'fields'],
            ]);

        $photoField = collect($response->json('profile_completion.fields'))
            ->firstWhere('key', 'profile_photo_path');

        $this->assertTrue($photoField['filled'] ?? false);
        $this->assertNotNull($user->fresh()->profile?->profile_photo_path);
    }

    public function test_completion_modal_photo_upload_returns_json_validation_error_without_file(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('profile.photo.update'), [
                'redirect_to' => 'dashboard',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['photo'])
            ->assertJsonPath('errors.photo.0', 'Please choose a profile photo to upload.');
    }

    public function test_completion_modal_photo_upload_returns_json_validation_error_for_invalid_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('profile.photo.update'), [
                'photo' => UploadedFile::fake()->create('notes.txt', 100, 'text/plain'),
                'redirect_to' => 'dashboard',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['photo']);
    }
}
