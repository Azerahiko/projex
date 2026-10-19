<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

const { auth } = usePage().props;
</script>

<template>
    <AppLayout>
        <template #title>Pengaturan</template>
        
        <div class="max-w-2xl">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Profil</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input
                            type="text"
                            :value="auth.user.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500"
                            readonly
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input
                            type="email"
                            :value="auth.user.email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500"
                            readonly
                        />
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-500">
                            Fitur pengaturan profil dan password akan segera tersedia.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
