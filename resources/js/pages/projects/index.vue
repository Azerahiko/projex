<script setup>
import { ref } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import projects from '@/routes/projects';

const page = usePage();
const { projects: projectsData } = page.props;

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

const goToPage = (pageNum) => {
    router.get(route('projects.index', { page: pageNum }), {
        preserveScroll: true,
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
</script>

<template>
    <AppLayout>
        <template #title>Projects</template>

        <div class="rounded-lg bg-white shadow">
            <div
                class="flex items-center justify-between border-b border-gray-200 p-6"
            >
                <h2 class="text-lg font-medium text-gray-900">
                    Daftar Project
                </h2>
                <Link
                    :href="projects.create.url()"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                >
                    + Project Baru
                </Link>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Nama
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Client
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Periode
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Tugas
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
                            v-for="project in projectsData.data"
                            :key="project.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ project.name }}
                                </div>
                                <div
                                    v-if="project.description"
                                    class="max-w-xs truncate text-sm text-gray-500"
                                >
                                    {{ project.description }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ project.client?.name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ formatDate(project.start_date) }} -
                                    {{ formatDate(project.end_date) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        project.status === 'aktif'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800',
                                    ]"
                                >
                                    {{ project.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800"
                                >
                                    {{ project.tasks_count ?? 0 }} tugas
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                            >
                                <Link
                                    :href="
                                        projects.show.url({
                                            project: project.id,
                                        })
                                    "
                                    class="mr-3 text-blue-600 hover:text-blue-900"
                                >
                                    Lihat
                                </Link>
                                <Link
                                    :href="
                                        projects.edit.url({
                                            project: project.id,
                                        })
                                    "
                                    class="mr-3 text-gray-600 hover:text-gray-900"
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
            <div
                v-if="projectsData.last_page > 1"
                class="border-t border-gray-200 p-4"
            >
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ projectsData.from }} sampai
                        {{ projectsData.to }} dari
                        {{ projectsData.total }} project
                    </div>
                    <div class="flex gap-2">
                        <button
                            v-if="projectsData.prev_page_url"
                            @click="goToPage(projectsData.current_page - 1)"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Sebelumnya
                        </button>
                        <button
                            v-if="projectsData.next_page_url"
                            @click="goToPage(projectsData.current_page + 1)"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Selanjutnya
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Empty state -->
            <div v-if="projectsData.data.length === 0" class="p-12 text-center">
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
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                        />
                    </svg>
                </div>
                <h3 class="mb-1 text-lg font-medium text-gray-900">
                    Belum ada project
                </h3>
                <p class="mb-4 text-sm text-gray-500">
                    Mulai dengan membuat project baru.
                </p>
                <Link
                    :href="projects.create.url()"
                    class="inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                >
                    + Project Baru
                </Link>
            </div>
        </div>

        <!-- Delete confirmation modal -->
        <div v-if="deletingProject" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="fixed inset-0 bg-gray-900/50"
                    @click="deletingProject = null"
                />
                <div
                    class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl"
                >
                    <h3 class="mb-2 text-lg font-medium text-gray-900">
                        Hapus Project
                    </h3>
                    <p class="mb-4 text-gray-600">
                        Apakah Anda yakin ingin menghapus project
                        <strong>{{ deletingProject.name }}</strong
                        >? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="deletingProject = null"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="deleteProject(deletingProject)"
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
