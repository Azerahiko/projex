<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case ProjectManager = 'project_manager';
    case Employee = 'employee';
    case Client = 'client';

    /**
     * Get the human readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::ProjectManager => 'Project Manager',
            self::Employee => 'Karyawan',
            self::Client => 'Klien',
        };
    }

    /**
     * Get the permissions assigned to this role.
     *
     * @return array<int, string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Admin => [
                'manage_users',
                'manage_invitations',
                'manage_clients',
                'view_admin_dashboard',
                'manage_all_projects',
                'manage_all_tasks',
                'view_all_activities',
                'manage_all_comments',
            ],
            self::ProjectManager => [
                'manage_owned_projects',
                'manage_project_members',
                'manage_project_tasks',
                'view_project_activities',
                'view_pm_dashboard',
            ],
            self::Employee => [
                'view_joined_projects',
                'create_projects',
                'update_assigned_tasks',
                'comment_on_tasks',
                'view_employee_dashboard',
                'view_own_activities',
            ],
            self::Client => [
                'view_client_projects',
                'view_client_dashboard',
            ],
        };
    }

    /**
     * Check if this role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions(), true);
    }

    /**
     * Get all role values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
