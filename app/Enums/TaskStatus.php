<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in-progress';
    case Done = 'done';

    /**
     * Get the human readable label for the task status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Todo => 'Belum Dimulai',
            self::InProgress => 'Sedang Dikerjakan',
            self::Done => 'Selesai',
        };
    }

    /**
     * Get the color used to render the status badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Todo => 'bg-blue-100 text-blue-800',
            self::InProgress => 'bg-yellow-100 text-yellow-800',
            self::Done => 'bg-green-100 text-green-800',
        };
    }

    /**
     * Get all valid status values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
