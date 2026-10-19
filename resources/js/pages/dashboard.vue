<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage, Link } from '@inertiajs/vue3';
import projects from '@/routes/projects';
import tasks from '@/routes/tasks';

const page = usePage();
const { auth, activeProjects, archivedProjects, totalProjects, assignedTasks, taskStats } = page.props;
</script>

<template>
    <AppLayout>
        <template #title>Dashboard</template>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-2">
                    Selamat Datang, {{ auth.user.name }}
                </h2>
                <p class="text-gray-600">
                    Ringkasan project dan tugas Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Projects card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                        <h3 class="font-medium text-gray-900">Total Project</h3>
                    </div>
                    <Link :href="projects.index.url()" class="block">
                        <p class="text-3xl font-bold text-gray-900">{{ totalProjects }}</p>
                    </Link>
                    <p class="text-sm text-gray-500">Project Anda</p>
                </div>

                <!-- Active Projects card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <!-- Assigned Tasks card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 class="font-medium text-gray-900">Tugas Anda</h3>
                    </div>
                    <Link :href="tasks.index.url()" class="block">
                        <p class="text-3xl font-bold text-gray-900">{{ taskStats.todo + taskStats.in_progress + taskStats.done }}</p>
                    </Link>
                    <p class="text-sm text-gray-500">Total tugas</p>
                </div>
            </div>

            <!-- Task Status Summary -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Status Tugas</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-blue-800">Belum Dimulai</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ taskStats.todo }}</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-yellow-800">Sedang Dikerjakan</p>
                        <p class="text-2xl font-bold text-yellow-900 mt-1">{{ taskStats.in_progress }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-green-800">Selesai</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">{{ taskStats.done }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Assigned Tasks -->
            <div v-if="assignedTasks && assignedTasks.length > 0" class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Tugas Terbaru untuk Anda</h3>
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
                        class="p-4 hover:bg-gray-50 flex items-center justify-between"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                    task.status === 'todo' ? 'bg-blue-100 text-blue-800' :
                                    task.status === 'in-progress' ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-green-100 text-green-800'
                                ]"
                            >
                                {{ task.status }}
                            </span>
                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                    task.priority === 'rendah' ? 'bg-gray-100 text-gray-800' :
                                    task.priority === 'sedang' ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-red-100 text-red-800'
                                ]"
                            >
                                {{ task.priority }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white rounded-lg shadow p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada tugas</h3>
                <p class="text-gray-500 text-sm mb-4">Tugas yang ditugaskan kepada Anda akan muncul di sini.</p>
            </div>
        </div>
    </AppLayout>
</template>