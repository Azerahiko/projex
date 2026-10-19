<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { login as loginRoute, register as registerRoute, logout as logoutRoute, dashboard } from '@/routes';
import { router } from '@inertiajs/vue3';

const page = usePage();
const auth = page.props.auth;
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex items-center justify-center mb-6">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-xl">P</span>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 text-center">
                    Selamat Datang di Projex
                </h1>
                <p class="mt-2 text-sm text-gray-600 text-center">
                    Manajemen project sederhana untuk tim Anda.
                </p>
            </div>

            <div v-if="auth.user">
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <p class="text-gray-700 mb-4">Halo, <strong>{{ auth.user.name }}</strong></p>
                    <div class="flex gap-3 justify-center">
                        <Link
                            :href="dashboard.url()"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            Dashboard
                        </Link>
                        <button
                            type="button"
                            @click="router.post(logoutRoute.url())"
                            class="px-4 py-2 text-red-600 text-sm font-medium hover:underline"
                        >
                            Keluar
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="grid grid-cols-2 gap-4">
                <Link
                    :href="loginRoute.url()"
                    class="px-4 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors text-center"
                >
                    Masuk
                </Link>
                <Link
                    :href="registerRoute.url()"
                    class="px-4 py-3 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 transition-colors text-center"
                >
                    Daftar
                </Link>
            </div>
        </div>
    </div>
</template>
