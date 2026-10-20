<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
});

test('user can view tasks index', function () {
    Task::factory()->count(3)->create(['project_id' => $this->project->id]);
    $foreignProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    Task::factory()->count(2)->create(['project_id' => $foreignProject->id]);

    $response = $this->get(route('tasks.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('tasks/index')
        ->has('tasks.data', 3));
});

test('user can view only tasks from accessible projects', function () {
    $ownProject = Project::factory()->create(['user_id' => $this->user->id]);
    $ownTask = Task::factory()->create(['project_id' => $ownProject->id, 'title' => 'Own Task']);
    $memberProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $memberProject->members()->attach($this->user->id);
    $memberTask = Task::factory()->create(['project_id' => $memberProject->id, 'title' => 'Member Task']);
    $otherProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    Task::factory()->create(['project_id' => $otherProject->id, 'title' => 'Other Task']);

    $response = $this->get(route('tasks.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('tasks/index')
        ->has('tasks.data', 2)
        ->where('tasks.data', fn ($tasks) => collect($tasks)->pluck('title')->contains('Own Task') &&
            collect($tasks)->pluck('title')->contains('Member Task')
        ));
});

test('user can filter tasks by status', function () {
    Task::factory()->create(['project_id' => $this->project->id, 'status' => 'todo', 'title' => 'Todo Task']);
    Task::factory()->create(['project_id' => $this->project->id, 'status' => 'done', 'title' => 'Done Task']);
    Task::factory()->create(['project_id' => $this->project->id, 'status' => 'in-progress', 'title' => 'Progress Task']);

    $response = $this->get(route('tasks.index', ['status' => 'done']));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('tasks/index')
        ->has('tasks.data', 1)
        ->where('tasks.data.0.title', 'Done Task'));
});

test('user can filter tasks by project', function () {
    $project1 = Project::factory()->create(['user_id' => $this->user->id, 'name' => 'Project A']);
    $project2 = Project::factory()->create(['user_id' => $this->user->id, 'name' => 'Project B']);
    Task::factory()->create(['project_id' => $project1->id, 'title' => 'Task A']);
    Task::factory()->create(['project_id' => $project2->id, 'title' => 'Task B']);

    $response = $this->get(route('tasks.index', ['project' => $project1->id]));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('tasks/index')
        ->has('tasks.data', 1)
        ->where('tasks.data.0.title', 'Task A'));
});

test('user can create task', function () {
    $data = [
        'project_id' => $this->project->id,
        'title' => 'New Task',
        'description' => 'Task description',
        'status' => 'todo',
        'priority' => 'tinggi',
        'due_date' => '2025-12-31',
        'assigned_to' => null,
    ];

    $response = $this->post(route('tasks.store'), $data);

    $response->assertRedirect(route('tasks.show', Task::first()->id));
    $this->assertDatabaseHas('tasks', [
        'title' => 'New Task',
        'project_id' => $this->project->id,
        'priority' => 'tinggi',
        'status' => 'todo',
    ]);
});

test('user cannot create task with invalid data', function () {
    $response = $this->post(route('tasks.store'), [
        'project_id' => '',
        'title' => '',
        'status' => 'invalid',
        'priority' => 'invalid',
    ]);

    $response->assertSessionHasErrors(['project_id', 'title', 'status', 'priority']);
});

