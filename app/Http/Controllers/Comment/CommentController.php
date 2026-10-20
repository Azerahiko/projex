<?php

namespace App\Http\Controllers\Comment;

use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Task;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function __construct(
        private ActivityLogger $activityLogger,
    ) {}

    /**
     * Store a newly created comment in storage.
     */
    public function store(StoreCommentRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('comment-on-task', $task);

        $comment = $task->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->validated()['body'],
        ]);

        $this->activityLogger->log(
            ActivityType::CommentCreated,
            "Menambahkan komentar pada tugas \"{$task->title}\"",
            auth()->user(),
            $task->project,
            $task,
            ['comment_id' => $comment->id],
        );

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(UpdateCommentRequest $request, Task $task, Comment $comment): RedirectResponse
    {
        $this->authorize('comment-on-task', $task);
        $this->authorizeCommentManagement($comment, $task);

        $comment->update($request->validated());

        $this->activityLogger->log(
            ActivityType::CommentUpdated,
            "Mengubah komentar pada tugas \"{$task->title}\"",
            auth()->user(),
            $task->project,
            $task,
            ['comment_id' => $comment->id],
        );

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Komentar berhasil diperbarui.');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Task $task, Comment $comment): RedirectResponse
    {
        $this->authorize('comment-on-task', $task);
        $this->authorizeCommentManagement($comment, $task);

        $commentId = $comment->id;
        $comment->delete();

        $this->activityLogger->log(
            ActivityType::CommentDeleted,
            "Menghapus komentar pada tugas \"{$task->title}\"",
            auth()->user(),
            $task->project,
            $task,
            ['comment_id' => $commentId],
        );

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Komentar berhasil dihapus.');
    }

    /**
     * Check if the current user can manage (update/delete) the comment.
     * Only the comment author or project owner can manage comments.
     */
    private function authorizeCommentManagement(Comment $comment, Task $task): void
    {
        // Ensure comment belongs to the task
        if ($comment->task_id !== $task->id) {
            abort(404);
        }

        $user = auth()->user();
        $isAuthor = $comment->user_id === $user->id;
        $isProjectOwner = $comment->task->project->user_id === $user->id;

        // Admin can manage all comments
        if ($user->isAdmin()) {
            return;
        }

        if (! $isAuthor && ! $isProjectOwner) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola komentar ini.');
        }
    }
}
