<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

const { auth } = usePage().props;
</script>

<template>
    <AppLayout>
        <template #title>Pengaturan</template>

        <div class="max-w-2xl">
            <div class="rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 p-6">
                    <h2 class="text-lg font-medium text-gray-900">Profil</h2>
                </div>

                <div class="space-y-6 p-6">
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Nama</label
                        >
                        <input
                            type="text"
                            :value="auth.user.name"
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-500"
                            readonly
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Email</label
                        >
                        <input
                            type="email"
                            :value="auth.user.email"
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-gray-500"
                            readonly
                        />
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-sm text-gray-500">
                            Fitur pengaturan profil dan password akan segera
                            tersedia.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
