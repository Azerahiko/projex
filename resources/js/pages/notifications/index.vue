<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import notifications from '@/routes/notifications';

const page = usePage();
const { notifications: notificationsData, unreadCount: initialUnreadCount } =
    page.props;

const unreadCount = ref(initialUnreadCount);
const showDeleteModal = ref(null);

const hasUnread = computed(() => unreadCount.value > 0);

function goToPage(pageNum) {
    router.get(notifications.index.url({ page: pageNum }), {
        preserveScroll: true,
    });
}

function markAsRead(notification) {
    if (!notification.read_at) {
        router.post(notifications.read.url(notification.id), {
            onSuccess: () => {
                notification.read_at = new Date().toISOString();
                unreadCount.value = Math.max(0, unreadCount.value - 1);
            },
        });
    }
}

function markAllAsRead() {
    if (unreadCount.value === 0) return;
    router.post(notifications.readAll.url(), {
        onSuccess: () => {
            notificationsData.data.forEach((n) => {
                n.read_at = new Date().toISOString();
            });
            unreadCount.value = 0;
        },
    });
}

function confirmDelete(notification) {
    showDeleteModal.value = notification;
}

function deleteNotification(notification) {
    router.delete(notifications.destroy.url(notification.id), {
        onSuccess: () => {
            showDeleteModal.value = null;
            if (!notification.read_at) {
                unreadCount.value = Math.max(0, unreadCount.value - 1);
            }
        },
    });
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return (
        date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }) +
        ' ' +
        date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
    );
}

function formatRelativeDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Baru saja';
    if (diffMins < 60) return `${diffMins} menit lalu`;
    if (diffHours < 24) return `${diffHours} jam lalu`;
    if (diffDays < 7) return `${diffDays} hari lalu`;
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}
</script>

<template>
    <AppLayout>
        <template #title>Notifikasi</template>

        <div class="max-w-3xl">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Notifikasi</h2>
                <button
                    v-if="unreadCount > 0"
                    type="button"
                    @click="markAllAsRead"
                    class="rounded-lg border border-transparent bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                >
                    Tandai Semua Dibaca
                </button>
            </div>

            <div v-if="notificationsData.data.length > 0" class="space-y-3">
                <div
                    v-for="notification in notificationsData.data"
                    :key="notification.id"
                    :class="[
                        'flex items-start gap-3 rounded-lg border p-4',
                        notification.read_at
                            ? 'border-gray-200 bg-white'
                            : 'border-blue-100 bg-blue-50',
                    ]"
                >
                    <div
                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-100"
                    >
                        <svg
                            class="h-5 w-5 text-blue-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                            />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ notification.data.title }}
                                </p>
                                <p class="mt-0.5 text-sm text-gray-700">
                                    {{ notification.data.message }}
                                </p>
                            </div>
                            <p class="text-xs whitespace-nowrap text-gray-500">
                                {{
                                    formatRelativeDate(notification.created_at)
                                }}
                            </p>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <a
                                v-if="notification.data.action_url"
                                :href="notification.data.action_url"
                                class="rounded-lg bg-blue-600 px-3 py-1 text-sm font-medium text-white hover:bg-blue-700"
                            >
                                {{ notification.data.action_text ?? 'Lihat' }}
                            </a>
                            <button
                                v-if="!notification.read_at"
                                @click="markAsRead(notification)"
                                class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Tandai Dibaca
                            </button>
                            <button
                                @click="confirmDelete(notification)"
                                class="rounded-lg border border-red-300 px-3 py-1 text-sm font-medium text-red-600 hover:bg-red-50"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="notificationsData.last_page > 1"
                    class="mt-6 border-t border-gray-200 pt-4"
                >
                    <nav class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Menampilkan {{ notificationsData.from }} sampai
                            {{ notificationsData.to }} dari
                            {{ notificationsData.total }} notifikasi
                        </div>
                        <div class="flex gap-2">
                            <button
                                v-if="notificationsData.prev_page_url"
                                @click="
                                    goToPage(notificationsData.current_page - 1)
                                "
                                class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Sebelumnya
                            </button>
                            <button
                                v-if="notificationsData.next_page_url"
                                @click="
                                    goToPage(notificationsData.current_page + 1)
                                "
                                class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Selanjutnya
                            </button>
                        </div>
                    </nav>
                </div>
            </div>

            <div v-else class="py-12 text-center">
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
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                        />
                    </svg>
                </div>
                <h3 class="mb-1 text-lg font-medium text-gray-900">
                    Belum ada notifikasi
                </h3>
                <p class="text-sm text-gray-500">
                    Notifikasi akan muncul di sini saat ada aktivitas yang
                    relevan.
                </p>
            </div>

            <!-- Delete confirmation modal -->
            <div
                v-if="showDeleteModal"
                class="fixed inset-0 z-50 overflow-y-auto"
            >
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="fixed inset-0 bg-gray-900/50"
                        @click="showDeleteModal = null"
                    />
                    <div
                        class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl"
                    >
                        <h3 class="mb-2 text-lg font-medium text-gray-900">
                            Hapus Notifikasi
                        </h3>
                        <p class="mb-4 text-gray-600">
                            Apakah Anda yakin ingin menghapus notifikasi ini?
                        </p>
                        <div class="flex justify-end gap-3">
                            <button
                                type="button"
                                @click="showDeleteModal = null"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                @click="deleteNotification(showDeleteModal)"
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
