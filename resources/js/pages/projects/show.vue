<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import projects from '@/routes/projects';
import tasks from '@/routes/tasks';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const { project, activities } = page.props;

const statusColors = {
    todo: 'bg-blue-100 text-blue-800',
    'in-progress': 'bg-yellow-100 text-yellow-800',
    done: 'bg-green-100 text-green-800',
};

const priorityColors = {
    rendah: 'bg-gray-100 text-gray-800',
    sedang: 'bg-yellow-100 text-yellow-800',
    tinggi: 'bg-red-100 text-red-800',
};

const projectStatusColors = {
    aktif: 'bg-green-100 text-green-800',
    arsip: 'bg-gray-100 text-gray-800',
};

const showDeleteModal = ref(false);

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deleteProject = () => {
    router.delete(route('projects.destroy', project.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const formatDateTime = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return (
        date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }) +
        ' ' +
        date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
    );
};

const formatActivityDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const diffMs = Date.now() - date.getTime();
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
};

const getActivityColor = (type) => {
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
};

const getActivityLabel = (type) => {
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
};
</script>

<template>
    <AppLayout>
        <template #title>{{ project.name }}</template>

        <div class="max-w-4xl">
            <div class="rounded-lg bg-white shadow">
                <div
                    class="flex items-start justify-between border-b border-gray-200 p-6"
                >
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ project.name }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ project.description ?? 'Tidak ada deskripsi' }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <Link
                            :href="projects.edit.url({ project: project.id })"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Edit
                        </Link>
                        <Link
                            :href="projects.index.url()"
                            class="rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        >
                            Kembali
                        </Link>
                    </div>
                </div>

                <div class="space-y-6 p-6">
                    <!-- Project Info -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Client</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ project.client?.name ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Pemilik</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ project.owner?.name }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Status</label
                            >
                            <span
                                class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="
                                    projectStatusColors[project.status] ??
                                    'bg-gray-100 text-gray-800'
                                "
                            >
                                {{ project.status }}
                            </span>
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Periode</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ formatDate(project.start_date) }} -
                                {{ formatDate(project.end_date) }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Dibuat</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ formatDateTime(project.created_at) }}
                            </p>
                        </div>
                    </div>

                    <!-- Members -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                Anggota Project ({{
                                    project.members?.length ?? 0
                                }})
                            </h3>
                        </div>
                        <div
                            v-if="project.members && project.members.length > 0"
                            class="space-y-2"
                        >
                            <div
                                v-for="member in project.members"
                                :key="member.id"
                                class="flex items-center justify-between rounded-lg bg-gray-50 p-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100"
                                    >
                                        <span
                                            class="text-sm font-medium text-blue-700"
                                            >{{ member.name.charAt(0) }}</span
                                        >
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ member.name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ member.email }}
                                        </p>
                                    </div>
                                </div>
                                <span
                                    v-if="member.id === project.user_id"
                                    class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800"
                                >
                                    Pemilik
                                </span>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-500">
                            Belum ada anggota. Pemilik project adalah
                            satu-satunya anggota.
                        </p>
                    </div>

                    <!-- Tasks -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                Tugas ({{ project.tasks?.length ?? 0 }})
                            </h3>
                            <Link
                                :href="
                                    tasks.create.url({ project: project.id })
                                "
                                class="rounded-lg border border-transparent bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                            >
                                + Tugas Baru
                            </Link>
                        </div>
                        <div
                            v-if="project.tasks && project.tasks.length > 0"
                            class="space-y-2"
                        >
                            <div
                                v-for="task in project.tasks"
                                :key="task.id"
                                class="flex items-center justify-between rounded-lg bg-gray-50 p-3"
                            >
                                <div class="flex items-center gap-3">
                                    <Link
                                        :href="
                                            tasks.show.url({ task: task.id })
                                        "
                                        class="text-sm font-medium text-gray-900 hover:text-blue-600"
                                    >
                                        {{ task.title }}
                                    </Link>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                        :class="
                                            statusColors[task.status] ??
                                            'bg-gray-100 text-gray-800'
                                        "
                                    >
                                        {{ task.status }}
                                    </span>
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                        :class="
                                            priorityColors[task.priority] ??
                                            'bg-gray-100 text-gray-800'
                                        "
                                    >
                                        {{ task.priority }}
                                    </span>
                                    <span
                                        v-if="task.assignee"
                                        class="text-xs text-gray-500"
                                        >{{ task.assignee.name }}</span
                                    >
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-500">
                            Belum ada tugas untuk project ini.
                        </p>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            Riwayat Aktivitas
                        </h3>
                        <div
                            v-if="activities && activities.length > 0"
                            class="space-y-3"
                        >
                            <div
                                v-for="activity in activities"
                                :key="activity.id"
                                class="flex items-start gap-3 rounded-lg bg-gray-50 p-3"
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
                                        <span class="mx-2 text-gray-500"
                                            >•</span
                                        >
                                        <span>{{
                                            getActivityLabel(activity.type)
                                        }}</span>
                                        <span
                                            v-if="activity.task"
                                            class="mx-2 text-gray-500"
                                            >•</span
                                        >
                                        <Link
                                            v-if="activity.task"
                                            :href="
                                                tasks.show.url({
                                                    task: activity.task.id,
                                                })
                                            "
                                            class="font-medium text-blue-600 hover:text-blue-900"
                                        >
                                            {{ activity.task.title }}
                                        </Link>
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{
                                            formatActivityDate(
                                                activity.created_at,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p
                            v-else
                            class="py-4 text-center text-sm text-gray-500"
                        >
                            Belum ada aktivitas.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Delete button at bottom -->
            <div class="mt-6 text-right">
                <button
                    type="button"
                    @click="confirmDelete"
                    class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50"
                >
                    Hapus Project
                </button>
            </div>

            <!-- Delete confirmation modal -->
            <div
                v-if="showDeleteModal"
                class="fixed inset-0 z-50 overflow-y-auto"
            >
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="fixed inset-0 bg-gray-900/50"
                        @click="showDeleteModal = false"
                    />
                    <div
                        class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl"
                    >
                        <h3 class="mb-2 text-lg font-medium text-gray-900">
                            Hapus Project
                        </h3>
                        <p class="mb-4 text-gray-600">
                            Apakah Anda yakin ingin menghapus project
                            <strong>{{ project.name }}</strong
                            >? Tindakan ini tidak dapat dibatalkan dan akan
                            menghapus semua tugas di dalamnya.
                        </p>
                        <div class="flex justify-end gap-3">
                            <button
                                type="button"
                                @click="showDeleteModal = false"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                @click="deleteProject"
                                class="rounded-lg border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
