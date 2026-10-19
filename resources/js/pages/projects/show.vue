<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import projects from '@/routes/projects';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const { project } = page.props;

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
</script>

<template>
    <AppLayout>
        <template #title>{{ project.name }}</template>

        <div class="max-w-4xl">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">{{ project.name }}</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ project.description ?? 'Tidak ada deskripsi' }}</p>
                    </div>
                    <div class="flex gap-3 flex-wrap">
                        <Link
                            :href="projects.edit.url({ project: project.id })"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Edit
                        </Link>
                        <Link
                            :href="projects.index.url()"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700"
                        >
                            Kembali
                        </Link>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Project Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Client</label>
                            <p class="mt-1 text-gray-900">{{ project.client?.name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Pemilik</label>
                            <p class="mt-1 text-gray-900">{{ project.owner?.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="projectStatusColors[project.status] ?? 'bg-gray-100 text-gray-800'"
                            >
                                {{ project.status }}
                            </span>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Periode</label>
                            <p class="mt-1 text-gray-900">
                                {{ formatDate(project.start_date) }} - {{ formatDate(project.end_date) }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Dibuat</label>
                            <p class="mt-1 text-gray-900">{{ formatDateTime(project.created_at) }}</p>
                        </div>
                    </div>

                    <!-- Members -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Anggota Project ({{ project.members?.length ?? 0 }})</h3>
                        </div>
                        <div v-if="project.members && project.members.length > 0" class="space-y-2">
                            <div
                                v-for="member in project.members"
                                :key="member.id"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-blue-700">{{ member.name.charAt(0) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ member.name }}</p>
                                        <p class="text-xs text-gray-500">{{ member.email }}</p>
                                    </div>
                                </div>
                                <span
                                    v-if="member.id === project.user_id"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                >
                                    Pemilik
                                </span>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-500">Belum ada anggota. Pemilik project adalah satu-satunya anggota.</p>
                    </div>

                    <!-- Tasks -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Tugas ({{ project.tasks?.length ?? 0 }})</h3>
                            <Link
                                :href="tasks.create.url({ project: project.id })"
                                class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700"
                            >
                                + Tugas Baru
                            </Link>
                        </div>
                        <div v-if="project.tasks && project.tasks.length > 0" class="space-y-2">
                            <div
                                v-for="task in project.tasks"
                                :key="task.id"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                            >
                                <div class="flex items-center gap-3">
                                    <Link
                                        :href="tasks.show.url({ task: task.id })"
                                        class="text-sm font-medium text-gray-900 hover:text-blue-600"
                                    >
                                        {{ task.title }}
                                    </Link>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                        :class="statusColors[task.status] ?? 'bg-gray-100 text-gray-800'"
                                    >
                                        {{ task.status }}
                                    </span>
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                        :class="priorityColors[task.priority] ?? 'bg-gray-100 text-gray-800'"
                                    >
                                        {{ task.priority }}
                                    </span>
                                    <span v-if="task.assignee" class="text-xs text-gray-500">{{ task.assignee.name }}</span>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-500">Belum ada tugas untuk project ini.</p>
                    </div>
                </div>
            </div>

            <!-- Delete button at bottom -->
            <div class="mt-6 text-right">
                <button
                    type="button"
                    @click="confirmDelete"
                    class="px-4 py-2 text-sm font-medium text-red-600 border border-red-300 rounded-lg hover:bg-red-50"
                >
                    Hapus Project
                </button>
            </div>

            <!-- Delete confirmation modal -->
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-gray-900/50" @click="showDeleteModal = false" />
                    <div class="relative w-full max-w-md bg-white rounded-lg shadow-xl p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus Project</h3>
                        <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menghapus project <strong>{{ project.name }}</strong>? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua tugas di dalamnya.</p>
                        <div class="flex justify-end gap-3">
                            <button
                                type="button"
                                @click="showDeleteModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                @click="deleteProject"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700"
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
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        };

        const formatDateTime = (dateString) => {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' +
                date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        };

        return {
            showDeleteModal,
            confirmDelete,
            deleteProject,
            formatDate,
            formatDateTime,
        };
    },
};
</script>