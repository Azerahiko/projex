<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { login as loginRoute } from '@/routes';

const page = usePage();
const { invitation, error } = page.props;

const form = useForm({
    name: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('invitations.accept', page.props.invitation?.token), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md space-y-6">
            <div class="mx-auto">
                <div class="mb-6 flex items-center justify-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600">
                        <span class="text-xl font-bold text-white">P</span>
                    </div>
                </div>
                <h1 class="text-center text-3xl font-bold text-gray-900">
                    Terima Undangan Projex
                </h1>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Bergabung ke tim dan kelola project bersama.
                </p>
            </div>

            <div v-if="error" class="rounded-lg bg-red-50 p-4 border border-red-200">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-red-800">{{ error }}</p>
                </div>
            </div>

            <div v-else-if="invitation" class="bg-white p-6 shadow sm:rounded-lg sm:p-10">
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-6">
                        <h2 class="text-lg font-medium text-gray-900">Detail Undangan</h2>
                        <dl class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="text-sm text-gray-900">{{ invitation.email }}</dd>
                            <dt class="text-sm font-medium text-gray-500">Peran</dt>
                            <dd class="text-sm text-gray-900 capitalize">{{ invitation.role }}</dd>
                            <dt class="text-sm font-medium text-gray-500">Dikirim oleh</dt>
                            <dd class="text-sm text-gray-900">{{ invitation.inviter?.name }}</dd>
                            <dt class="text-sm font-medium text-gray-500">Kedaluwarsa</dt>
                            <dd class="text-sm text-gray-900">{{ formatDate(invitation.expires_at) }}</dd>
                        </dl>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Buat Akun Anda</h3>
                        <form @submit.prevent="submit">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <input
                                        id="name"
                                        name="name"
                                        type="text"
                                        autocomplete="name"
                                        required
                                        class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        v-model="form.name"
                                        :class="{ 'border-red-500': form.errors.name }"
                                    />
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.name }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        autocomplete="new-password"
                                        required
                                        minlength="8"
                                        class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        v-model="form.password"
                                        :class="{ 'border-red-500': form.errors.password }"
                                    />
                                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.password }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        autocomplete="new-password"
                                        required
                                        class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        v-model="form.password_confirmation"
                                        :class="{ 'border-red-500': form.errors.password_confirmation }"
                                    />
                                    <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.password_confirmation }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-70"
                                >
                                    Terima Undangan & Masuk
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white p-6 shadow sm:rounded-lg sm:p-10 text-center">
                <p class="text-gray-600">Memuat detail undangan...</p>
            </div>
        </div>
    </div>
</template>