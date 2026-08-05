@extends('layouts.base')

@section('body')
<div class="min-h-screen flex bg-gray-100" x-data="{ sidebarOpen: false }">

    <div x-show="sidebarOpen"
         x-transition.opacity
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/40 z-30 lg:hidden"
         x-cloak></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'"
           class="fixed inset-y-0 right-0 w-64 bg-white border-l border-gray-200 z-40
                  transform transition-transform lg:translate-x-0 lg:static lg:inset-auto lg:translate-x-0
                  flex flex-col">
        <div class="h-16 flex items-center justify-center border-b border-gray-200">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-white font-bold">ر</div>
                <span class="font-bold text-gray-900">لوحة التحكم</span>
            </a>
        </div>

        <x-admin-sidebar />

        <div class="border-t border-gray-200 p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-right px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50">
                    🚪 تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-8">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">{{ auth()->user()->name ?? 'المدير' }}</span>
                <div class="w-9 h-9 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold">
                    {{ mb_substr(auth()->user()->name ?? 'م', 0, 1) }}
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>
@endsection
