<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { login as loginRoute, register as registerRoute } from '@/routes';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(registerRoute.url(), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="max-w-md w-full space-y-6">
            <div class="mx-auto">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-xl">P</span>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 text-center">
                    Buat Akun Projex
                </h1>
                <p class="mt-2 text-sm text-gray-600 text-center">
                    Bergabunglah dengan Projex dan mulai kelola project Anda.
                </p>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6 sm:p-10">
                <form @submit.prevent="submit">
                    <div class="space-y-6">
                        <div>
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700"
                                >Nama Lengkap</label
                            >
                            <div class="mt-1">
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    autocomplete="name"
                                    required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                                    v-model="form.name"
                                    :class="{ 'border-red-500': form.errors.name }"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label
                                for="email"
                                class="block text-sm font-medium text-gray-700"
                                >Email</label
                            >
                            <div class="mt-1">
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    autocomplete="email"
                                    required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                                    v-model="form.email"
                                    :class="{ 'border-red-500': form.errors.email }"
                                />
                                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label
                                for="password"
                                class="block text-sm font-medium text-gray-700"
                                >Password</label
                            >
                            <div class="mt-1">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="new-password"
                                    required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                                    v-model="form.password"
                                    :class="{ 'border-red-500': form.errors.password }"
                                />
                                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.password }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Minimal 8 karakter.
                                </p>
                            </div>
                        </div>

                        <div>
                            <label
                                for="password_confirmation"
                                class="block text-sm font-medium text-gray-700"
                                >Konfirmasi Password</label
                            >
                            <div class="mt-1">
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                                    v-model="form.password_confirmation"
                                    :class="{ 'border-red-500': form.errors.password_confirmation }"
                                />
                                <p
                                    v-if="form.errors.password_confirmation"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.password_confirmation }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-70 transition-colors"
                            >
                                Daftar
                            </button>
                        </div>
                    </div>
                </form>

                <p class="mt-6 text-center text-sm text-gray-600">
                    Sudah punya akun?
                    <Link
                        :href="loginRoute.url()"
                        class="font-medium text-blue-600 hover:text-blue-500"
                    >
                        Masuk
                    </Link>
                </p>
            </div>
        </div>
    </div>
</template>
