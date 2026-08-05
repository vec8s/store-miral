@extends('layouts.app')
@section('title', 'المتجر — رافال')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">متجرنا 🛍️</h1>
    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <h3 class="font-semibold mb-2">خاتم الماس فاخر</h3>
            <p class="text-brand-600 font-bold mb-4">450 ر.س</p>
            <button @click="$store.cart.add({ id: 1, name: 'خاتم الماس فاخر', price: 450, image: 'https://via.placeholder.com/300' })" class="w-full py-2 bg-brand-600 text-white rounded-lg">أضف للسلة</button>
        </div>
    </div>
</div>
@endsection
