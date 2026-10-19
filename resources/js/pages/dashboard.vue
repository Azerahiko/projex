<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

const { auth } = usePage().props;
</script>

<template>
    <AppLayout>
        <template #title>Dashboard</template>
        
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-2">
                    Selamat Datang, {{ auth.user.name }}
                </h2>
                <p class="text-gray-600">
                    Ini adalah dashboard Projex. Project dan tugas Anda akan ditampilkan di sini setelah fitur manajemen project diimplementasikan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Projects card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                        <h3 class="font-medium text-gray-900">Projects</h3>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">0</p>
                    <p class="text-sm text-gray-500">Project aktif</p>
                </div>

                <!-- Tasks card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-medium text-gray-900">Tugas</h3>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">0</p>
                    <p class="text-sm text-gray-500">Tugas menunggu</p>
                </div>

                <!-- Completed card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="font-medium text-gray-900">Selesai</h3>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">0</p>
                    <p class="text-sm text-gray-500">Tugas selesai</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
