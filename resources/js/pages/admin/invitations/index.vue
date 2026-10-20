<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import adminInvitations from '@/routes/admin/invitations';
import adminUsers from '@/routes/admin/users';

const page = usePage();
const { invitations, roles } = page.props;

const roleLabels = {
    admin: 'Administrator',
    project_manager: 'Project Manager',
    employee: 'Karyawan',
    client: 'Klien',
};

const statusLabels = {
    pending: 'Menunggu',
    accepted: 'Diterima',
    expired: 'Kedaluwarsa',
    revoked: 'Dibatalkan',
};

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    accepted: 'bg-green-100 text-green-800',
    expired: 'bg-gray-100 text-gray-800',
    revoked: 'bg-red-100 text-red-800',
};

const showInviteForm = ref(false);
const inviteForm = ref({
    email: '',
    role: 'employee',
});

function closeInviteForm() {
    showInviteForm.value = false;
    inviteForm.value = { email: '', role: 'employee' };
}

function submitInvite() {
    router.post(adminInvitations.store.url(), inviteForm.value, {
        onSuccess: () => closeInviteForm(),
    });
}

function resendInvitation(invitation) {
    if (confirm(`Kirim ulang undangan ke ${invitation.email}?`)) {
        router.post(adminInvitations.resend.url({ invitation: invitation.id }));
    }
}

function revokeInvitation(invitation) {
    if (confirm(`Batalkan undangan untuk ${invitation.email}?`)) {
        router.delete(adminInvitations.destroy.url({ invitation: invitation.id }));
    }
}

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <AppLayout>
        <template #title>Kelola Undangan</template>

        <div class="rounded-lg bg-white shadow">
            <div class="flex items-center justify-between border-b border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900">Daftar Undangan</h2>
                <button
                    type="button"
                    @click="showInviteForm = true"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                >
                    Undang Pengguna
                </button>
            </div>

            <!-- Invite Form Modal -->
            <div v-if="showInviteForm" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-gray-900/50" @click="closeInviteForm" />
                    <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Undang Pengguna Baru</h3>
                            <button type="button" @click="closeInviteForm" class="text-gray-500 hover:text-gray-700">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="submitInvite">
                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input
                                        id="email"
                                        type="email"
                                        v-model="inviteForm.email"
                                        required
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    />
                                    <p v-if="page.props.errors.email" class="mt-1 text-sm text-red-600">
                                        {{ page.props.errors.email }}
                                    </p>
                                </div>

                                <div>
                                    <label for="role" class="block text-sm font-medium text-gray-700">Peran</label>
                                    <select
                                        id="role"
                                        v-model="inviteForm.role"
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    >
                                        <option v-for="role in roles" :key="role.value" :value="role.value">
                                            {{ roleLabels[role.value] }}
                                        </option>
                                    </select>
                                </div>

                                <div class="flex justify-end gap-3 pt-4">
                                    <button
                                        type="button"
                                        @click="closeInviteForm"
                                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="page.props.inviting"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-70"
                                    >
                                        Kirim Undangan
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
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Peran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Dikirim Oleh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Kedaluwarsa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Diterima</th>
                            <th class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="invitation in invitations.data" :key="invitation.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ invitation.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', roleColors[invitation.role]]">
                                    {{ roleLabels[invitation.role] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', statusColors[invitation.status]]">
                                    {{ statusLabels[invitation.status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ invitation.inviter?.name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ formatDate(invitation.expires_at) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ invitation.accepted_at ? formatDate(invitation.accepted_at) : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3">
                                    <button
                                        v-if="invitation.status === 'pending' && !invitation.is_expired"
                                        type="button"
                                        @click="resendInvitation(invitation)"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        Kirim Ulang
                                    </button>
                                    <button
                                        v-if="invitation.status === 'pending'"
                                        type="button"
                                        @click="revokeInvitation(invitation)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Batalkan
                                    </button>
                                    <span v-else-if="invitation.status === 'accepted'" class="text-green-600">Selesai</span>
                                    <span v-else class="text-gray-500">{{ statusLabels[invitation.status] }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="invitations.last_page > 1" class="border-t border-gray-200 p-4">
                <nav class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ invitations.from }} sampai {{ invitations.to }} dari {{ invitations.total }} undangan
                    </div>
                    <div class="flex gap-2">
                        <button
                            v-if="invitations.prev_page_url"
                            @click="router.get(adminInvitations.index.url({ page: invitations.current_page - 1 }), { preserveScroll: true })"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Sebelumnya
                        </button>
                        <button
                            v-if="invitations.next_page_url"
                            @click="router.get(adminInvitations.index.url({ page: invitations.current_page + 1 }), { preserveScroll: true })"
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