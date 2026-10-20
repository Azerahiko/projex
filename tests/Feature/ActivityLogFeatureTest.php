<?php

use App\Models\Activity;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
    $this->task = Task::factory()->create(['project_id' => $this->project->id]);
    $this->actingAs($this->user);
});

test('creating project logs activity', function () {
    $data = [
        'name' => 'New Project',
        'status' => 'aktif',
        'start_date' => '2025-01-01',
        'end_date' => '2025-12-31',
    ];

    $response = $this->post(route('projects.store'), $data);

    $response->assertRedirect();
    $project = Project::where('name', 'New Project')->first();
    $this->assertNotNull($project);

    $this->assertDatabaseHas('activities', [
        'project_id' => $project->id,
        'type' => 'project_created',
        'user_id' => $this->user->id,
    ]);
});

test('updating project logs activity', function () {
    $response = $this->put(route('projects.update', $this->project), [
        'name' => 'Updated Name',
        'status' => 'aktif',
        'start_date' => '2025-01-01',
        'end_date' => '2025-12-31',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'project_id' => $this->project->id,
        'type' => 'project_updated',
        'user_id' => $this->user->id,
    ]);
});

test('creating task logs activity', function () {
    $data = [
        'project_id' => $this->project->id,
        'title' => 'New Task',
        'status' => 'todo',
        'priority' => 'sedang',
    ];

    $response = $this->post(route('tasks.store'), $data);

    $response->assertRedirect();
    $task = Task::where('title', 'New Task')->first();
    $this->assertNotNull($task);

    $this->assertDatabaseHas('activities', [
        'project_id' => $this->project->id,
        'task_id' => $task->id,
        'type' => 'task_created',
        'user_id' => $this->user->id,
    ]);
});

test('updating task logs activity', function () {
    $this->task->update(['status' => 'todo']);

    $data = [
        'project_id' => $this->project->id,
        'title' => 'Updated Task',
        'status' => 'done',
        'priority' => 'tinggi',
    ];

    $response = $this->put(route('tasks.update', $this->task), $data);

    $response->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'project_id' => $this->project->id,
        'task_id' => $this->task->id,
        'type' => 'task_status_changed',
        'user_id' => $this->user->id,
    ]);
});

test('changing task status logs specific activity', function () {
    $this->task->update(['status' => 'todo']);

    $data = [
        'project_id' => $this->project->id,
        'title' => $this->task->title,
        'status' => 'done',
        'priority' => 'sedang',
    ];

    $response = $this->put(route('tasks.update', $this->task), $data);

    $response->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'project_id' => $this->project->id,
        'task_id' => $this->task->id,
        'type' => 'task_status_changed',
        'user_id' => $this->user->id,
    ]);

    $activity = Activity::where('type', 'task_status_changed')->first();
    $this->assertEquals('todo', $activity->properties['old_status']);
    $this->assertEquals('done', $activity->properties['new_status']);
});

test('changing task assignee logs assignment activity', function () {
    $member = User::factory()->create();
    $this->project->members()->attach($member->id);

    // Update task to have no assignee first
    $this->task->update(['assigned_to' => null]);

    $data = [
        'project_id' => $this->project->id,
        'title' => $this->task->title,
        'status' => 'todo',
        'priority' => 'sedang',
        'assigned_to' => $member->id,
    ];

    $response = $this->put(route('tasks.update', $this->task), $data);

    $response->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'project_id' => $this->project->id,
        'task_id' => $this->task->id,
        'type' => 'task_assigned',
        'user_id' => $this->user->id,
    ]);

    $activity = Activity::where('type', 'task_assigned')->first();
    $this->assertNull($activity->properties['old_assigned_to']);
    $this->assertEquals($member->id, $activity->properties['new_assigned_to']);
});

test('deleting task logs activity', function () {
    $response = $this->delete(route('tasks.destroy', $this->task));

    $response->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'project_id' => $this->project->id,
        'type' => 'task_deleted',
        'user_id' => $this->user->id,
    ]);
    // task_id should be nulled due to nullOnDelete FK
    $activity = Activity::where('type', 'task_deleted')->first();
    $this->assertNull($activity->task_id);
});

test('adding comment logs activity', function () {
    $response = $this->post(route('tasks.comments.store', $this->task), [
        'body' => 'Komentar baru',
    ]);

    $response->assertRedirect();

    $comment = Comment::where('body', 'Komentar baru')->first();
    $this->assertNotNull($comment);

    $this->assertDatabaseHas('activities', [
        'project_id' => $this->project->id,
        'task_id' => $this->task->id,
        'type' => 'comment_created',
        'user_id' => $this->user->id,
    ]);
});

test('updating comment logs activity', function () {
    $comment = Comment::factory()->create([
        'task_id' => $this->task->id,
        'user_id' => $this->user->id,
    ]);

    $response = $this->put(route('tasks.comments.update', [$this->task, $comment]), [
        'body' => 'Komentar diperbarui',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'project_id' => $this->project->id,
        'task_id' => $this->task->id,
        'type' => 'comment_updated',
        'user_id' => $this->user->id,
    ]);
});

