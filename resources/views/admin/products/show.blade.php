@extends("layouts.admin")

@section("title", ($product->name ?? "تفاصيل المنتج") . " — لوحة التحكم")

@section("content")
<div class="space-y-6">
    <a href="{{ route('admin.products.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">← العودة للمنتجات</a>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $product->category->name ?? "—" }}</p>
                </div>
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
            </div>

            <img src="{{ $product->thumbnail_url ?? 'https://picsum.photos/seed/' . $product->id . '/800/600' }}"
                 alt="{{ $product->name }}"
                 class="w-full rounded-xl mb-4 bg-gray-50 object-cover max-h-96">

            <h2 class="font-bold text-gray-900 mb-2">الوصف</h2>
            <div class="prose prose-sm max-w-none text-gray-600">
                {!! $product->description ?? "<p class='text-gray-400'>لا يوجد وصف متوفر.</p>" !!}
            </div>
        </div>

        <aside class="space-y-4">
            <div class="card p-6">
                <h3 class="font-bold text-gray-900 mb-4">معلومات المنتج</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">SKU</dt><dd class="font-mono text-gray-900">{{ $product->sku ?? "—" }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">السعر</dt><dd class="font-semibold text-brand-700">{{ number_format($product->price ?? 0, 2) }} ر.س</dd></div>
                    @if(!empty($product->sale_price))
                        <div class="flex justify-between"><dt class="text-gray-500">سعر البيع</dt><dd class="font-semibold text-red-600">{{ number_format($product->sale_price, 2) }} ر.س</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-gray-500">المخزون</dt><dd class="{{ ($product->stock ?? 0) < 10 ? 'text-red-600 font-bold' : 'text-gray-900' }}">{{ $product->stock ?? 0 }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">الوزن</dt><dd class="text-gray-900">{{ $product->weight ?? "—" }} كجم</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">تاريخ الإضافة</dt><dd class="text-gray-900">{{ optional($product->created_at)->format('Y-m-d') ?? "—" }}</dd></div>
                </dl>
            </div>

            <div class="card p-6">
                <h3 class="font-bold text-gray-900 mb-4">إجراءات</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-primary w-full py-2.5 text-sm text-center block">تعديل</a>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا المنتج؟')">
                        @csrf 
                        @method("DELETE")
                        <button type="submit" class="btn-ghost w-full py-2.5 text-sm text-red-600 hover:bg-red-50">حذف</button>
                    </form>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
