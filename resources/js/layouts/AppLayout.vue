<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { logout as logoutRoute, dashboard } from '@/routes';
import { index as projectsIndex } from '@/routes/projects';
import { index as tasksIndex } from '@/routes/tasks';
import { index as settingsIndex } from '@/routes/settings';

const projects = projectsIndex;
const tasks = tasksIndex;
const settings = settingsIndex;

const page = usePage();
const auth = computed(() => page.props.auth);

const sidebarOpen = ref(false);
const dropdownOpen = ref(false);

const navigation = [
    { name: 'Dashboard', href: dashboard.url(), icon: 'home', current: page.url === '/dashboard' },
    { name: 'Projects', href: projects.url(), icon: 'folder', current: page.url.startsWith('/projects') },
    { name: 'Tasks', href: tasks.url(), icon: 'check-circle', current: page.url.startsWith('/tasks') },
    { name: 'Settings', href: settings.url(), icon: 'cog', current: page.url.startsWith('/settings') },
];

function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value;
}

function toggleDropdown() {
    dropdownOpen.value = !dropdownOpen.value;
}

function logout() {
    dropdownOpen.value = false;
    router.post(logoutRoute.url());
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Mobile sidebar overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-200 ease-in-out lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center h-16 px-6 border-b border-gray-200">
                    <Link :href="dashboard.url()" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">P</span>
                        </div>
                        <span class="font-semibold text-lg text-gray-900">Projex</span>
                    </Link>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                            item.current
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-gray-700 hover:bg-gray-100'
                        ]"
                        @click="sidebarOpen = false"
                    >
                        <!-- Home icon -->
                        <svg v-if="item.icon === 'home'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <!-- Folder icon -->
                        <svg v-else-if="item.icon === 'folder'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <!-- Check-circle icon -->
                        <svg v-else-if="item.icon === 'check-circle'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <!-- Cog icon -->
                        <svg v-else-if="item.icon === 'cog'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ item.name }}
                    </Link>
                </nav>

                <!-- User section in sidebar (mobile) -->
                <div class="p-4 border-t border-gray-200 lg:hidden">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">{{ auth.user?.name?.charAt(0) ?? 'U' }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ auth.user?.name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth.user?.email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top navigation -->
            <header class="sticky top-0 z-30 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <!-- Mobile menu button -->
                    <button
                        type="button"
                        class="p-2 -ml-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100"
                        @click="toggleSidebar"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Page title -->
                    <div class="flex-1 lg:flex-none">
                        <h1 class="text-lg font-semibold text-gray-900">
                            <slot name="title">Dashboard</slot>
                        </h1>
                    </div>

                    <!-- User dropdown -->
                    <div class="relative">
                        <button
                            type="button"
                            class="flex items-center gap-2 p-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100"
                            @click="toggleDropdown"
                        >
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">{{ auth.user?.name?.charAt(0) ?? 'U' }}</span>
                            </div>
                            <span class="hidden sm:block">{{ auth.user?.name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div
                            v-if="dropdownOpen"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                        >
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ auth.user?.name }}</p>
                                <p class="text-xs text-gray-500">{{ auth.user?.email }}</p>
                            </div>
                            <button
                                type="button"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50"
                                @click="logout"
                            >
                                Keluar
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>

        <!-- Click outside to close dropdown -->
        <div
            v-if="dropdownOpen"
            class="fixed inset-0 z-40"
            @click="dropdownOpen = false"
        />
    </div>
</template>
