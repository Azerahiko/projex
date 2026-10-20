<script setup>
import { ref, watch, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import tasks from '@/routes/tasks';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const { task, projects } = page.props;

const form = useForm({
    project_id: String(task.project_id),
    title: task.title,
    description: task.description ?? '',
    status: task.status,
    priority: task.priority,
    due_date: task.due_date
        ? new Date(task.due_date).toISOString().split('T')[0]
        : '',
    assigned_to: task.assigned_to ? String(task.assigned_to) : '',
});

const projectOptions = computed(() => {
    const project = projects.find((p) => p.id === Number(form.project_id));
    return project ?? null;
});

const assigneeOptions = computed(() => {
    if (!projectOptions.value) return [];
    const members = projectOptions.value.members ?? [];
    const owner = projectOptions.value.owner ?? null;
    const options = [];
    if (owner)
        options.push({ id: owner.id, name: owner.name, email: owner.email });
    members.forEach((m) => {
        if (!options.some((o) => o.id === m.id)) {
            options.push({ id: m.id, name: m.name, email: m.email });
        }
    });
    return options;
});

watch(
    () => form.project_id,
    (newProjectId) => {
        if (
            form.assigned_to &&
            !assigneeOptions.value.some(
                (o) => o.id === Number(form.assigned_to),
            )
        ) {
            form.assigned_to = '';
        }
    },
);

function submit() {
    form.put(tasks.update.url({ task: task.id }), {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <AppLayout>
        <template #title>Edit {{ task.title }}</template>

        <div class="max-w-2xl">
            <div class="rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">
                            Edit Tugas
                        </h2>
                        <Link
                            :href="tasks.show.url({ task: task.id })"
                            class="text-sm text-gray-600 hover:text-gray-900"
                        >
                            Kembali ke detail
                        </Link>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6 p-6">
                    <div>
                        <label
                            for="project_id"
                            class="block text-sm font-medium text-gray-700"
                            >Project *</label
                        >
                        <select
                            id="project_id"
                            v-model="form.project_id"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            :class="{
                                'border-red-500': form.errors.project_id,
                            }"
                            required
                        >
                            <option
                                v-for="project in projects"
                                :key="project.id"
                                :value="String(project.id)"
                            >
                                {{ project.name }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.project_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.project_id }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="title"
                            class="block text-sm font-medium text-gray-700"
                            >Judul *</label
                        >
                        <input
                            id="title"
                            type="text"
                            v-model="form.title"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            :class="{ 'border-red-500': form.errors.title }"
                            required
                        />
                        <p
                            v-if="form.errors.title"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.title }}
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

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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
                                :class="{
                                    'border-red-500': form.errors.status,
                                }"
                                required
                            >
                                <option value="todo">Belum Dimulai</option>
                                <option value="in-progress">
                                    Sedang Dikerjakan
                                </option>
                                <option value="done">Selesai</option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="priority"
                                class="block text-sm font-medium text-gray-700"
                                >Prioritas *</label
                            >
                            <select
                                id="priority"
                                v-model="form.priority"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                :class="{
                                    'border-red-500': form.errors.priority,
                                }"
                                required
                            >
                                <option value="rendah">Rendah</option>
                                <option value="sedang">Sedang</option>
                                <option value="tinggi">Tinggi</option>
                            </select>
                            <p
                                v-if="form.errors.priority"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.priority }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label
                                for="due_date"
                                class="block text-sm font-medium text-gray-700"
                                >Deadline</label
                            >
                            <input
                                id="due_date"
                                type="date"
                                v-model="form.due_date"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                :class="{
                                    'border-red-500': form.errors.due_date,
                                }"
                            />
                            <p
                                v-if="form.errors.due_date"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.due_date }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="assigned_to"
                                class="block text-sm font-medium text-gray-700"
                                >Penanggung Jawab</label
                            >
                            <select
                                id="assigned_to"
                                v-model="form.assigned_to"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                :class="{
                                    'border-red-500': form.errors.assigned_to,
                                }"
                            >
                                <option value="">
                                    -- Pilih Anggota (Opsional) --
                                </option>
                                <option
                                    v-for="member in assigneeOptions"
                                    :key="member.id"
                                    :value="String(member.id)"
                                >
                                    {{ member.name }} ({{ member.email }})
                                </option>
                            </select>
                            <p
                                v-if="form.errors.assigned_to"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.assigned_to }}
                            </p>
                            <p
                                v-if="!projectOptions && form.project_id"
                                class="mt-1 text-sm text-gray-500"
                            >
                                Pilih project terlebih dahulu untuk melihat
                                daftar anggota.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex justify-end gap-3 border-t border-gray-200 pt-4"
                    >
                        <Link
                            :href="tasks.show.url({ task: task.id })"
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
