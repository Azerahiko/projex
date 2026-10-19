<script setup>
import { usePage } from '@inertiajs/vue3';

const { auth } = usePage().props;
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h1 class="mt-6 text-3xl font-bold text-gray-900">
          Selamat Datang di Projex
        </h1>
        <p class="mt-2 text-sm text-gray-600">
          Manajemen project sederhana untuk tim Anda.
        </p>
      </div>

      <div>
        @guest
        <div class="grid grid-cols-2 gap-4">
          <a
            href="{{ route('login') }}"
            class="px-4 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
          >
            Masuk
          </a>
          <a
            href="{{ route('register') }}"
            class="px-4 py-3 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700"
          >
            Daftar
          </a>
        </div>
        @else
        <div>
          <p class="text-sm text-gray-600">Halo, {{ auth.user.name }}.</p>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
              class="px-4 py-3 text-sm font-medium text-red-600 hover:underline"
              type="submit"
            >
              Keluar
            </button>
          </form>
        </div>
        @endguest
      </div>
    </div>
  </div>
</template>
