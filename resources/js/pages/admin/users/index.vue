<script setup>
import { ref, computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import adminUsers from '@/routes/admin/users';

const page = usePage();
const { users, roles } = page.props;

const roleLabels = {
    admin: 'Administrator',
    project_manager: 'Project Manager',
    employee: 'Karyawan',
    client: 'Klien',
};

const roleColors = {
    admin: 'bg-blue-100 text-blue-800',
    project_manager: 'bg-purple-100 text-purple-800',
    employee: 'bg-green-100 text-green-800',
    client: 'bg-orange-100 text-orange-800',
};

const updatingUser = ref(null);
const selectedRole = ref('');

function startRoleUpdate(user) {
    updatingUser.value = user.id;
    selectedRole.value = user.role;
}

function cancelRoleUpdate() {
    updatingUser.value = null;
    selectedRole.value = '';
}

function updateRole(user) {
    router.put(adminUsers.role.update.url({ user: user.id }), {
        role: selectedRole.value,
        onSuccess: () => {
            updatingUser.value = null;
            selectedRole.value = '';
        },
    });
}

function confirmDelete(user) {
    if (confirm(`Apakah Anda yakin ingin menghapus pengguna "${user.name}"?`)) {
        router.delete(adminUsers.destroy.url({ user: user.id }));
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
        <template #title>Kelola Pengguna</template>

        <div class="rounded-lg bg-white shadow">
            <div class="border-b border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900">Daftar Pengguna</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Peran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Klien</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Dibuat</th>
                            <th class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ user.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="updatingUser !== user.id">
                                    <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', roleColors[user.role]]">
                                        {{ roleLabels[user.role] }}
                                    </span>
                                </div>
                                <div v-else class="flex items-center gap-2">
                                    <select
                                        v-model="selectedRole"
                                        class="block w-40 rounded-lg border border-gray-300 px-2 py-1 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    >
                                        <option v-for="role in roles" :key="role.value" :value="role.value">
                                            {{ roleLabels[role.value] }}
                                        </option>
                                    </select>
                                    <button
                                        type="button"
                                        @click="updateRole(user)"
                                        class="rounded-lg bg-blue-600 px-3 py-1 text-xs font-medium text-white hover:bg-blue-700"
                                    >
                                        Simpan
                                    </button>
                                    <button
                                        type="button"
                                        @click="cancelRoleUpdate"
                                        class="rounded-lg border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        Batal
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ user.client?.name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ formatDate(user.created_at) }}</div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                <div v-if="updatingUser === user.id">
                                    <button
                                        type="button"
                                        @click="cancelRoleUpdate"
                                        class="mr-3 text-gray-600 hover:text-gray-900"
                                    >
                                        Batal
                                    </button>
                                </div>
                                <div v-else class="flex items-center justify-end gap-3">
                                    <button
                                        type="button"
                                        @click="startRoleUpdate(user)"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        Ubah Peran
                                    </button>
                                    <button
                                        type="button"
                                        @click="confirmDelete(user)"
                                        :disabled="user.id === page.props.auth.user.id"
                                        class="text-red-600 hover:text-red-900 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.last_page > 1" class="border-t border-gray-200 p-4">
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ users.from }} sampai {{ users.to }} dari {{ users.total }} pengguna
                    </div>
                    <div class="flex gap-2">
                        <button
                            v-if="users.prev_page_url"
                            @click="router.get(adminUsers.index.url({ page: users.current_page - 1 }), { preserveScroll: true })"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Sebelumnya
                        </button>
                        <button
                            v-if="users.next_page_url"
                            @click="router.get(adminUsers.index.url({ page: users.current_page + 1 }), { preserveScroll: true })"
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