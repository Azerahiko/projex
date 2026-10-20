<script setup>
import { useForm } from '@inertiajs/vue3';
import { login as loginRoute } from '@/routes';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(loginRoute.url(), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md space-y-6">
            <div class="mx-auto">
                <div class="mb-6 flex items-center justify-center">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600"
                    >
                        <span class="text-xl font-bold text-white">P</span>
                    </div>
                </div>
                <h1 class="text-center text-3xl font-bold text-gray-900">
                    Masuk ke Projex
                </h1>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Selamat datang kembali. Silakan masuk ke akun Anda.
                </p>
            </div>

            <div class="bg-white p-6 shadow sm:rounded-lg sm:p-10">
                <form @submit.prevent="submit">
                    <div class="space-y-6">
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
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    v-model="form.email"
                                    :class="{
                                        'border-red-500': form.errors.email,
                                    }"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <label
                                    for="password"
                                    class="block text-sm font-medium text-gray-700"
                                    >Password</label
                                >
                            </div>
                            <div class="mt-1">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    v-model="form.password"
                                    :class="{
                                        'border-red-500': form.errors.password,
                                    }"
                                />
                                <p
                                    v-if="form.errors.password"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.password }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                v-model="form.remember"
                            />
                            <label
                                for="remember"
                                class="ml-2 block text-sm text-gray-900"
                            >
                                Ingat saya
                            </label>
                        </div>

                        <div class="flex items-center justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-70"
                            >
                                Masuk
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>