test('user cannot create task for inaccessible project', function () {
    $foreignProject = Project::factory()->create(['user_id' => $this->otherUser->id]);

    $response = $this->post(route('tasks.store'), [
        'project_id' => $foreignProject->id,
        'title' => 'Hacked Task',
        'status' => 'todo',
        'priority' => 'sedang',
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('tasks', ['title' => 'Hacked Task']);
});

test('task defaults to status todo and priority sedang when not provided', function () {
    $data = [
        'project_id' => $this->project->id,
        'title' => 'Default Task',
    ];

    $response = $this->post(route('tasks.store'), $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('tasks', [
        'title' => 'Default Task',
        'status' => 'todo',
        'priority' => 'sedang',
    ]);
});

test('user can create task assigned to project member', function () {
    $member = User::factory()->create();
    $this->project->members()->attach($member->id);

    $data = [
        'project_id' => $this->project->id,
        'title' => 'Assigned Task',
        'assigned_to' => $member->id,
    ];

    $response = $this->post(route('tasks.store'), $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('tasks', [
        'title' => 'Assigned Task',
        'assigned_to' => $member->id,
    ]);
});

test('user cannot assign task to non-member', function () {
    $nonMember = User::factory()->create();

    $data = [
        'project_id' => $this->project->id,
        'title' => 'Invalid Assignee',
        'assigned_to' => $nonMember->id,
    ];

    $response = $this->post(route('tasks.store'), $data);

    $response->assertSessionHasErrors('assigned_to');
});

test('user can view task they own project of', function () {
    $task = Task::factory()->create(['project_id' => $this->project->id]);

    $response = $this->get(route('tasks.show', $task));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('tasks/show')
        ->where('task.id', $task->id));
});

test('user can view task from member project', function () {
    $memberProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $memberProject->members()->attach($this->user->id);
    $task = Task::factory()->create(['project_id' => $memberProject->id]);

    $response = $this->get(route('tasks.show', $task));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('tasks/show')
        ->where('task.id', $task->id));
});

test('user cannot view task from inaccessible project', function () {
    $foreignProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $task = Task::factory()->create(['project_id' => $foreignProject->id]);

    $response = $this->get(route('tasks.show', $task));

    $response->assertStatus(403);
});

test('user can edit their own project task', function () {
    $task = Task::factory()->create(['project_id' => $this->project->id]);

    $response = $this->get(route('tasks.edit', $task));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('tasks/edit')
        ->where('task.id', $task->id));
});

test('user cannot edit task from inaccessible project', function () {
    $foreignProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $task = Task::factory()->create(['project_id' => $foreignProject->id]);

    $response = $this->get(route('tasks.edit', $task));

    $response->assertStatus(403);
});

test('user can update their own project task', function () {
    $task = Task::factory()->create([
        'project_id' => $this->project->id,
        'title' => 'Old Title',
        'status' => 'todo',
        'priority' => 'rendah',
    ]);

    $data = [
        'project_id' => $this->project->id,
        'title' => 'Updated Title',
        'status' => 'done',
        'priority' => 'tinggi',
    ];

    $response = $this->put(route('tasks.update', $task), $data);

    $response->assertRedirect(route('tasks.show', $task));
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated Title',
        'status' => 'done',
        'priority' => 'tinggi',
    ]);
});

test('user cannot update task from inaccessible project', function () {
    $foreignProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $task = Task::factory()->create(['project_id' => $foreignProject->id]);

    $response = $this->put(route('tasks.update', $task), [
        'project_id' => $foreignProject->id,
        'title' => 'Hacked Title',
        'status' => 'todo',
        'priority' => 'sedang',
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('tasks', ['title' => 'Hacked Title']);
});

test('user can move task to another accessible project', function () {
    $task = Task::factory()->create(['project_id' => $this->project->id]);
    $otherProject = Project::factory()->create(['user_id' => $this->user->id]);

    $data = [
        'project_id' => $otherProject->id,
        'title' => $task->title,
        'status' => 'todo',
        'priority' => 'sedang',
    ];

    $response = $this->put(route('tasks.update', $task), $data);

    $response->assertRedirect(route('tasks.show', $task));
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'project_id' => $otherProject->id,
    ]);
});

test('user cannot move task to inaccessible project', function () {
    $task = Task::factory()->create(['project_id' => $this->project->id]);
    $foreignProject = Project::factory()->create(['user_id' => $this->otherUser->id]);

    $response = $this->put(route('tasks.update', $task), [
        'project_id' => $foreignProject->id,
        'title' => $task->title,
        'status' => 'todo',
        'priority' => 'sedang',
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('tasks', ['project_id' => $foreignProject->id]);
});

test('user can delete their own project task', function () {
    $task = Task::factory()->create(['project_id' => $this->project->id]);

    $response = $this->delete(route('tasks.destroy', $task));

    $response->assertRedirect(route('tasks.index'));
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('user cannot delete task from inaccessible project', function () {
    $foreignProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $task = Task::factory()->create(['project_id' => $foreignProject->id]);

    $response = $this->delete(route('tasks.destroy', $task));

    $response->assertStatus(403);
    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
});

test('task requires valid status values', function () {
    $response = $this->post(route('tasks.store'), [
        'project_id' => $this->project->id,
        'title' => 'Test',
        'status' => 'invalid_status',
        'priority' => 'sedang',
    ]);

    $response->assertSessionHasErrors('status');
});

test('task requires valid priority values', function () {
    $response = $this->post(route('tasks.store'), [
        'project_id' => $this->project->id,
        'title' => 'Test',
        'status' => 'todo',
        'priority' => 'invalid_priority',
    ]);

    $response->assertSessionHasErrors('priority');
});

test('task requires valid due_date', function () {
    $response = $this->post(route('tasks.store'), [
        'project_id' => $this->project->id,
        'title' => 'Test',
        'status' => 'todo',
        'priority' => 'sedang',
        'due_date' => 'invalid-date',
    ]);

    $response->assertSessionHasErrors('due_date');
});
