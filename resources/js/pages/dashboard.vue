<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage, Link } from '@inertiajs/vue3';
import projects from '@/routes/projects';
import tasks from '@/routes/tasks';
import adminUsers from '@/routes/admin/users';
import adminInvitations from '@/routes/admin/invitations';

const page = usePage();
const {
    auth,
    activeProjects,
    archivedProjects,
    totalProjects,
    assignedTasks,
    taskStats,
    dashboardType,
    totalTasks,
    totalUsers,
    usersByRole,
} = page.props;
</script>

<template>
    <AppLayout>
        <template #title>Dashboard</template>

        <div class="space-y-6">
            <div class="rounded-lg bg-white p-6 shadow">
                <h2 class="mb-2 text-lg font-medium text-gray-900">
                    Selamat Datang, {{ auth.user.name }}
                </h2>
                <p class="text-gray-600">
                    <template v-if="dashboardType === 'admin'">Ringkasan keseluruhan organisasi.</template>
                    <template v-else-if="dashboardType === 'project_manager'">Ringkasan project yang Anda kelola.</template>
                    <template v-else-if="dashboardType === 'employee'">Ringkasan project dan tugas Anda.</template>
                    <template v-else-if="dashboardType === 'client'">Ringkasan project klien Anda.</template>
                    <template v-else>Ringkasan project dan tugas Anda.</template>
                </p>
            </div>

            <!-- Admin Dashboard -->
            <div v-if="dashboardType === 'admin'" class="space-y-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                            </div>
                            <h3 class="font-medium text-gray-900">Total Project</h3>
                        </div>
                        <Link :href="projects.index.url()" class="block">
                            <p class="text-3xl font-bold text-gray-900">{{ totalProjects }}</p>
                        </Link>
                        <p class="text-sm text-gray-500">Keseluruhan</p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="font-medium text-gray-900">Project Aktif</h3>
                        </div>
                        <Link :href="projects.index.url()" class="block">
                            <p class="text-3xl font-bold text-gray-900">{{ activeProjects }}</p>
                        </Link>
                        <p class="text-sm text-gray-500">Sedang berjalan</p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100">
                                <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="font-medium text-gray-900">Total Pengguna</h3>
                        </div>
                        <Link :href="adminUsers.url()" class="block">
                            <p class="text-3xl font-bold text-gray-900">{{ totalUsers }}</p>
                        </Link>
                        <p class="text-sm text-gray-500">Keseluruhan</p>
                    </div>
                </div>

                <!-- Task Stats -->
                <div class="rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900">Status Tugas (Keseluruhan)</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3">
                        <div class="rounded-lg bg-blue-50 p-4">
                            <p class="text-sm font-medium text-blue-800">Belum Dimulai</p>
                            <p class="mt-1 text-2xl font-bold text-blue-900">{{ taskStats.todo }}</p>
                        </div>
                        <div class="rounded-lg bg-yellow-50 p-4">
                            <p class="text-sm font-medium text-yellow-800">Sedang Dikerjakan</p>
                            <p class="mt-1 text-2xl font-bold text-yellow-900">{{ taskStats.in_progress }}</p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-800">Selesai</p>
                            <p class="mt-1 text-2xl font-bold text-green-900">{{ taskStats.done }}</p>
                        </div>
                    </div>
                </div>

                <!-- Users by Role -->
                <div class="rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900">Pengguna per Peran</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-4">
                        <div class="rounded-lg bg-blue-50 p-4">
                            <p class="text-sm font-medium text-blue-800">Administrator</p>
                            <p class="mt-1 text-2xl font-bold text-blue-900">{{ usersByRole.admin }}</p>
                        </div>
                        <div class="rounded-lg bg-purple-50 p-4">
                            <p class="text-sm font-medium text-purple-800">Project Manager</p>
                            <p class="mt-1 text-2xl font-bold text-purple-900">{{ usersByRole.project_manager }}</p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-800">Karyawan</p>
                            <p class="mt-1 text-2xl font-bold text-green-900">{{ usersByRole.employee }}</p>
                        </div>
                        <div class="rounded-lg bg-orange-50 p-4">
                            <p class="text-sm font-medium text-orange-800">Klien</p>
                            <p class="mt-1 text-2xl font-bold text-orange-900">{{ usersByRole.client }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900">Aksi Cepat</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <Link
                            :href="adminUsers.url()"
                            class="flex items-center gap-3 rounded-lg border border-gray-200 p-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Kelola Pengguna</p>
                                <p class="text-sm text-gray-500">Lihat, ubah peran, atau hapus pengguna</p>
                            </div>
                        </Link>
                        <Link
                            :href="adminInvitations.url()"
                            class="flex items-center gap-3 rounded-lg border border-gray-200 p-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                                <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Kelola Undangan</p>
                                <p class="text-sm text-gray-500">Kirim, kirim ulang, atau batalkan undangan</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- PM / Employee / Client Dashboard (Shared Layout) -->
            <div v-else class="space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Total Projects card -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                            </div>
                            <h3 class="font-medium text-gray-900">Total Project</h3>
                        </div>
                        <Link :href="projects.index.url()" class="block">
                            <p class="text-3xl font-bold text-gray-900">{{ totalProjects }}</p>
                        </Link>
                        <p class="text-sm text-gray-500">
                            <template v-if="dashboardType === 'project_manager'">Project Anda</template>
                            <template v-else-if="dashboardType === 'employee'">Project yang diikuti</template>
                            <template v-else-if="dashboardType === 'client'">Project klien</template>
                            <template v-else>Project Anda</template>
                        </p>
                    </div>

                    <!-- Active Projects card -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="font-medium text-gray-900">Project Aktif</h3>
                        </div>
                        <Link :href="projects.index.url()" class="block">
                            <p class="text-3xl font-bold text-gray-900">{{ activeProjects }}</p>
                        </Link>
                        <p class="text-sm text-gray-500">Sedang berjalan</p>
                    </div>

                    <!-- Archived Projects card -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100">
                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 01-2 2H7a2 2 0 01-2-2V8z" />
                                </svg>
                            </div>
                            <h3 class="font-medium text-gray-900">Project Arsip</h3>
                        </div>
                        <Link :href="projects.index.url()" class="block">
                            <p class="text-3xl font-bold text-gray-900">{{ archivedProjects }}</p>
                        </Link>
                        <p class="text-sm text-gray-500">Sudah selesai/diarisipkan</p>
                    </div>

                    <!-- Assigned/Total Tasks card -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                                <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <h3 class="font-medium text-gray-900">
                                <template v-if="dashboardType === 'employee'">Tugas Anda</template>
                                <template v-else>Total Tugas</template>
                            </h3>
                        </div>
                        <Link :href="tasks.index.url()" class="block">
                            <p class="text-3xl font-bold text-gray-900">
                                <template v-if="totalTasks !== undefined">{{ totalTasks }}</template>
                                <template v-else>{{ taskStats.todo + taskStats.in_progress + taskStats.done }}</template>
                            </p>
                        </Link>
                        <p class="text-sm text-gray-500">
                            <template v-if="dashboardType === 'employee'">Tugas yang ditugaskan</template>
                            <template v-else-if="dashboardType === 'project_manager'">Semua tugas project Anda</template>
                            <template v-else-if="dashboardType === 'client'">Semua tugas project klien</template>
                            <template v-else>Total tugas</template>
                        </p>
                    </div>
                </div>

                <!-- Task Status Summary -->
                <div class="rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900">Status Tugas</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3">
                        <div class="rounded-lg bg-blue-50 p-4">
                            <p class="text-sm font-medium text-blue-800">Belum Dimulai</p>
                            <p class="mt-1 text-2xl font-bold text-blue-900">{{ taskStats.todo }}</p>
                        </div>
                        <div class="rounded-lg bg-yellow-50 p-4">
                            <p class="text-sm font-medium text-yellow-800">Sedang Dikerjakan</p>
                            <p class="mt-1 text-2xl font-bold text-yellow-900">{{ taskStats.in_progress }}</p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-800">Selesai</p>
                            <p class="mt-1 text-2xl font-bold text-green-900">{{ taskStats.done }}</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Assigned Tasks (Employee/PM only) -->
                <div v-if="dashboardType !== 'client' && assignedTasks && assignedTasks.length > 0" class="rounded-lg bg-white shadow">
                    <div class="flex items-center justify-between border-b border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            <template v-if="dashboardType === 'employee'">Tugas Terbaru untuk Anda</template>
                            <template v-else>Tugas Terbaru (Project Anda)</template>
                        </h3>
                        <Link
                            :href="tasks.index.url()"
                            class="text-sm text-blue-600 hover:text-blue-900"
                        >
                            Lihat semua
                        </Link>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div
                            v-for="task in assignedTasks"
                            :key="task.id"
                            class="flex items-center justify-between p-4 hover:bg-gray-50"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <Link
                                        :href="tasks.show.url({ task: task.id })"
                                        class="font-medium text-gray-900 hover:text-blue-600"
                                    >
                                        {{ task.title }}
                                    </Link>
                                    <p class="text-sm text-gray-500">{{ task.project?.name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span :class="[
                                    'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                                    task.status === 'todo' ? 'bg-blue-100 text-blue-800'
                                    : task.status === 'in-progress' ? 'bg-yellow-100 text-yellow-800'
                                    : 'bg-green-100 text-green-800',
                                ]">
                                    {{ task.status }}
                                </span>
                                <span :class="[
                                    'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                                    task.priority === 'rendah' ? 'bg-gray-100 text-gray-800'
                                    : task.priority === 'sedang' ? 'bg-yellow-100 text-yellow-800'
                                    : 'bg-red-100 text-red-800',
                                ]">
                                    {{ task.priority }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="dashboardType !== 'client'" class="rounded-lg bg-white p-12 text-center shadow">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="mb-1 text-lg font-medium text-gray-900">Belum ada tugas</h3>
                    <p class="mb-4 text-sm text-gray-500">
                        <template v-if="dashboardType === 'employee'">Tugas yang ditugaskan kepada Anda akan muncul di sini.</template>
                        <template v-else>Tugas dalam project Anda akan muncul di sini.</template>
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>