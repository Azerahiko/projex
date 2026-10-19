<script setup>
import { usePage, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import projects from '@/routes/projects';

const page = usePage();
const { projects: projectsData } = page.props;
</script>

<template>
    <AppLayout>
        <template #title>Projects</template>

        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Daftar Project</h2>
                <Link
                    :href="projects.create.url()"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                >
                    + Project Baru
                </Link>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tugas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="project in projectsData.data" :key="project.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ project.name }}</div>
                                <div v-if="project.description" class="text-sm text-gray-500 truncate max-w-xs">{{ project.description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ project.client?.name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ formatDate(project.start_date) }} - {{ formatDate(project.end_date) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        project.status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ project.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ project.tasks_count ?? 0 }} tugas
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link
                                    :href="projects.show.url({ project: project.id })"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    Lihat
                                </Link>
                                <Link
                                    :href="projects.edit.url({ project: project.id })"
                                    class="text-gray-600 hover:text-gray-900 mr-3"
                                >
                                    Edit
                                </Link>
                                <button
                                    type="button"
                                    @click="confirmDelete(project)"
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
            <div v-if="projectsData.last_page > 1" class="p-4 border-t border-gray-200">
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ projectsData.from }} sampai {{ projectsData.to }} dari {{ projectsData.total }} project
                    </div>
                    <div class="flex gap-2">
                        <button
                            v-if="projectsData.prev_page_url"
                            @click="goToPage(projectsData.current_page - 1)"
                            class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Sebelumnya
                        </button>
                        <button
                            v-if="projectsData.next_page_url"
                            @click="goToPage(projectsData.current_page + 1)"
                            class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Selanjutnya
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Empty state -->
            <div v-if="projectsData.data.length === 0" class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada project</h3>
                <p class="text-gray-500 text-sm mb-4">Mulai dengan membuat project baru.</p>
                <Link
                    :href="projects.create.url()"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors inline-block"
                >
                    + Project Baru
                </Link>
            </div>
        </div>

        <!-- Delete confirmation modal -->
        <div v-if="deletingProject" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50" @click="deletingProject = null" />
                <div class="relative w-full max-w-md bg-white rounded-lg shadow-xl p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus Project</h3>
                    <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menghapus project <strong>{{ deletingProject.name }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="deletingProject = null"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="deleteProject(deletingProject)"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700"
                        >
                            Hapus
                        </button>
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
        const deletingProject = ref(null);

        const confirmDelete = (project) => {
            deletingProject.value = project;
        };

        const deleteProject = (project) => {
            router.delete(route('projects.destroy', project.id), {
                onSuccess: () => {
                    deletingProject.value = null;
                },
            });
        };

        const goToPage = (page) => {
            router.get(route('projects.index', { page }), { preserveScroll: true });
        };

        const formatDate = (dateString) => {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        };

        return {
            deletingProject,
            confirmDelete,
            deleteProject,
            goToPage,
            formatDate,
        };
    },
};
</script>