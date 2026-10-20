<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import clients from '@/routes/clients';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const { client } = page.props;

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
</script>

<template>
    <AppLayout>
        <template #title>{{ client.name }}</template>

        <div class="max-w-3xl">
            <div class="rounded-lg bg-white shadow">
                <div
                    class="flex items-center justify-between border-b border-gray-200 p-6"
                >
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ client.name }}
                        </h2>
                        <p class="text-sm text-gray-500">{{ client.email }}</p>
                    </div>
                    <div class="flex gap-3">
                        <Link
                            :href="clients.edit.url({ client: client.id })"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Edit
                        </Link>
                        <Link
                            :href="clients.index.url()"
                            class="rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        >
                            Kembali
                        </Link>
                    </div>
                </div>

                <div class="space-y-6 p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Perusahaan</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ client.company ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Telepon</label
                            >
                            <p class="mt-1 text-gray-900">
                                {{ client.phone ?? '-' }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Alamat</label
                            >
                            <p class="mt-1 whitespace-pre-wrap text-gray-900">
                                {{ client.address ?? '-' }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Catatan</label
                            >
                            <p class="mt-1 whitespace-pre-wrap text-gray-900">
                                {{ client.notes ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                Project ({{ client.projects_count ?? 0 }})
                            </h3>
                        </div>
                        <p class="text-sm text-gray-500">
                            Daftar project untuk client ini akan ditampilkan di
                            sini setelah fitur project management
                            diimplementasikan.
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
                    Hapus Client
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
                            Hapus Client
                        </h3>
                        <p class="mb-4 text-gray-600">
                            Apakah Anda yakin ingin menghapus client
                            <strong>{{ client.name }}</strong
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
                                @click="deleteClient"
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
