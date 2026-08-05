@extends("layouts.app")

@section("title", "رافال — متجر الحلي والهدايا الفاخرة")
@section("description", "اكتشف عالمنا من السلاسل والأساور وبوكسات الهدايا. توصيل سريع لجميع مناطق المملكة.")

@section("content")
{{-- HERO --}}
<section class="relative overflow-hidden bg-gradient-to-bl from-brand-50 via-white to-amber-50">
    <div class="container-rtl py-16 lg:py-24 grid lg:grid-cols-2 gap-10 items-center">
        <div class="text-center lg:text-right">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-100 text-brand-700 text-xs font-semibold mb-5">
                ✨ مجموعة جديدة 2026
            </span>
            <h1 class="text-4xl lg:text-6xl font-extrabold text-gray-900 leading-tight text-balance">
                صنعت خصيصاً لك
            </h1>
            <p class="mt-5 text-lg text-gray-600 leading-relaxed max-w-xl lg:max-w-none">
                اكتشف عالم رافال... حيث يلتقي الذوق الرفيع بالتفاصيل الفاخرة. تشكيلة مختارة من الحلي والهدايا لكل مناسبة.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center lg:justify-start gap-3">
                <a href="{{ route("shop.index") }}" class="btn-primary px-7 py-3 text-base">تسوّق الآن</a>
                <a href="{{ url("/about") }}" class="btn-ghost px-6 py-3 text-base">تعرّف علينا</a>
            </div>
            <div class="mt-10 flex items-center justify-center lg:justify-start gap-8 text-sm text-gray-500">
                <div><span class="block text-2xl font-bold text-brand-700">+10K</span>عميل سعيد</div>
                <div><span class="block text-2xl font-bold text-brand-700">+500</span>منتج مميز</div>
                <div><span class="block text-2xl font-bold text-brand-700">4.9★</span>تقييم العملاء</div>
            </div>
        </div>

        <div class="relative">
            <div class="aspect-square rounded-3xl bg-gradient-to-br from-brand-200 via-brand-100 to-amber-100 shadow-card flex items-center justify-center">
                <div class="text-center">
                    <div class="text-9xl">💎</div>
                    <p class="mt-4 text-brand-800 font-semibold">مجوهرات فاخرة</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 rounded-2xl bg-white shadow-card flex items-center justify-center">
                <div class="text-center">
                    <p class="text-xs text-gray-500">خصم يصل</p>
                    <p class="text-2xl font-bold text-brand-700">50%</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CATEGORIES --}}
<section class="py-16">
    <div class="container-rtl">
        <h2 class="section-title">أقســـام رافَـــال</h2>
        <p class="section-subtitle">تشكيلات متنوعة لكل ذوق ومناسبة</p>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
                $cats = [
                    ["name" => "السلاسل", "icon" => "📿", "color" => "from-pink-100 to-pink-50"],
                    ["name" => "الأساور", "icon" => "💫", "color" => "from-purple-100 to-purple-50"],
                    ["name" => "بوكسات هدايا", "icon" => "🎁", "color" => "from-amber-100 to-amber-50"],
                    ["name" => "هدايا رجالية", "icon" => "🎩", "color" => "from-blue-100 to-blue-50"],
                    ["name" => "الساعات", "icon" => "⌚", "color" => "from-slate-100 to-slate-50"],
                    ["name" => "السبح", "icon" => "📿", "color" => "from-emerald-100 to-emerald-50"],
                ];
            @endphp
            @foreach($cats as $cat)
                <a href="{{ route("shop.index", ["category" => $cat["name"]]) }}"
                   class="group p-6 rounded-2xl bg-gradient-to-br {{ $cat["color"] }} hover:shadow-card transition-all text-center">
                    <div class="text-5xl mb-3 group-hover:scale-110 transition-transform">{{ $cat["icon"] }}</div>
                    <p class="font-semibold text-gray-800">{{ $cat["name"] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- FEATURED PRODUCTS --}}
<section class="py-16 bg-white">
    <div class="container-rtl">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="section-title text-right mb-1">أحدث المنتجات</h2>
                <p class="text-gray-500 text-sm">تشكيلة مختارة من أجمل ما لدينا</p>
            </div>
            <a href="{{ route("shop.index") }}" class="hidden sm:inline-flex btn-ghost text-sm">عرض الكل ←</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @forelse($featured ?? [] as $product)
                <x-product-card :product="$product" />
            @empty
                @for($i = 0; $i < 4; $i++)
                    <x-product-card :product="new \App\Domains\Catalog\Models\Product([
                        'id' => $i, 'name' => 'منتج تجريبي '.($i+1), 'slug' => 'demo-'.$i,
                        'price' => rand(50, 500), 'sale_price' => null,
                        'thumbnail_url' => 'https://picsum.photos/seed/rafal'.$i.'/400/400',
                        'created_at' => now(),
                    ])" />
                @endfor
            @endforelse
        </div>
    </div>
</section>

{{-- FEATURES --}}
<section class="py-16 bg-gradient-to-br from-brand-50 to-amber-50">
    <div class="container-rtl">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $features = [
                    ["icon" => "🚚", "title" => "توصيل سريع", "desc" => "نقدم خدمة توصيل سريعة لجميع عملائنا"],
                    ["icon" => "💰", "title" => "ضمان استعادة الأموال", "desc" => "ملتزمون بأعلى مستوى من الجودة والرضا"],
                    ["icon" => "🎁", "title" => "برامج الولاء", "desc" => "تخفيضات حصرية وبرامج ولاء جذابة"],
                    ["icon" => "💬", "title" => "دعم فني 24/7", "desc" => "دعم تقني قوي لتجربة تسوق مثالية"],
                ];
            @endphp
            @foreach($features as $f)
                <div class="card p-6 text-center">
                    <div class="text-5xl mb-3">{{ $f["icon"] }}</div>
                    <h3 class="font-bold text-gray-900 mb-1.5">{{ $f["title"] }}</h3>
                    <p class="text-sm text-gray-600">{{ $f["desc"] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- TESTIMONIALS --}}
<section class="py-16">
    <div class="container-rtl">
        <h2 class="section-title">آراء العملاء</h2>
        <p class="section-subtitle">ماذا يقول عملاؤنا عن تجربتهم معنا</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $reviews = [
                    ["name" => "محمد الردادي", "text" => "رائع جداا، الجودة فوق الممتاز والتوصيل كان سريع جداً.", "rating" => 5],
                    ["name" => "مرام البارقي", "text" => "يجننن ويستحق التجربة ☺️ شكراً من القلب على الهدية الرائعة.", "rating" => 5],
                    ["name" => "حسن العتيبي", "text" => "جداً أنيقة وجميلة، شكراً رافال 😍❤️.", "rating" => 5],
                ];
            @endphp
            @foreach($reviews as $r)
                <div class="card p-6">
                    <div class="flex items-center gap-1 mb-3">
                        @for($i = 0; $i < $r["rating"]; $i++)
                            <svg class="w-4 h-4 text-amber-400 fill-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.447a1 1 0 00-.364 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.366-2.447a1 1 0 00-1.176 0l-3.366 2.447c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.063 9.384c-.784-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-700 text-sm leading-relaxed mb-4">"{{ $r["text"] }}"</p>
                    <p class="font-semibold text-gray-900 text-sm">— {{ $r["name"] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
