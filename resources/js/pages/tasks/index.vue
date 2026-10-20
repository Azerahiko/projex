<script setup>
import { ref, computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import tasks from '@/routes/tasks';
import projects from '@/routes/projects';

const page = usePage();
const { tasks: tasksData, filters, projects: projectsData } = page.props;

const localFilters = ref({ ...filters });
const deletingTask = ref(null);

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const getStatusColor = (status) => {
    const colors = {
        todo: 'bg-blue-100 text-blue-800',
        'in-progress': 'bg-yellow-100 text-yellow-800',
        done: 'bg-green-100 text-green-800',
    };
    return colors[status] ?? 'bg-gray-100 text-gray-800';
};

const getPriorityColor = (priority) => {
    const colors = {
        rendah: 'bg-gray-100 text-gray-800',
        sedang: 'bg-yellow-100 text-yellow-800',
        tinggi: 'bg-red-100 text-red-800',
    };
    return colors[priority] ?? 'bg-gray-100 text-gray-800';
};

const applyFilters = () => {
    router.get(
        route('tasks.index', {
            status: localFilters.value.status,
            project: localFilters.value.project,
        }),
        {
            preserveScroll: true,
            replace: true,
        },
    );
};

const clearFilters = () => {
    localFilters.value = { status: '', project: '' };
    router.get(route('tasks.index'), { preserveScroll: true, replace: true });
};

const hasActiveFilters = computed(
    () => localFilters.value.status || localFilters.value.project,
);

const confirmDelete = (task) => {
    deletingTask.value = task;
};

const deleteTask = (task) => {
    router.delete(route('tasks.destroy', task.id), {
        onSuccess: () => {
            deletingTask.value = null;
        },
    });
};

const goToPage = (pageNum) => {
    router.get(
        route('tasks.index', {
            page: pageNum,
            status: localFilters.value.status,
            project: localFilters.value.project,
        }),
        {
            preserveScroll: true,
        },
    );
};

const formatPriorityLabel = (priority) => {
    const labels = {
        rendah: 'Rendah',
        sedang: 'Sedang',
        tinggi: 'Tinggi',
    };
    return labels[priority] ?? priority;
};

const formatStatusLabel = (status) => {
    const labels = {
        todo: 'Belum Dimulai',
        'in-progress': 'Sedang Dikerjakan',
        done: 'Selesai',
    };
    return labels[status] ?? status;
};
</script>

<template>
    <AppLayout>
        <template #title>Tugas</template>

        <div class="rounded-lg bg-white shadow">
            <div
                class="flex flex-col gap-4 border-b border-gray-200 p-6 sm:flex-row sm:items-center sm:justify-between"
            >
                <h2 class="text-lg font-medium text-gray-900">Daftar Tugas</h2>

                <Link
                    :href="tasks.create.url()"
                    class="w-full rounded-lg bg-blue-600 px-4 py-2 text-center text-sm font-medium text-white transition-colors hover:bg-blue-700 sm:w-auto"
                >
                    + Tugas Baru
                </Link>
            </div>

            <!-- Filters -->
            <div class="border-b border-gray-200 bg-gray-50 p-4">
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1 sm:w-48">
                        <label
                            for="filter-status"
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Status</label
                        >
                        <select
                            id="filter-status"
                            v-model="localFilters.status"
                            @change="applyFilters"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        >
                            <option value="">Semua Status</option>
                            <option value="todo">Belum Dimulai</option>
                            <option value="in-progress">
                                Sedang Dikerjakan
                            </option>
                            <option value="done">Selesai</option>
                        </select>
                    </div>
                    <div class="flex-1 sm:w-64">
                        <label
                            for="filter-project"
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Project</label
                        >
                        <select
                            id="filter-project"
                            v-model="localFilters.project"
                            @change="applyFilters"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        >
                            <option value="">Semua Project</option>
                            <option
                                v-for="project in projectsData"
                                :key="project.id"
                                :value="project.id"
                            >
                                {{ project.name }}
                            </option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Hapus Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Judul
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Project
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Prioritas
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Deadline
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Penanggung Jawab
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr
                            v-for="task in tasksData.data"
                            :key="task.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ task.title }}
                                </div>
                                <div
                                    v-if="task.description"
                                    class="max-w-xs truncate text-sm text-gray-500"
                                >
                                    {{ task.description }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ task.project?.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        getStatusColor(task.status),
                                    ]"
                                >
                                    {{ formatStatusLabel(task.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        getPriorityColor(task.priority),
                                    ]"
                                >
                                    {{ formatPriorityLabel(task.priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ formatDate(task.due_date) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div
                                    v-if="task.assignee"
                                    class="text-sm text-gray-700"
                                >
                                    {{ task.assignee.name }}
                                </div>
                                <div v-else class="text-sm text-gray-400">
                                    -
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                            >
                                <Link
                                    :href="tasks.show.url({ task: task.id })"
                                    class="mr-3 text-blue-600 hover:text-blue-900"
                                >
                                    Lihat
                                </Link>
                                <Link
                                    :href="tasks.edit.url({ task: task.id })"
                                    class="mr-3 text-gray-600 hover:text-gray-900"
                                >
                                    Edit
                                </Link>
                                <button
                                    type="button"
                                    @click="confirmDelete(task)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="tasksData.last_page > 1"
                class="border-t border-gray-200 p-4"
            >
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ tasksData.from }} sampai
                        {{ tasksData.to }} dari {{ tasksData.total }} tugas
                    </div>
                    <div class="flex gap-2">
                        <button
                            v-if="tasksData.prev_page_url"
                            @click="goToPage(tasksData.current_page - 1)"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Sebelumnya
                        </button>
                        <button
                            v-if="tasksData.next_page_url"
                            @click="goToPage(tasksData.current_page + 1)"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Selanjutnya
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Empty state -->
            <div v-if="tasksData.data.length === 0" class="p-12 text-center">
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
                    Belum ada tugas
                </h3>
                <p class="mb-4 text-sm text-gray-500">
                    Mulai dengan membuat tugas baru.
                </p>
                <Link
                    :href="tasks.create.url()"
                    class="inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                >
                    + Tugas Baru
                </Link>
            </div>
        </div>

        <!-- Delete confirmation modal -->
        <div v-if="deletingTask" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="fixed inset-0 bg-gray-900/50"
                    @click="deletingTask = null"
                />
                <div
                    class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl"
                >
                    <h3 class="mb-2 text-lg font-medium text-gray-900">
                        Hapus Tugas
                    </h3>
                    <p class="mb-4 text-gray-600">
                        Apakah Anda yakin ingin menghapus tugas
                        <strong>{{ deletingTask.title }}</strong
                        >? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="deletingTask = null"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="deleteTask(deletingTask)"
                            class="rounded-lg border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
