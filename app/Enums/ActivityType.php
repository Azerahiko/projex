<?php

namespace App\Enums;

enum ActivityType: string
{
    case ProjectCreated = 'project_created';
    case ProjectUpdated = 'project_updated';
    case TaskCreated = 'task_created';
    case TaskUpdated = 'task_updated';
    case TaskDeleted = 'task_deleted';
    case TaskStatusChanged = 'task_status_changed';
    case TaskAssigned = 'task_assigned';
    case CommentCreated = 'comment_created';
    case CommentUpdated = 'comment_updated';
    case CommentDeleted = 'comment_deleted';

    /**
     * Get the human readable label for the activity type.
     */
    public function label(): string
    {
        return match ($this) {
            self::ProjectCreated => 'Membuat project',
            self::ProjectUpdated => 'Memperbarui project',
            self::TaskCreated => 'Membuat tugas',
            self::TaskUpdated => 'Memperbarui tugas',
            self::TaskDeleted => 'Menghapus tugas',
            self::TaskStatusChanged => 'Mengubah status tugas',
            self::TaskAssigned => 'Menugaskan tugas',
            self::CommentCreated => 'Menambahkan komentar',
            self::CommentUpdated => 'Mengubah komentar',
            self::CommentDeleted => 'Menghapus komentar',
        };
    }

    /**
     * Get the color used to render the activity type badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::ProjectCreated, self::TaskCreated, self::CommentCreated => 'bg-blue-100 text-blue-800',
            self::ProjectUpdated, self::TaskUpdated, self::CommentUpdated => 'bg-yellow-100 text-yellow-800',
            self::TaskStatusChanged, self::TaskAssigned => 'bg-purple-100 text-purple-800',
            self::TaskDeleted, self::CommentDeleted => 'bg-red-100 text-red-800',
        };
    }
}
