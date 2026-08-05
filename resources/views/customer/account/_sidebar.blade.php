@php $active = $active ?? 'profile'; @endphp
<aside class="lg:col-span-1">
    <nav class="card p-3 space-y-1 text-sm">
        <a href="{{ route('account.profile') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $active === 'profile' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
            <span>👤</span> المعلومات الشخصية
        </a>
        <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $active === 'orders' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
            <span>📦</span> طلباتي
        </a>
        <a href="{{ route('account.addresses') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $active === 'addresses' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
            <span>📍</span> العناوين
        </a>
        <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $active === 'wishlist' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
            <span>💝</span> المفضلة
        </a>
        <a href="{{ route('account.password') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $active === 'password' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
            <span>🔒</span> تغيير كلمة المرور
        </a>
    </nav>
</aside>
