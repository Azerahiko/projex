<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
    $this->task = Task::factory()->create(['project_id' => $this->project->id]);
    $this->actingAs($this->user);
});

test('user can view their notifications', function () {
    $this->user->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => 'test',
        'data' => ['title' => 'Test', 'message' => 'Test message'],
    ]);

    $response = $this->get(route('notifications.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('notifications/index')
        ->has('notifications.data', 1));
});

test('notifications paginated', function () {
    for ($i = 0; $i < 20; $i++) {
        $this->user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'test',
            'data' => ['title' => "Test $i", 'message' => 'Test message'],
        ]);
    }

    $response = $this->get(route('notifications.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('notifications/index')
        ->has('notifications.data', 15));
});

test('user can mark notification as read', function () {
    $notification = $this->user->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => 'test',
        'data' => ['title' => 'Test', 'message' => 'Test message'],
    ]);

    $response = $this->post(route('notifications.read', $notification->id));

    $response->assertRedirect(route('notifications.index'));
    $this->assertDatabaseHas('notifications', [
        'id' => $notification->id,
    ]);
    $this->assertNotNull($notification->fresh()->read_at);
});

test('user can mark all notifications as read', function () {
    $this->user->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => 'test1',
        'data' => ['title' => 'Test 1', 'message' => 'Test message'],
    ]);
    $this->user->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => 'test2',
        'data' => ['title' => 'Test 2', 'message' => 'Test message'],
    ]);

    $response = $this->post(route('notifications.read-all'));

    $response->assertRedirect(route('notifications.index'));
    $this->assertEquals(0, $this->user->unreadNotifications()->count());
});

test('user can delete notification', function () {
    $notification = $this->user->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => 'test',
        'data' => ['title' => 'Test', 'message' => 'Test message'],
    ]);

    $response = $this->delete(route('notifications.destroy', $notification->id));

    $response->assertRedirect(route('notifications.index'));
    $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
});

test('user cannot access other users notification', function () {
    $otherNotification = $this->otherUser->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => 'test',
        'data' => ['title' => 'Test', 'message' => 'Test message'],
    ]);

    $response = $this->post(route('notifications.read', $otherNotification->id));

    $response->assertStatus(404);
});

test('guest cannot access notifications', function () {
    auth()->logout();

    $this->get(route('notifications.index'))->assertRedirect(route('login'));
    $this->post(route('notifications.read-all'))->assertRedirect(route('login'));
});

test('assigning task sends notification to assignee', function () {
    $member = User::factory()->create();
    $this->project->members()->attach($member->id);

    $data = [
        'project_id' => $this->project->id,
        'title' => $this->task->title,
        'status' => 'todo',
        'priority' => 'sedang',
        'assigned_to' => $member->id,
    ];

    $this->put(route('tasks.update', $this->task), $data);

    $this->assertDatabaseHas('notifications', [
        'notifiable_id' => $member->id,
        'type' => TaskAssignedNotification::class,
    ]);
});

test('changing task status sends notification to assignee', function () {
    $member = User::factory()->create();
    $this->project->members()->attach($member->id);
    $this->task->update(['assigned_to' => $member->id, 'status' => 'todo']);

    $data = [
        'project_id' => $this->project->id,
        'title' => $this->task->title,
        'status' => 'done',
        'priority' => 'sedang',
    ];

    $this->put(route('tasks.update', $this->task), $data);

    $this->assertDatabaseHas('notifications', [
        'notifiable_id' => $member->id,
        'type' => TaskStatusChangedNotification::class,
    ]);
});

test('actor does not receive notification for own assignment', function () {
    $data = [
        'project_id' => $this->project->id,
        'title' => $this->task->title,
        'status' => 'todo',
        'priority' => 'sedang',
        'assigned_to' => $this->user->id,
    ];

    $this->put(route('tasks.update', $this->task), $data);

    $this->assertDatabaseMissing('notifications', [
        'notifiable_id' => $this->user->id,
        'type' => TaskAssignedNotification::class,
    ]);
});

test('actor does not receive notification for own status change', function () {
    $this->task->update(['assigned_to' => $this->user->id, 'status' => 'todo']);

    $data = [
        'project_id' => $this->project->id,
        'title' => $this->task->title,
        'status' => 'done',
        'priority' => 'sedang',
    ];

    $this->put(route('tasks.update', $this->task), $data);

    $this->assertDatabaseMissing('notifications', [
        'notifiable_id' => $this->user->id,
        'type' => TaskStatusChangedNotification::class,
    ]);
});

test('unread notifications count shared in inertia', function () {
    $this->user->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => 'test',
        'data' => ['title' => 'Test', 'message' => 'Test message'],
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->where('unreadNotificationsCount', 1));
});

test('unread notifications count zero when none', function () {
    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->where('unreadNotificationsCount', 0));
});
