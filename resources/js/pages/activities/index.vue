<script setup>
import { usePage, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import activities from '@/routes/activities';
import tasks from '@/routes/tasks';
import projects from '@/routes/projects';

const page = usePage();
const { activities: activitiesData } = page.props;

function goToPage(pageNum) {
    router.get(activities.index.url({ page: pageNum }), {
        preserveScroll: true,
    });
}

function formatRelativeDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Baru saja';
    if (diffMins < 60) return `${diffMins} menit lalu`;
    if (diffHours < 24) return `${diffHours} jam lalu`;
    if (diffDays < 7) return `${diffDays} hari lalu`;
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function getActivityColor(type) {
    const colors = {
        project_created: 'bg-blue-100 text-blue-800',
        project_updated: 'bg-yellow-100 text-yellow-800',
        task_created: 'bg-blue-100 text-blue-800',
        task_updated: 'bg-yellow-100 text-yellow-800',
        task_deleted: 'bg-red-100 text-red-800',
        task_status_changed: 'bg-purple-100 text-purple-800',
        task_assigned: 'bg-purple-100 text-purple-800',
        comment_created: 'bg-blue-100 text-blue-800',
        comment_updated: 'bg-yellow-100 text-yellow-800',
        comment_deleted: 'bg-red-100 text-red-800',
    };
    return colors[type] ?? 'bg-gray-100 text-gray-800';
}

function getActivityLabel(type) {
    const labels = {
        project_created: 'Membuat project',
        project_updated: 'Memperbarui project',
        task_created: 'Membuat tugas',
        task_updated: 'Memperbarui tugas',
        task_deleted: 'Menghapus tugas',
        task_status_changed: 'Mengubah status tugas',
        task_assigned: 'Menugaskan tugas',
        comment_created: 'Menambahkan komentar',
        comment_updated: 'Mengubah komentar',
        comment_deleted: 'Menghapus komentar',
    };
    return labels[type] ?? type;
}
</script>

<template>
    <AppLayout>
        <template #title>Riwayat Aktivitas</template>

        <div class="max-w-4xl">
            <h2 class="mb-6 text-lg font-medium text-gray-900">
                Riwayat Aktivitas
            </h2>

            <div v-if="activitiesData.data.length > 0" class="space-y-3">
                <div
                    v-for="activity in activitiesData.data"
                    :key="activity.id"
                    class="flex items-start gap-3 rounded-lg bg-gray-50 p-4"
                >
                    <div
                        class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full"
                        :class="getActivityColor(activity.type)"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-900">
                            <span class="font-medium">{{
                                activity.user?.name ?? 'Sistem'
                            }}</span>
                            <span class="mx-2 text-gray-500">•</span>
                            <span>{{ getActivityLabel(activity.type) }}</span>
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ formatRelativeDate(activity.created_at) }}
                        </p>

                        <div
                            v-if="activity.project || activity.task"
                            class="mt-2 flex flex-wrap gap-2"
                        >
                            <Link
                                v-if="activity.project"
                                :href="
                                    projects.show.url({
                                        project: activity.project.id,
                                    })
                                "
                                class="inline-flex items-center gap-1 rounded bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700"
                            >
                                <svg
                                    class="h-3 w-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                                    />
                                </svg>
                                {{ activity.project.name }}
                            </Link>
                            <Link
                                v-if="activity.task"
                                :href="
                                    tasks.show.url({ task: activity.task.id })
                                "
                                class="inline-flex items-center gap-1 rounded bg-green-50 px-2 py-1 text-xs font-medium text-green-700"
                            >
                                <svg
                                    class="h-3 w-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                                    />
                                </svg>
                                {{ activity.task.title }}
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="activitiesData.last_page > 1"
                    class="mt-6 border-t border-gray-200 pt-4"
                >
                    <nav class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Menampilkan {{ activitiesData.from }} sampai
                            {{ activitiesData.to }} dari
                            {{ activitiesData.total }} aktivitas
                        </div>
                        <div class="flex gap-2">
                            <button
                                v-if="activitiesData.prev_page_url"
                                @click="
                                    goToPage(activitiesData.current_page - 1)
                                "
                                class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Sebelumnya
                            </button>
                            <button
                                v-if="activitiesData.next_page_url"
                                @click="
                                    goToPage(activitiesData.current_page + 1)
                                "
                                class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Selanjutnya
                            </button>
                        </div>
                    </nav>
                </div>
            </div>

            <div v-else class="py-12 text-center">
                <div
                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100"
                >
                    <svg
                        class="h-8 w-8 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
                <h3 class="mb-1 text-lg font-medium text-gray-900">
                    Belum ada aktivitas
                </h3>
                <p class="text-sm text-gray-500">
                    Riwayat aktivitas akan muncul di sini saat ada aktivitas
                    pada project yang Anda ikuti.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
