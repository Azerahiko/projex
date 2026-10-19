<script setup>
import { usePage } from '@inertiajs/vue3';

const { auth } = usePage().props;
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <h1 class="font-semibold text-xl text-gray-900 tracking-tight">
                Projex Dashboard
              </h1>
            </div>
          </div>
          <div class="hidden sm:ml-6 sm:flex sm:items-center">
            <span class="mr-4 text-sm text-gray-700">
              Halo, {{ auth.user.name }}
            </span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
              @csrf
              <button
                class="px-3 py-2 text-sm font-medium text-red-600 hover:underline"
                type="submit"
              >
                Keluar
              </button>
            </form>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="border-4 border-dashed border-gray-200 rounded-lg h-96 flex flex-col items-center justify-center text-center">
          <h2 class="text-lg font-medium text-gray-900">
            Dashboard Anda
          </h2>
          <p class="mt-1 max-w-xs mx-auto text-sm text-gray-500">
            Project dan tugas akan ditampilkan di sini. Fitur manajemen project
            akan tersedia dalam tahap pengembangan selanjutnya.
          </p>
        </div>
      </div>
    </main>
  </div>
</template>
