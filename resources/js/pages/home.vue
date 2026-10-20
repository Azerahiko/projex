<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { login as loginRoute, logout as logoutRoute, dashboard } from '@/routes';
import { router } from '@inertiajs/vue3';

const page = usePage();
const auth = page.props.auth;
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md space-y-8">
            <div>
                <div class="mb-6 flex items-center justify-center">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600"
                    >
                        <span class="text-xl font-bold text-white">P</span>
                    </div>
                </div>
                <h1 class="text-center text-3xl font-bold text-gray-900">
                    Selamat Datang di Projex
                </h1>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Manajemen project sederhana untuk tim Anda.
                </p>
            </div>

            <div v-if="auth.user">
                <div class="rounded-lg bg-white p-6 text-center shadow">
                    <p class="mb-4 text-gray-700">
                        Halo, <strong>{{ auth.user.name }}</strong>
                    </p>
                    <div class="flex justify-center gap-3">
                        <Link
                            :href="dashboard.url()"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                        >
                            Dashboard
                        </Link>
                        <button
                            type="button"
                            @click="router.post(logoutRoute.url())"
                            class="px-4 py-2 text-sm font-medium text-red-600 hover:underline"
                        >
                            Keluar
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="flex justify-center">
                <Link
                    :href="loginRoute.url()"
                    class="rounded-lg border border-transparent bg-blue-600 px-4 py-3 text-center text-sm font-medium text-white transition-colors hover:bg-blue-700 w-full max-w-xs"
                >
                    Masuk
                </Link>
            </div>
        </div>
    </div>
</template>