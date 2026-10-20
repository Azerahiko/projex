<script setup>
import { ref } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import clients from '@/routes/clients';

const page = usePage();
const { clients: clientsData } = page.props;

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

const goToPage = (pageNum) => {
    router.get(route('clients.index', { page: pageNum }), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <template #title>Clients</template>

        <div class="rounded-lg bg-white shadow">
            <div
                class="flex items-center justify-between border-b border-gray-200 p-6"
            >
                <h2 class="text-lg font-medium text-gray-900">Daftar Client</h2>
                <Link
                    :href="clients.create.url()"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                >
                    + Client Baru
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
                                Email
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Perusahaan
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Telepon
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Project
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
                            v-for="client in clientsData.data"
                            :key="client.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ client.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ client.email }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ client.company ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ client.phone ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800"
                                >
                                    {{ client.projects_count ?? 0 }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                            >
                                <Link
                                    :href="
                                        clients.show.url({ client: client.id })
                                    "
                                    class="mr-3 text-blue-600 hover:text-blue-900"
                                >
                                    Lihat
                                </Link>
                                <Link
                                    :href="
                                        clients.edit.url({ client: client.id })
                                    "
                                    class="mr-3 text-gray-600 hover:text-gray-900"
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
            <div
                v-if="clientsData.last_page > 1"
                class="border-t border-gray-200 p-4"
            >
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ clientsData.from }} sampai
                        {{ clientsData.to }} dari {{ clientsData.total }} client
                    </div>
                    <div class="flex gap-2">
                        <button
                            v-if="clientsData.prev_page_url"
                            @click="goToPage(clientsData.current_page - 1)"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Sebelumnya
                        </button>
                        <button
                            v-if="clientsData.next_page_url"
                            @click="goToPage(clientsData.current_page + 1)"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Selanjutnya
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Empty state -->
            <div v-if="clientsData.data.length === 0" class="p-12 text-center">
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
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                </div>
                <h3 class="mb-1 text-lg font-medium text-gray-900">
                    Belum ada client
                </h3>
                <p class="mb-4 text-sm text-gray-500">
                    Mulai dengan menambahkan client baru.
                </p>
                <Link
                    :href="clients.create.url()"
                    class="inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                >
                    + Client Baru
                </Link>
            </div>
        </div>

        <!-- Delete confirmation modal -->
        <div v-if="deletingClient" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="fixed inset-0 bg-gray-900/50"
                    @click="deletingClient = null"
                />
                <div
                    class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl"
                >
                    <h3 class="mb-2 text-lg font-medium text-gray-900">
                        Hapus Client
                    </h3>
                    <p class="mb-4 text-gray-600">
                        Apakah Anda yakin ingin menghapus client
                        <strong>{{ deletingClient.name }}</strong
                        >? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="deletingClient = null"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="deleteClient(deletingClient)"
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
