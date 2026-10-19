<?php

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->client = Client::factory()->create();
    $this->actingAs($this->user);
});

test('user can view projects index', function () {
    Project::factory()->count(3)->create(['user_id' => $this->user->id]);
    Project::factory()->count(2)->create(['user_id' => $this->otherUser->id]);

    $response = $this->get(route('projects.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('projects/index')
        ->has('projects.data', 3));
});

test('user can view only their own projects and projects they are member of', function () {
    $ownProject = Project::factory()->create(['user_id' => $this->user->id]);
    $memberProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $memberProject->members()->attach($this->user->id);
    $otherProject = Project::factory()->create(['user_id' => $this->otherUser->id]);

    $response = $this->get(route('projects.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('projects/index')
        ->has('projects.data', 2)
        ->where('projects.data.0.id', $ownProject->id)
        ->where('projects.data.1.id', $memberProject->id));
});

test('user can create project', function () {
    $data = [
        'name' => 'New Project',
        'description' => 'Project description',
        'client_id' => $this->client->id,
        'start_date' => '2025-01-15',
        'end_date' => '2025-06-15',
        'status' => 'aktif',
    ];

    $response = $this->post(route('projects.store'), $data);

    $response->assertRedirect(route('projects.show', Project::first()->id));
    $this->assertDatabaseHas('projects', [
        'name' => 'New Project',
        'user_id' => $this->user->id,
        'client_id' => $this->client->id,
        'status' => 'aktif',
    ]);
});

test('user cannot create project with invalid data', function () {
    $response = $this->post(route('projects.store'), [
        'name' => '',
        'start_date' => '2025-01-15',
        'end_date' => '2025-01-10', // before start_date
        'status' => 'invalid',
    ]);

    $response->assertSessionHasErrors(['name', 'end_date', 'status']);
});

test('user can view project they own', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->get(route('projects.show', $project));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('projects/show')
        ->where('project.id', $project->id));
});

test('user can view project they are member of', function () {
    $project = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $project->members()->attach($this->user->id);

    $response = $this->get(route('projects.show', $project));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('projects/show')
        ->where('project.id', $project->id));
});

test('user cannot view project they are not member of', function () {
    $project = Project::factory()->create(['user_id' => $this->otherUser->id]);

    $response = $this->get(route('projects.show', $project));

    $response->assertStatus(403);
});

test('user can edit their own project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->get(route('projects.edit', $project));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('projects/edit')
        ->where('project.id', $project->id));
});

test('user cannot edit project they do not own', function () {
    $project = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $project->members()->attach($this->user->id);

    $response = $this->get(route('projects.edit', $project));

    $response->assertStatus(403);
});

test('user can update their own project', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Old Name',
        'status' => 'aktif',
    ]);

    $data = [
        'name' => 'Updated Name',
        'description' => 'Updated description',
        'client_id' => $this->client->id,
        'start_date' => '2025-01-15',
        'end_date' => '2025-06-15',
        'status' => 'arsip',
    ];

    $response = $this->put(route('projects.update', $project), $data);

    $response->assertRedirect(route('projects.show', $project));
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated Name',
        'status' => 'arsip',
    ]);
});

test('user cannot update project they do not own', function () {
    $project = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $project->members()->attach($this->user->id);

    $response = $this->put(route('projects.update', $project), [
        'name' => 'Hacked Name',
        'start_date' => '2025-01-15',
        'end_date' => '2025-06-15',
        'status' => 'aktif',
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('projects', ['name' => 'Hacked Name']);
});

test('user can delete their own project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->delete(route('projects.destroy', $project));

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

test('user cannot delete project they do not own', function () {
    $project = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $project->members()->attach($this->user->id);

    $response = $this->delete(route('projects.destroy', $project));

    $response->assertStatus(403);
    $this->assertDatabaseHas('projects', ['id' => $project->id]);
});

test('project requires valid end_date after start_date', function () {
    $response = $this->post(route('projects.store'), [
        'name' => 'Test',
        'start_date' => '2025-06-15',
        'end_date' => '2025-01-15',
        'status' => 'aktif',
    ]);

    $response->assertSessionHasErrors('end_date');
});

test('project requires valid status', function () {
    $response = $this->post(route('projects.store'), [
        'name' => 'Test',
        'start_date' => '2025-01-15',
        'end_date' => '2025-06-15',
        'status' => 'invalid_status',
    ]);

    $response->assertSessionHasErrors('status');
});

test('project can be created without client', function () {
    $data = [
        'name' => 'Project without client',
        'description' => 'No client',
        'start_date' => '2025-01-15',
        'end_date' => '2025-06-15',
        'status' => 'aktif',
    ];

    $response = $this->post(route('projects.store'), $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('projects', [
        'name' => 'Project without client',
        'client_id' => null,
    ]);
});

test('dashboard shows project statistics', function () {
    Project::factory()->count(2)->create(['user_id' => $this->user->id, 'status' => 'aktif']);
    Project::factory()->count(1)->create(['user_id' => $this->user->id, 'status' => 'arsip']);
    Project::factory()->count(1)->create(['user_id' => $this->otherUser->id]);
    $memberProject = Project::factory()->create(['user_id' => $this->otherUser->id, 'status' => 'aktif']);
    $memberProject->members()->attach($this->user->id);

    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('dashboard')
        ->where('activeProjects', 3) // 2 own + 1 member
        ->where('archivedProjects', 1)
        ->where('totalProjects', 4));
});
