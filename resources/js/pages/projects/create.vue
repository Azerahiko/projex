<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import projects from '@/routes/projects';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const { clients } = page.props;

const form = useForm({
    name: '',
    description: '',
    client_id: '',
    start_date: '',
    end_date: '',
    status: 'aktif',
});

function submit() {
    form.post(projects.store.url());
}
</script>

<template>
    <AppLayout>
        <template #title>Tambah Project</template>

        <div class="max-w-2xl">
            <div class="rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">
                            Tambah Project Baru
                        </h2>
                        <Link
                            :href="projects.index.url()"
                            class="text-sm text-gray-600 hover:text-gray-900"
                        >
                            Kembali ke daftar
                        </Link>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6 p-6">
                    <div>
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700"
                            >Nama Project *</label
                        >
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            :class="{ 'border-red-500': form.errors.name }"
                            required
                        />
                        <p
                            v-if="form.errors.name"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="description"
                            class="block text-sm font-medium text-gray-700"
                            >Deskripsi</label
                        >
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            :class="{
                                'border-red-500': form.errors.description,
                            }"
                        />
                        <p
                            v-if="form.errors.description"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="client_id"
                            class="block text-sm font-medium text-gray-700"
                            >Client</label
                        >
                        <select
                            id="client_id"
                            v-model="form.client_id"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            :class="{ 'border-red-500': form.errors.client_id }"
                        >
                            <option value="">
                                -- Pilih Client (Opsional) --
                            </option>
                            <option
                                v-for="client in clients"
                                :key="client.id"
                                :value="client.id"
                            >
                                {{ client.name }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.client_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.client_id }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label
                                for="start_date"
                                class="block text-sm font-medium text-gray-700"
                                >Tanggal Mulai *</label
                            >
                            <input
                                id="start_date"
                                type="date"
                                v-model="form.start_date"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                :class="{
                                    'border-red-500': form.errors.start_date,
                                }"
                                required
                            />
                            <p
                                v-if="form.errors.start_date"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.start_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="end_date"
                                class="block text-sm font-medium text-gray-700"
                                >Tanggal Selesai *</label
                            >
                            <input
                                id="end_date"
                                type="date"
                                v-model="form.end_date"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                :class="{
                                    'border-red-500': form.errors.end_date,
                                }"
                                required
                            />
                            <p
                                v-if="form.errors.end_date"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.end_date }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label
                            for="status"
                            class="block text-sm font-medium text-gray-700"
                            >Status *</label
                        >
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            :class="{ 'border-red-500': form.errors.status }"
                            required
                        >
                            <option value="aktif">Aktif</option>
                            <option value="arsip">Arsip</option>
                        </select>
                        <p
                            v-if="form.errors.status"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.status }}
                        </p>
                    </div>

                    <div
                        class="flex justify-end gap-3 border-t border-gray-200 pt-4"
                    >
                        <Link
                            :href="projects.index.url()"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-70"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
