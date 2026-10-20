<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    public function __construct(
        public readonly Task $task,
        public readonly ?User $assignedBy,
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
        $assignedBy = $this->assignedBy;
        $assignerName = $assignedBy ? $assignedBy->name : 'Sistem';

        return new DatabaseMessage([
            'title' => 'Tugas Ditugaskan',
            'message' => "{$assignerName} menugaskan Anda pada tugas \"{$this->task->title}\"",
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
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
