@extends('layouts.base')

@section('body')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-bl from-brand-50 via-white to-amber-50 px-4 py-12">
    <div class="w-full max-w-md">
        <a href="{{ url('/') }}" class="flex items-center justify-center gap-3 mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-500 to-gold-500 flex items-center justify-center text-white text-2xl font-bold shadow-card">
                ر
            </div>
            <span class="text-2xl font-bold text-gray-900">رافال</span>
        </a>

        <div class="card p-8">
            <h1 class="text-2xl font-bold text-gray-900 text-center mb-2">
                @yield('heading')
            </h1>
            <p class="text-gray-500 text-center mb-6">
                @yield('subheading')
            </p>

            @yield('content')
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            @yield('footer-link')
        </p>
    </div>
</div>
@endsection
