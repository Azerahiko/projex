<script setup>
import { useForm, usePage } from '@inertiajs/vue3';

const errors = usePage().props.errors ?? {};
const form = useForm({
  email: '',
  password: '',
  remember: false,
});

function submit() {
  form.post(route('login.store'), {
    onSuccess: () => {
      // Handled by Inertia redirect
    },
    onError: () => {
      // Validation errors handled by Inertia
    },
  });
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="max-w-md w-full space-y-6">
      <div class="mx-auto">
        <h1 class="mt-6 text-3xl font-bold text-gray-900 text-center">
          Masuk ke Projex
        </h1>
        <p class="mt-2 text-sm text-gray-600 text-center">
          Selamat datang kembali. Silakan masuk ke akun Anda.
        </p>
      </div>

      <div class="mt-8 px-4 py-8 bg-white shadow sm:rounded-lg sm:p-10">
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
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-input"
                  v-model="form.email"
                  :class="{ 'border-red-500': errors.email }"
                />
                <p
                  v-if="errors.email"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ errors.email[0] }}
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
                <div class="text-sm">
                  <a
                    href="#"
                    class="font-medium text-blue-600 hover:text-blue-500"
                    >Lupa password?</a
                  >
                </div>
              </div>
              <div class="mt-1">
                <input
                  id="password"
                  name="password"
                  type="password"
                  autocomplete="current-password"
                  required
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-input"
                  v-model="form.password"
                  :class="{ 'border-red-500': errors.password }"
                />
                <p
                  v-if="errors.password"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ errors.password[0] }}
                </p>
              </div>
            </div>

            <div class="flex items-center">
              <input
                id="remember"
                name="remember"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                v-model="form.remember"
              />
              <label for="remember" class="ml-2 block text-sm text-gray-900">
                Ingat saya
              </label>
            </div>

            <div class="flex items-center justify-end mt-6">
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-70"
              >
                Masuk
              </button>
            </div>
          </div>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600">
          Belum punya akun?
          <a
            href="{{ route('register') }}"
            class="font-medium text-blue-600 hover:text-blue-500"
          >
            Daftar sekarang
          </a>
        </p>
      </div>
    </div>
  </div>
</template>
