@php
    $current = request()->route()?->getName() ?? "";
@endphp

<nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm scrollbar-thin">
    @php
        $groups = [
            "الرئيسية" => [
                ["name" => "admin.dashboard", "route" => "admin.dashboard", "label" => "لوحة التحكم", "icon" => "M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"],
            ],
            "المتجر" => [
                ["name" => "admin.products.index", "route" => "admin.products.index", "label" => "المنتجات", "icon" => "M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"],
                ["name" => "admin.orders.index", "route" => "admin.orders.index", "label" => "الطلبات", "icon" => "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"],
                ["name" => "admin.customers.index", "route" => "admin.customers.index", "label" => "العملاء", "icon" => "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"],
            ],
            "النظام" => [
                ["name" => "admin.settings", "route" => "admin.settings", "label" => "الإعدادات", "icon" => "M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"],
                ["name" => "admin.sync", "route" => "admin.sync.index", "label" => "مزامنة سلة", "icon" => "M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"],
            ],
        ];
    @endphp

    @foreach($groups as $heading => $items)
        <p class="px-3 pt-4 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $heading }}</p>
        @foreach($items as $item)
            @php $active = str_starts_with($current, $item["name"]); @endphp
            <a href="{{ route($item["route"]) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                      {{ $active ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                <span>{{ $item["label"] }}</span>
            </a>
        @endforeach
    @endforeach
</nav>
