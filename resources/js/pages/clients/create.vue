<script setup>
import { useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import clients from '@/routes/clients';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    address: '',
    notes: '',
});

function submit() {
    form.post(clients.store.url());
}

function goBack() {
    router.visit(clients.index.url());
}
</script>

<template>
    <AppLayout>
        <template #title>Tambah Client</template>

        <div class="max-w-2xl">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">Tambah Client Baru</h2>
                        <Link
                            :href="clients.index.url()"
                            class="text-sm text-gray-600 hover:text-gray-900"
                        >
                            Kembali ke daftar
                        </Link>
                    </div>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama *</label>
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                            :class="{ 'border-red-500': form.errors.name }"
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                            :class="{ 'border-red-500': form.errors.email }"
                            required
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Telepon</label>
                        <input
                            id="phone"
                            type="text"
                            v-model="form.phone"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                            :class="{ 'border-red-500': form.errors.phone }"
                        />
                        <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                    </div>

                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-700">Perusahaan</label>
                        <input
                            id="company"
                            type="text"
                            v-model="form.company"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                            :class="{ 'border-red-500': form.errors.company }"
                        />
                        <p v-if="form.errors.company" class="mt-1 text-sm text-red-600">{{ form.errors.company }}</p>
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea
                            id="address"
                            v-model="form.address"
                            rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                            :class="{ 'border-red-500': form.errors.address }"
                        />
                        <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                            :class="{ 'border-red-500': form.errors.notes }"
                        />
                        <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">{{ form.errors.notes }}</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link
                            :href="clients.index.url()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-70"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>