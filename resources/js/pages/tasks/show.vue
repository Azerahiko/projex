<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import tasks from '@/routes/tasks';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const { task } = page.props;

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

<script>
import { ref } from 'vue';

export default {
    setup() {
        const showDeleteModal = ref(false);

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
                date.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                })
            );
        };

        return {
            showDeleteModal,
            confirmDelete,
            deleteTask,
            formatDate,
            formatDateTime,
        };
    },
};
</script>
