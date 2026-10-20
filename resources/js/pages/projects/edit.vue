<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import projects from '@/routes/projects';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const { project } = page.props;

const form = useForm({
    name: project.name,
    description: project.description ?? '',
    client_name: project.client?.name ?? '',
    client_email: project.client?.email ?? '',
    client_phone: project.client?.phone ?? '',
    client_company: project.client?.company ?? '',
    start_date: project.start_date
        ? new Date(project.start_date).toISOString().split('T')[0]
        : '',
    end_date: project.end_date
        ? new Date(project.end_date).toISOString().split('T')[0]
        : '',
    status: project.status,
});

function submit() {
    form.put(projects.update.url({ project: project.id }), {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <AppLayout>
        <template #title>Edit {{ project.name }}</template>

        <div class="max-w-2xl">
            <div class="rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">
                            Edit Project
                        </h2>
                        <Link
                            :href="projects.show.url({ project: project.id })"
                            class="text-sm text-gray-600 hover:text-gray-900"
                        >
                            Kembali ke detail
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

                    <!-- Client Information Section -->
                    <div class="space-y-4 border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-900">Informasi Client (Opsional)</h3>
                        <p class="text-xs text-gray-500">
                            Isi untuk menautkan project ke client. Client dengan email sama akan otomatis disatukan.
                        </p>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    for="client_name"
                                    class="block text-sm font-medium text-gray-700"
                                    >Nama Client *</label
                                >
                                <input
                                    id="client_name"
                                    type="text"
                                    v-model="form.client_name"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :class="{ 'border-red-500': form.errors.client_name }"
                                />
                                <p
                                    v-if="form.errors.client_name"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.client_name }}
                                </p>
                            </div>

                            <div>
                                <label
                                    for="client_email"
                                    class="block text-sm font-medium text-gray-700"
                                    >Email Client *</label
                                >
                                <input
                                    id="client_email"
                                    type="email"
                                    v-model="form.client_email"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :class="{ 'border-red-500': form.errors.client_email }"
                                />
                                <p
                                    v-if="form.errors.client_email"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.client_email }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    for="client_phone"
                                    class="block text-sm font-medium text-gray-700"
                                    >Nomor Telepon</label
                                >
                                <input
                                    id="client_phone"
                                    type="tel"
                                    v-model="form.client_phone"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :class="{ 'border-red-500': form.errors.client_phone }"
                                />
                                <p
                                    v-if="form.errors.client_phone"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.client_phone }}
                                </p>
                            </div>

                            <div>
                                <label
                                    for="client_company"
                                    class="block text-sm font-medium text-gray-700"
                                    >Nama Perusahaan</label
                                >
                                <input
                                    id="client_company"
                                    type="text"
                                    v-model="form.client_company"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :class="{ 'border-red-500': form.errors.client_company }"
                                />
                                <p
                                    v-if="form.errors.client_company"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.client_company }}
                                </p>
                            </div>
                        </div>
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
                            :href="projects.show.url({ project: project.id })"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-70"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>