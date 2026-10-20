<?php

namespace App\Notifications;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class TaskStatusChangedNotification extends Notification
{
    public function __construct(
        public readonly Task $task,
        public readonly string $oldStatus,
        public readonly string $newStatus,
        public readonly ?User $changedBy,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $changedBy = $this->changedBy;
        $changerName = $changedBy ? $changedBy->name : 'Sistem';
        $oldLabel = TaskStatus::tryFrom($this->oldStatus)?->label() ?? $this->oldStatus;
        $newLabel = TaskStatus::tryFrom($this->newStatus)?->label() ?? $this->newStatus;

        return new DatabaseMessage([
            'title' => 'Status Tugas Diubah',
            'message' => "{$changerName} mengubah status tugas \"{$this->task->title}\" dari \"{$oldLabel}\" menjadi \"{$newLabel}\"",
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'action_url' => route('tasks.show', $this->task),
            'action_text' => 'Lihat Tugas',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable)->data;
    }
}
