<?php

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

test('user can view comments on task they have access to', function () {
    Comment::factory()->count(2)->create(['task_id' => $this->task->id]);

    $response = $this->get(route('tasks.show', $this->task));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('tasks/show')
        ->has('comments', 2));
});

test('user can add comment to task in accessible project', function () {
    $response = $this->post(route('tasks.comments.store', $this->task), [
        'body' => 'Ini komentar baru',
    ]);

    $response->assertRedirect(route('tasks.show', $this->task));
    $this->assertDatabaseHas('comments', [
        'task_id' => $this->task->id,
        'user_id' => $this->user->id,
        'body' => 'Ini komentar baru',
    ]);
});

test('user can add comment to task in member project', function () {
    $memberProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $memberProject->members()->attach($this->user->id);
    $task = Task::factory()->create(['project_id' => $memberProject->id]);

    $response = $this->post(route('tasks.comments.store', $task), [
        'body' => 'Komentar sebagai anggota',
    ]);

    $response->assertRedirect(route('tasks.show', $task));
    $this->assertDatabaseHas('comments', [
        'task_id' => $task->id,
        'user_id' => $this->user->id,
    ]);
});

test('user cannot add comment to task in inaccessible project', function () {
    $foreignProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $task = Task::factory()->create(['project_id' => $foreignProject->id]);

    $response = $this->post(route('tasks.comments.store', $task), [
        'body' => 'Komentar terlarang',
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('comments', ['body' => 'Komentar terlarang']);
});

test('comment body is required', function () {
    $response = $this->post(route('tasks.comments.store', $this->task), [
        'body' => '',
    ]);

    $response->assertSessionHasErrors('body');
    $this->assertDatabaseCount('comments', 0);
});

test('comment body has maximum length', function () {
    $response = $this->post(route('tasks.comments.store', $this->task), [
        'body' => str_repeat('a', 10001),
    ]);

    $response->assertSessionHasErrors('body');
    $this->assertDatabaseCount('comments', 0);
});

test('author can update own comment', function () {
    $comment = Comment::factory()->create([
        'task_id' => $this->task->id,
        'user_id' => $this->user->id,
    ]);

    $response = $this->put(route('tasks.comments.update', [$this->task, $comment]), [
        'body' => 'Komentar diperbarui',
    ]);

    $response->assertRedirect(route('tasks.show', $this->task));
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'body' => 'Komentar diperbarui',
    ]);
});

test('project owner can update comment written by member', function () {
    $comment = Comment::factory()->create([
        'task_id' => $this->task->id,
        'user_id' => $this->otherUser->id,
    ]);

    $response = $this->put(route('tasks.comments.update', [$this->task, $comment]), [
        'body' => 'Komentar oleh owner',
    ]);

    $response->assertRedirect(route('tasks.show', $this->task));
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'body' => 'Komentar oleh owner',
    ]);
});

test('member cannot update comment written by someone else', function () {
    $memberProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $memberProject->members()->attach($this->user->id);
    $task = Task::factory()->create(['project_id' => $memberProject->id]);
    $comment = Comment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $this->otherUser->id,
    ]);

    $response = $this->put(route('tasks.comments.update', [$task, $comment]), [
        'body' => 'Komentar dimodifikasi anggota',
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('comments', ['body' => 'Komentar dimodifikasi anggota']);
});

test('author can delete own comment', function () {
    $comment = Comment::factory()->create([
        'task_id' => $this->task->id,
        'user_id' => $this->user->id,
    ]);

    $response = $this->delete(route('tasks.comments.destroy', [$this->task, $comment]));

    $response->assertRedirect(route('tasks.show', $this->task));
    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('project owner can delete comment written by member', function () {
    $comment = Comment::factory()->create([
        'task_id' => $this->task->id,
        'user_id' => $this->otherUser->id,
    ]);

    $response = $this->delete(route('tasks.comments.destroy', [$this->task, $comment]));

    $response->assertRedirect(route('tasks.show', $this->task));
    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('member cannot delete comment written by someone else', function () {
    $memberProject = Project::factory()->create(['user_id' => $this->otherUser->id]);
    $memberProject->members()->attach($this->user->id);
    $task = Task::factory()->create(['project_id' => $memberProject->id]);
    $comment = Comment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $this->otherUser->id,
    ]);

    $response = $this->delete(route('tasks.comments.destroy', [$task, $comment]));

    $response->assertStatus(403);
    $this->assertDatabaseHas('comments', ['id' => $comment->id]);
});

test('comment from another task cannot be managed through wrong task', function () {
    $otherTask = Task::factory()->create(['project_id' => $this->project->id]);
    $comment = Comment::factory()->create([
        'task_id' => $otherTask->id,
        'user_id' => $this->user->id,
    ]);

    $response = $this->delete(route('tasks.comments.destroy', [$this->task, $comment]));

    $response->assertStatus(404);
    $this->assertDatabaseHas('comments', ['id' => $comment->id]);
});

test('guest cannot access comment routes', function () {
    auth()->logout();

    $this->post(route('tasks.comments.store', $this->task), ['body' => 'Anon'])
        ->assertRedirect(route('login'));

    $comment = Comment::factory()->create(['task_id' => $this->task->id]);
    $this->put(route('tasks.comments.update', [$this->task, $comment]), ['body' => 'Anon'])
        ->assertRedirect(route('login'));
    $this->delete(route('tasks.comments.destroy', [$this->task, $comment]))
        ->assertRedirect(route('login'));
});
