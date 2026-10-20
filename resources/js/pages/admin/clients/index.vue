<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import adminClients from '@/routes/admin/clients';

const page = usePage();
const { clients } = page.props;

const showCreateForm = ref(false);
const createForm = ref({
    name: '',
    email: '',
    phone: '',
    company: '',
    address: '',
    notes: '',
});

function closeCreateForm() {
    showCreateForm.value = false;
    createForm.value = {
        name: '',
        email: '',
        phone: '',
        company: '',
        address: '',
        notes: '',
    };
}

function submitCreate() {
    router.post(adminClients.store.url(), createForm.value, {
        onSuccess: () => closeCreateForm(),
    });
}

function confirmDelete(client) {
    if (confirm(`Apakah Anda yakin ingin menghapus klien "${client.name}"?`)) {
        router.delete(adminClients.destroy.url({ client: client.id }));
    }
}

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
        <template #title>Kelola Klien</template>

        <div class="rounded-lg bg-white shadow">
            <div class="flex items-center justify-between border-b border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900">Daftar Klien</h2>
                <button
                    type="button"
                    @click="showCreateForm = true"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                >
                    Tambah Klien
                </button>
            </div>

            <!-- Create Form Modal -->
            <div v-if="showCreateForm" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-gray-900/50" @click="closeCreateForm" />
                    <div class="relative w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Tambah Klien Baru</h3>
                            <button type="button" @click="closeCreateForm" class="text-gray-500 hover:text-gray-700">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="submitCreate">
                            <div class="space-y-4">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                                        <input
                                            id="name"
                                            type="text"
                                            v-model="createForm.name"
                                            required
                                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                        <p v-if="page.props.errors.name" class="mt-1 text-sm text-red-600">
                                            {{ page.props.errors.name }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                                        <input
                                            id="email"
                                            type="email"
                                            v-model="createForm.email"
                                            required
                                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                        <p v-if="page.props.errors.email" class="mt-1 text-sm text-red-600">
                                            {{ page.props.errors.email }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700">Telepon</label>
                                        <input
                                            id="phone"
                                            type="text"
                                            v-model="createForm.phone"
                                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                    </div>

                                    <div>
                                        <label for="company" class="block text-sm font-medium text-gray-700">Perusahaan</label>
                                        <input
                                            id="company"
                                            type="text"
                                            v-model="createForm.company"
                                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                                    <textarea
                                        id="address"
                                        v-model="createForm.address"
                                        rows="3"
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                                    <textarea
                                        id="notes"
                                        v-model="createForm.notes"
                                        rows="3"
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    />
                                </div>

                                <div class="flex justify-end gap-3 pt-4">
                                    <button
                                        type="button"
                                        @click="closeCreateForm"
                                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="page.props.creating"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-70"
                                    >
                                        Simpan Klien
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Perusahaan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Telepon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Dibuat</th>
                            <th class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="client in clients.data" :key="client.id" class="hover:bg-gray-50">
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
                                <div class="text-sm text-gray-700">{{ client.projects_count ?? 0 }} project</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ formatDate(client.created_at) }}</div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                <button
                                    type="button"
                                    @click="confirmDelete(client)"
                                    :disabled="(client.projects_count ?? 0) > 0"
                                    class="text-red-600 hover:text-red-900 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="clients.last_page > 1" class="border-t border-gray-200 p-4">
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ clients.from }} sampai {{ clients.to }} dari {{ clients.total }} klien
                    </div>
                    <div class="flex gap-2">
                        <button
                            v-if="clients.prev_page_url"
                            @click="router.get(adminClients.index.url({ page: clients.current_page - 1 }), { preserveScroll: true })"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Sebelumnya
                        </button>
                        <button
                            v-if="clients.next_page_url"
                            @click="router.get(adminClients.index.url({ page: clients.current_page + 1 }), { preserveScroll: true })"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Selanjutnya
                        </button>
                    </div>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>