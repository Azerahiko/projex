<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import clients from '@/routes/clients';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const { client } = page.props;
</script>

<template>
    <AppLayout>
        <template #title>{{ client.name }}</template>

        <div class="max-w-3xl">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">{{ client.name }}</h2>
                        <p class="text-sm text-gray-500">{{ client.email }}</p>
                    </div>
                    <div class="flex gap-3">
                        <Link
                            :href="clients.edit.url({ client: client.id })"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Edit
                        </Link>
                        <Link
                            :href="clients.index.url()"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700"
                        >
                            Kembali
                        </Link>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Perusahaan</label>
                            <p class="mt-1 text-gray-900">{{ client.company ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Telepon</label>
                            <p class="mt-1 text-gray-900">{{ client.phone ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Alamat</label>
                            <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ client.address ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Catatan</label>
                            <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ client.notes ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Project ({{ client.projects_count ?? 0 }})</h3>
                        </div>
                        <p class="text-sm text-gray-500">Daftar project untuk client ini akan ditampilkan di sini setelah fitur project management diimplementasikan.</p>
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
                    Hapus Client
                </button>
            </div>

            <!-- Delete confirmation modal -->
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-gray-900/50" @click="showDeleteModal = false" />
                    <div class="relative w-full max-w-md bg-white rounded-lg shadow-xl p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus Client</h3>
                        <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menghapus client <strong>{{ client.name }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
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
                                @click="deleteClient"
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

        const deleteClient = () => {
            router.delete(route('clients.destroy', client.id), {
                onSuccess: () => {
                    showDeleteModal.value = false;
                },
            });
        };

        return {
            showDeleteModal,
            confirmDelete,
            deleteClient,
        };
    },
};
</script>