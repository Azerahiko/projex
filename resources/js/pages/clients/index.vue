<script setup>
import { usePage, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import clients from '@/routes/clients';
const page = usePage();
const { clients: clientsData } = page.props;
</script>

<template>
    <AppLayout>
        <template #title>Clients</template>

        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Daftar Client</h2>
                <Link
                    :href="clients.create.url()"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                >
                    + Client Baru
                </Link>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perusahaan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="client in clientsData.data" :key="client.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ client.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ client.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ client.company ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ client.phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ client.projects_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link
                                    :href="clients.show.url({ client: client.id })"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    Lihat
                                </Link>
                                <Link
                                    :href="clients.edit.url({ client: client.id })"
                                    class="text-gray-600 hover:text-gray-900 mr-3"
                                >
                                    Edit
                                </Link>
                                <button
                                    type="button"
                                    @click="confirmDelete(client)"
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
            <div v-if="clientsData.last_page > 1" class="p-4 border-t border-gray-200">
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ clientsData.from }} sampai {{ clientsData.to }} dari {{ clientsData.total }} client
                    </div>
                    <div class="flex gap-2">
                        <button
                            v-if="clientsData.prev_page_url"
                            @click="goToPage(clientsData.current_page - 1)"
                            class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Sebelumnya
                        </button>
                        <button
                            v-if="clientsData.next_page_url"
                            @click="goToPage(clientsData.current_page + 1)"
                            class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Selanjutnya
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Empty state -->
            <div v-if="clientsData.data.length === 0" class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada client</h3>
                <p class="text-gray-500 text-sm mb-4">Mulai dengan menambahkan client baru.</p>
                <Link
                    :href="clients.create.url()"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors inline-block"
                >
                    + Client Baru
                </Link>
            </div>
        </div>

        <!-- Delete confirmation modal -->
        <div v-if="deletingClient" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50" @click="deletingClient = null" />
                <div class="relative w-full max-w-md bg-white rounded-lg shadow-xl p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus Client</h3>
                    <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menghapus client <strong>{{ deletingClient.name }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="deletingClient = null"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="deleteClient(deletingClient)"
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
        const deletingClient = ref(null);

        const confirmDelete = (client) => {
            deletingClient.value = client;
        };

        const deleteClient = (client) => {
            router.delete(route('clients.destroy', client.id), {
                onSuccess: () => {
                    deletingClient.value = null;
                },
            });
        };

        const goToPage = (page) => {
            router.get(route('clients.index', { page }), { preserveScroll: true });
        };

        return {
            deletingClient,
            confirmDelete,
            deleteClient,
            goToPage,
        };
    },
};
</script>