test('deleting comment logs activity', function () {
    $comment = Comment::factory()->create([
        'task_id' => $this->task->id,
        'user_id' => $this->user->id,
    ]);

    $response = $this->delete(route('tasks.comments.destroy', [$this->task, $comment]));

    $response->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'project_id' => $this->project->id,
        'type' => 'comment_deleted',
        'user_id' => $this->user->id,
    ]);
});

test('project show page includes activities', function () {
    // Create some activities manually for the project
    $project = $this->project;
    $project->activities()->create([
        'type' => 'project_created',
        'description' => 'Membuat project Test',
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('projects.show', $project));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('projects/show')
        ->has('activities', 1));
});

test('task show page includes comments and activities', function () {
    Comment::factory()->count(2)->create(['task_id' => $this->task->id]);
    $this->task->activities()->create([
        'type' => 'task_created',
        'description' => 'Membuat tugas Test',
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('tasks.show', $this->task));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('tasks/show')
        ->has('comments', 2)
        ->has('activities', 1));
});

test('activity timeline shows only last 30 activities per page', function () {
    for ($i = 0; $i < 35; $i++) {
        $this->task->activities()->create([
            'type' => 'task_updated',
            'description' => "Update ke-{$i}",
            'user_id' => $this->user->id,
        ]);
    }

    $response = $this->get(route('tasks.show', $this->task));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('tasks/show')
        ->has('activities', 30));
});

test('activities scoped to task are visible on task page', function () {
    $otherTask = Task::factory()->create(['project_id' => $this->project->id]);

    // Activity for other task
    $otherTask->activities()->create([
        'type' => 'task_created',
        'description' => 'Tugas lain',
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('tasks.show', $this->task));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('tasks/show')
        ->has('activities', 0));
});

test('project activities include task activities', function () {
    $project = $this->project;
    $task = Task::factory()->create(['project_id' => $project->id]);

    // Task activity - also set project_id since it's a project-scoped activity
    $task->activities()->create([
        'project_id' => $project->id,
        'type' => 'task_created',
        'description' => 'Membuat tugas Test',
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('projects.show', $project));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('projects/show')
        ->has('activities', 1)
        ->where('activities.0.type', 'task_created'));
});

test('activity feed page shows activities from accessible projects', function () {
    $ownProject = Project::factory()->create(['user_id' => $this->user->id]);
    $memberProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $memberProject->members()->attach($this->user->id);
    $foreignProject = Project::factory()->create(['user_id' => $this->otherUser->id]);

    $ownTask = Task::factory()->create(['project_id' => $ownProject->id]);
    $memberTask = Task::factory()->create(['project_id' => $memberProject->id]);
    $foreignTask = Task::factory()->create(['project_id' => $foreignProject->id]);

    // Activities for accessible projects
    $ownTask->activities()->create([
        'project_id' => $ownProject->id,
        'type' => 'task_created',
        'description' => 'Membuat tugas',
        'user_id' => $this->user->id,
    ]);
    $memberTask->activities()->create([
        'project_id' => $memberProject->id,
        'type' => 'task_updated',
        'description' => 'Memperbarui tugas',
        'user_id' => $this->user->id,
    ]);

    // Activity for inaccessible project (should not appear)
    $foreignTask->activities()->create([
        'project_id' => $foreignProject->id,
        'type' => 'task_created',
        'description' => 'Tugas asing',
        'user_id' => $this->otherUser->id,
    ]);

    $response = $this->get(route('activities.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('activities/index')
        ->has('activities.data', 2));
});

test('activity feed paginated', function () {
    for ($i = 0; $i < 25; $i++) {
        $this->task->activities()->create([
            'project_id' => $this->project->id,
            'type' => 'task_updated',
            'description' => "Update ke-{$i}",
            'user_id' => $this->user->id,
        ]);
    }

    $response = $this->get(route('activities.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('activities/index')
        ->has('activities.data', 20));
});

test('guest cannot access activity feed', function () {
    auth()->logout();

    $response = $this->get(route('activities.index'));

    $response->assertRedirect(route('login'));
});

test('activity feed includes project activities', function () {
    $this->project->activities()->create([
        'type' => 'project_updated',
        'description' => 'Memperbarui project',
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('activities.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('activities/index')
        ->has('activities.data', 1)
        ->where('activities.data.0.type', 'project_updated'));
});

test('activity feed includes comment activities', function () {
    $comment = Comment::factory()->create([
        'task_id' => $this->task->id,
        'user_id' => $this->user->id,
    ]);

    // Comment activities via ActivityLogger would set project_id
    Activity::create([
        'project_id' => $this->project->id,
        'task_id' => $this->task->id,
        'type' => 'comment_created',
        'description' => 'Menambahkan komentar',
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('activities.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('activities/index')
        ->has('activities.data', 1)
        ->where('activities.data.0.type', 'comment_created'));
});
