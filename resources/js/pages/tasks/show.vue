<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import tasks from '@/routes/tasks';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const { task, comments, activities } = page.props;

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

const statusLabels = {
    todo: 'Belum Dimulai',
    'in-progress': 'Sedang Dikerjakan',
    done: 'Selesai',
};

const priorityLabels = {
    rendah: 'Rendah',
    sedang: 'Sedang',
    tinggi: 'Tinggi',
};

const showDeleteModal = ref(false);
const showCommentForm = ref(false);
const commentBody = ref('');

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deleteTask = () => {
    router.delete(route('tasks.destroy', task.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
};

const addComment = () => {
    if (!commentBody.value.trim()) return;
    router.post(
        route('tasks.comments.store', task.id),
        {
            body: commentBody.value,
        },
        {
            onSuccess: () => {
                commentBody.value = '';
                showCommentForm.value = false;
            },
        },
    );
};

const editComment = (comment) => {
    comment.editing = true;
    comment.editBody = comment.body;
};

const saveComment = (comment) => {
    if (!comment.editBody.trim()) return;
    router.put(
        route('tasks.comments.update', [task.id, comment.id]),
        {
            body: comment.editBody,
        },
        {
            onSuccess: () => {
                comment.editing = false;
            },
        },
    );
};

const cancelEditComment = (comment) => {
    comment.editing = false;
    comment.editBody = comment.body;
};

const deleteComment = (comment) => {
    if (!confirm('Apakah Anda yakin ingin menghapus komentar ini?')) return;
    router.delete(route('tasks.comments.destroy', [task.id, comment.id]));
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
        <template #title>{{ task.title }}</template>

        <div class="max-w-3xl">
            <div class="rounded-lg bg-white shadow">
                <div
                    class="flex flex-col gap-4 border-b border-gray-200 p-6 sm:flex-row sm:items-start sm:justify-between"
                >
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ task.title }}
                        </h2>
                        <p
                            v-if="task.description"
                            class="mt-1 text-sm text-gray-500"
                        >
                            {{ task.description }}
                        </p>
                    </div>
                    <div
                        class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row"
                    >
                        <Link
                            :href="tasks.edit.url({ task: task.id })"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Edit
                        </Link>
                        <Link
                            :href="tasks.index.url()"
                            class="rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-blue-700"
                        >
                            Kembali
                        </Link>
                    </div>
                </div>

                <div class="space-y-6 p-6">
                    <!-- Task Info -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Project</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ task.project?.name }}
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
                                    statusColors[task.status] ??
                                    'bg-gray-100 text-gray-800'
                                "
                            >
                                {{ statusLabels[task.status] ?? task.status }}
                            </span>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Prioritas</label
                            >
                            <span
                                class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="
                                    priorityColors[task.priority] ??
                                    'bg-gray-100 text-gray-800'
                                "
                            >
                                {{
                                    priorityLabels[task.priority] ??
                                    task.priority
                                }}
                            </span>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Deadline</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ formatDate(task.due_date) }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Penanggung Jawab</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ task.assignee?.name ?? '-' }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Dibuat</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ formatDateTime(task.created_at) }}
                            </p>
                        </div>
                        <div
                            v-if="task.updated_at !== task.created_at"
                            class="md:col-span-2"
                        >
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Diperbarui</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ formatDateTime(task.updated_at) }}
                            </p>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                Komentar ({{ comments?.length ?? 0 }})
                            </h3>
                            <button
                                v-if="!showCommentForm"
                                @click="showCommentForm = true"
                                class="rounded-lg border border-transparent bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                            >
                                + Tambah Komentar
                            </button>
                        </div>

                        <!-- Comment Form -->
                        <div
                            v-if="showCommentForm"
                            class="mb-4 rounded-lg bg-gray-50 p-4"
                        >
                            <textarea
                                v-model="commentBody"
                                rows="3"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Tulis komentar..."
                            />
                            <div class="mt-3 flex justify-end gap-2">
                                <button
                                    type="button"
                                    @click="showCommentForm = false"
                                    class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="button"
                                    @click="addComment"
                                    class="rounded-lg border border-transparent bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                                >
                                    Kirim
                                </button>
                            </div>
                        </div>

                        <!-- Comments List -->
                        <div
                            v-if="comments && comments.length > 0"
                            class="space-y-4"
                        >
                            <div
                                v-for="comment in comments"
                                :key="comment.id"
                                class="rounded-lg bg-gray-50 p-4"
                            >
                                <div
                                    class="flex items-start justify-between gap-4"
                                >
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-blue-100"
                                            >
                                                <span
                                                    class="text-sm font-medium text-blue-700"
                                                    >{{
                                                        comment.author?.name?.charAt(
                                                            0,
                                                        ) ?? '?'
                                                    }}</span
                                                >
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm font-medium text-gray-900"
                                                >
                                                    {{ comment.author?.name }}
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{
                                                        formatDateTime(
                                                            comment.created_at,
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <p v-if="comment.editing" class="mt-2">
                                            <textarea
                                                v-model="comment.editBody"
                                                rows="2"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            />
                                        </p>
                                        <p
                                            v-else
                                            class="mt-2 whitespace-pre-wrap text-gray-700"
                                        >
                                            {{ comment.body }}
                                        </p>
                                    </div>
                                    <div
                                        class="flex flex-shrink-0 items-center gap-2"
                                    >
                                        <template v-if="comment.editing">
                                            <button
                                                @click="saveComment(comment)"
                                                class="rounded-lg bg-blue-600 px-3 py-1 text-sm font-medium text-white hover:bg-blue-700"
                                            >
                                                Simpan
                                            </button>
                                            <button
                                                @click="
                                                    cancelEditComment(comment)
                                                "
                                                class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                            >
                                                Batal
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button
                                                @click="editComment(comment)"
                                                class="text-sm text-gray-600 hover:text-gray-900"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="deleteComment(comment)"
                                                class="text-sm text-red-600 hover:text-red-900"
                                            >
                                                Hapus
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p
                            v-else
                            class="py-4 text-center text-sm text-gray-500"
                        >
                            Belum ada komentar.
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
                                        <span
                                            v-if="activity.task"
                                            class="font-medium text-blue-600"
                                            >{{ activity.task.title }}</span
                                        >
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
                    Hapus Tugas
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
                            Hapus Tugas
                        </h3>
                        <p class="mb-4 text-gray-600">
                            Apakah Anda yakin ingin menghapus tugas
                            <strong>{{ task.title }}</strong
                            >? Tindakan ini tidak dapat dibatalkan.
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
                                @click="deleteTask"
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
