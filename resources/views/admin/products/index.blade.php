@extends("layouts.admin")

@section("title", "المنتجات — لوحة التحكم")

@section("content")
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">المنتجات</h1>
            <p class="text-sm text-gray-500 mt-1">{{ isset($products) ? $products->total() : 0 }} منتج</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn-primary text-sm">+ إضافة منتج</a>
    </div>

    <div class="card overflow-hidden">
        <form method="GET" action="{{ route('admin.products.index') }}" class="p-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="بحث بالاسم أو SKU..." class="input py-2 text-sm max-w-xs">
            <select name="status" class="input py-2 text-sm w-auto">
                <option value="">كل الحالات</option>
                @if(class_exists('\App\Domains\Catalog\Enums\PublicationStatus'))
                    @foreach(\App\Domains\Catalog\Enums\PublicationStatus::cases() as $s)
                        <option value="{{ $s->value }}" {{ request('status') == $s->value ? 'selected' : '' }}>
                            {{ method_exists($s, 'label') ? $s->label() : $s->name }}
                        </option>
                    @endforeach
                @endif
            </select>
            <button type="submit" class="btn-ghost text-sm py-2">تطبيق</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-right text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">المنتج</th>
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">السعر</th>
                        <th class="px-4 py-3">المخزون</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products ?? [] as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->thumbnail_url ?? 'https://picsum.photos/seed/' . $product->id . '/60/60' }}" class="w-10 h-10 rounded-lg object-cover bg-gray-50">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $product->category->name ?? "—" }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $product->sku ?? "—" }}</td>
                            <td class="px-4 py-3 font-semibold">{{ number_format($product->price ?? 0, 2) }} ر.س</td>
                            <td class="px-4 py-3">
                                <span class="{{ ($product->stock ?? 0) < 10 ? 'text-red-600 font-semibold' : '' }}">{{ $product->stock ?? 0 }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusValue = is_object($product->status) ? $product->status->value : $product->status;
                                    $statusLabel = is_object($product->status) && method_exists($product->status, 'label') 
                                        ? $product->status->label() 
                                        : ($statusValue ?? 'مسودة');
                                    $isPublished = $statusValue === 'published';
                                @endphp
                                <span class="badge-{{ $isPublished ? 'success' : 'warning' }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-left">
                                <a href="{{ route('admin.products.show', $product->id) }}" class="text-brand-600 hover:text-brand-700 text-xs font-semibold">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">لا توجد منتجات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($products) && method_exists($products, 'links'))
            <div class="p-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
