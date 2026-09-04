# 🧠 عقل المشروع (Project Brain) & المرجع الأساسي

> هذا الملف هو **المرجع الوحيد** الذي يجب الرجوع إليه قبل أي تعديل أو إضافة أو حذف في هذا المشروع.
> أي قرار جديد يُتَّخذ يجب تسجيله هنا فوراً حتى لا يضيع السياق بين الجلسات.

---

## 1. فكرة المشروع والهدف (Project Vision & Concept)

### المشروع
- **الاسم:** `Miral Store - متجر ميرال` (مذكور في `metadata.json` كمتجر الحلي والهدايا الفاخرة — "Miral Jewelry & Luxury Gifts E-Commerce Store").
- **الاسم التقني في `.env`:** `APP_NAME="Headless Store"` — متجر **رأس بلا جسد (Headless)** يعتمد على واجهات برمجية (APIs).
- **الفكرة:** بناء متجر إلكتروني متكامل يعمل بواجهة أمامية حديثة (Livewire / Inertia + Vue / Tailwind) ومتصّل بمنصة **Salla** التجارية عبر OAuth 2.0 و REST APIs.

### التقنيات المستخدمة (Stack)
| الطبقة | التقنية |
|---|---|
| Backend | Laravel 13.x (`composer.json: ^13.17`) — فعلياً 13.23.0 |
| اللغة | PHP 8.5.8 (المطلوب في composer: `^8.3`) |
| الأمان/المصادقة | Laravel Fortify + Laravel Socialite |
| الواجهة التفاعلية | Livewire 4.1 + Flux 2.13.1 + Blaze |
| الواجهة الأمامية | Inertia + Vue (صفحات المتجر) + Blade (لوحة التحكم) |
| التنسيق | Tailwind CSS + Vite 8 (bundler: Rolldown) |
| اختبارات | PHPUnit ^12.5.23 (وليس Pest) |
| قاعدة البيانات | MySQL 8 (عبر Docker) |
| التكامل الخارجي | Salla OAuth 2.0 + Merchant APIs + Webhooks |

### الهدف النهائي
1. تسجيل دخول تاجر متجر Salla عبر OAuth 2.0.
2. جلب المنتجات والطلبات والعملاء من Salla إلى المتجر.
3. مزامنة البيانات والسماح بالإدارة من لوحة تحكم محلية.
4. متجر قابل للتسويق بواجهة عربية/إنجليزية احترافية واستجابة كاملة للجوال.

---

## 2. المتطلبات والاحتياجات الأساسية (Requirements & Dependencies)

### بيئة التطوير الفعلية (مثبتة ومُختبَرة)
- **PHP:** 8.5.8
- **Composer:** 2.10.1
- **Node.js:** v24.13.0
- **npm:** 11.6.2
- **Docker / Docker Compose:** متاح (يُستخدم لتشغيل MySQL و app)

> ⚠️ **قاعدة حاسمة:** مدير الحزم المعتمد هو **npm فقط**. لا تستخدم `pnpm` إطلاقاً — `pnpm run build` نقل حزم `node_modules` إلى `node_modules/.ignored` وكسر البناء (انظر القسم 4).

### Docker Compose (`docker-compose.yml`)
| الخدمة | التفاصيل |
|---|---|
| `app` | منفذ **8000** (حاوية Laravel) |
| MySQL | `mysql:latest` على منفذ **3307:3306** (المضيف 3307، داخل الحاوية 3306) |
| Volume | `dbdata` لحفظ البيانات |

### إعدادات `.env` الأساسية
| المفتاح | القيمة |
|---|---|
| `APP_NAME` | `Headless Store` |
| `DB_DATABASE` | `headless_store` |
| `QUEUE_CONNECTION` | `database` |
| `CACHE_STORE` | `database` |
| `MAIL_MAILER` | `log` |
| `SALLA_CLIENT_ID` / `SALLA_CLIENT_SECRET` | مفاتيح OAuth لتطبيق Salla |
| `SALLA_REDIRECT_URI` | رابط إعادة التوجيه بعد المصادقة |
| `SALLA_API_URL` | `https://api.salla.dev/admin/v2` |
| `SALLA_MERCHANT` / `SALLA_WEBHOOK` | بيانات التاجر والويب هوك |
| `SALLA_DRIVER` | **غير موجود بعد** في `.env.example` (يُضاف في مرحلة لاحقة — القيمة: `auto`/`mock`/`http`) |

### الحزم الرئيسية في `composer.json`
- `laravel/livewire-starter-kit`
- `inertiajs/inertia-laravel`
- `laravel/chisel`
- `laravel/fortify` `^1.37.2`
- `laravel/framework` `^13.17`
- `laravel/socialite` `^5.29`
- `livewire/blaze`
- `livewire/flux` `^2.13.1`
- `livewire/livewire` `^4.1`

### ملاحظة على اسم المتجر
- `metadata.json` (أداة AI خارجية): «Miral Store - متجر ميرال».
- `.env.example`: `APP_NAME="Headless Store"`.
- الحقيقة النهائية للاسم لم تُحسم — يُرجى توحيد الاسم قبل الإطلاق.

---

## 3. خطة العمل وخريطة الطريق (Master Roadmap & Tasks)

> مراحل تطوير تكامل Salla بأسلوب TDD (الاختبار أولاً، ثم التنفيذ، ثم الرفع للخضراء).

### المراحل المخطط لها (10 مراحل)
| # | المرحلة | الحالة |
|---|---|---|
| 1 | إنشاء DTOs (Data Transfer Objects) لبيانات Salla | ✅ مكتمل |
| 2 | إصلاح `SallaAuthenticator` (تخزين رمز الوصول + انتهاء الصلاحية) | ✅ مكتمل |
| 3 | إنشاء `MockSallaClient` (محاكاة بدون شبكة) | ✅ مكتمل |
| 4 | إنشاء `SallaClient` الحقيقي (عبر HTTP مع `Http::fake`) | ✅ مكتمل |
| 5 | إنشاء `SallaManager` (اختيار mock/http/auto) | ✅ مكتمل |
| 6 | إنشاء `SallaServiceProvider` وتسجيله | ✅ مكتمل |
| 7 | ربط الواجهة `SallaClientContract` في الحاوية | ✅ مكتمل |
| 8 | نظام Health Checks + نقطة `/up` | ✅ مكتمل |
| 9 | تجميع الاختبارات وتشغيل المجموعة الكاملة | ✅ تم (متبقٍ فقط أخطاء Auth المسبقة) |
| 10 | توثيق المشروع (`README.md` + `PROJECT_BRAIN.md`) + إضافة `SALLA_DRIVER` | ⏳ جارٍ (هذا الملف جزء منها) |

### مرحلة إضافية (غير مخطط لها مسبقاً)
- **تدقيق وتحسين استجابة الموبايل** لقالب Blade لوحة التحكم — ✅ مكتمل (القسم 4).
- **إكمال منطق عمل تكامل Salla (Production-Ready)** — ✅ مكتمل (القسم 4.7): تحقق توقيع Webhooks + معالجة أحداث المنتجات/الطلبات + خدمات المزامنة + Checkout مع كشف تدفقه.

### خريطة ما بعد المرحلة 10 (معلّقة)
- [ ] حسم مصير مكوّني «رافال» القديمين: `components/navbar.blade.php` و `components/footer.blade.php`.
- [ ] إصلاح إخفاقات Auth المسبقة (مكوّن `layouts::auth` المفقود، مسار `dashboard`، `UserFactory::withTwoFactor`).
- [ ] التحقق من سبب عودة `/up` بـ 503 في بيئة التشغيل الفعلية (وليس في الاختبارات).
- [ ] توضيح الغرض من مجلدَي الجذر الفارغين: `oneday/` و `seeders/`.
- [ ] **تحقق حي (بيانات اعتماد فعلية)**: تدفق Salla hosted-checkout الخارجي + توقيع الـ webhook الحقيقي من Salla Dashboard.
- [ ] توحيد اسم المتجر: `Miral Store` مقابل `Headless Store`.

---

## 4. سجل التفاصيل الفنية والعمليات المنفذة (Detailed Execution Log)

> كل ملف أنشئ أو عُدّل، ولماذا. رتب زمنياً تقريباً.

### 4.1 الإعداد والبيئة (Setup & Environment)

#### تثبيت المشروع
- `composer create-project laravel/livewire-starter-kit` (أو ما يعادله) لتهيئة مشروع Laravel 13 + Livewire.
- إضافة حزم: Fortify و Socialite و Flux.
- إعداد `docker-compose.yml` (app + MySQL على المنفذ 3307).
- `.env` مُعدّ بـ `DB_DATABASE=headless_store` و `QUEUE_CONNECTION=database` و `CACHE_STORE=database` و `MAIL_MAILER=log` ومتغيرات `SALLA_*`.

#### مشكلة الـ Build مع pnpm → npm (أُصلحت)
- **الحدث:** عند تنفيذ `pnpm run build`، نقل pnpm حزم `node_modules` إلى مجلد `node_modules/.ignored` (تعارض بين pnpm و npm على نفس المشروع).
- **النتيجة:** انكسر `npm run dev`/`build`.
- **الحل المطبق:**
  1. استعادة المحتوى من `node_modules/.ignored` إلى `node_modules`.
  2. إعادة إنشاء مجلدات scoped الفارغة (مثل `@alpinejs`, `@inertiajs`, `@tailwindcss`, `@vitejs`).
  3. التحقق من وجود كل الحزم.
  4. `npm run build` نجح: Vite 8.2.1، 618 module، `✓ built in 11.04s`.
- **الدرس:** استخدام **npm فقط** (تحذير `PLUGIN_TIMINGS` من Rolldown ليس خطأ، لا حاجة لتعطيله).

#### حالة Git
- الفرع: `main`
- تم توثيق وحفظ التعديلات في سلسلة Commits تضم مراحل النشر وإعدادات السيرفر السحابي (Railway / Vercel)، وأمان الاتصال وحل مشكلة المحتوى المختلط (Mixed Content / HTTPS Scheme)، وبناء وتضمين أصول Inertia وVite.
- شجرة العمل نظيفة (`working tree clean`).

### 4.2 تكامل Salla (المراحل 1–7)

| الملف | التعديل/الإنشاء | السبب |
|---|---|---|
| `app/Domains/Shared/DTOs/*` (6 ملفات) | ✅ إنشاء | نقل بيانات Salla ككائنات مصفوفة-تعيينية ثابتة: منتجات، طلبات، عملاء، أقسام، etc. |
| `app/Shared/Salla/SallaAuthenticator.php` | 🔧 إصلاح | إضافة استيراد `App\Domains\Settings\Models\SallaToken` + عمود `access_token_expires_at` لقراءة/كتابة تاريخ انتهاء التوكن بشكل صحيح |
| `database/migrations/*` (جدول `salla_tokens`) | 🔧 تعديل | إضافة عمود `access_token_expires_at` |
| `app/Shared/Salla/Clients/MockSallaClient.php` | ✅ إنشاء | محاكاة كاملة لاستجابات Salla ببيانات عربية (دون أي طلب شبكة) — يُستخدم في الاختبارات والـ seed |
| `app/Shared/Salla/Clients/SallaClient.php` | ✅ إنشاء | العميل الحقيقي عبر `Http::client` مع معالجة OAuth وإعادة المحاولة |
| `app/Shared/Salla/SallaManager.php` | ✅ إنشاء | يختار العميل تلقائياً: `mock` → `http` → `auto` بناءً على `SALLA_DRIVER` |
| `app/Shared/Salla/SallaServiceProvider.php` | ✅ إنشاء | تسجيل `SallaManager` وربط `SallaClientContract` في الحاوية |
| `bootstrap/providers.php` | 🔧 تعديل | تسجيل `SallaServiceProvider` |
| `config/salla.php` | ✅ إنشاء | `driver => env('SALLA_DRIVER','auto')` + إعدادات http/cache/token_storage/webhooks |
| `app/Services/SallaService.php` | 🔧 (قرار) | **بقيت واجهة بلا تغيير** حتى لا تنكسر المتحكمات الحالية — القرار: لا تعديل على واجهات المتحكمات الآن |

### 4.3 نظام الصحة / `/up` (المرحلة 8)

| الملف | النوع | الوصف |
|---|---|---|
| `app/Shared/Health/HealthReport.php` | ✅ إنشاء | هيكل تقرير الحالة (status + checks + timestamp) |
| `app/Shared/Health/HealthCheck.php` | ✅ إنشاء | واجهة فحص (`check(): bool` + `label()`) |
| `app/Shared/Health/HealthServiceProvider.php` | ✅ إنشاء | تسجيل 6 فحوصات (DB, Cache, Queue, Mail, Salla, Config) + تسجيل `HealthController` كـ singleton |
| `app/Shared/Health/Checks/*` | ✅ إنشاء | ملفات الفحوصات الستة |
| `app/Shared/Health/Http/HealthController.php` | ✅ إنشاء | `GET /up` → 200 مع `{"status":"ok"}`، أو 503 مع `{"status":"degraded"}` + التفاصيل عبر `?details=1` |
| `tests/Feature/Health/HealthEndpointTest.php` | ✅ إنشاء | 4 اختبارات: ok، degraded عند فشل فحص، كشف التفاصيل |
| `bootstrap/providers.php` | 🔧 تعديل | تسجيل `HealthServiceProvider` |

### 4.4 تدقيق الاستجابة للموبايل (Responsive Audit & Fix)

> قواعد الالتزام: **عدم تغيير التصميم البصري فوق شاشة sm**، عدم لمس PHP/DB/Routes/Controllers، تكبير النصوص على الهاتف بدرجة طفيفة. الهدف: التوافق مع **Mobile (320–640) / Tablet (768–1024) / Laptop (1280–1440) / Ultra-Wide (1920+)**.

#### أ. واجهة المتجر التفاعلية (Vue/Inertia) — `npm run build` ناجح (Vite 8.2.1, 618 modules)

| الملف | التعديل |
|---|---|
| `resources/js/Components/Header.vue` | إخفاء الوصف الفرعي تحت `sm`، إخفاء أيقونات المفضلة/الطلبات تحت `sm`، زر تسجيل الدخول `whitespace-nowrap` |
| `resources/js/Components/Footer.vue` | `gap-12` → `gap-8 md:gap-12` |
| `resources/js/Pages/Customer/Home.vue` | حشوات البانر، `py-16/py-20` → `py-10 sm:py-16`، إضافة `flex-wrap`، أزرار CTA `min-h-[44px]` |
| `resources/js/Pages/Customer/Product.vue` | breadcrumb `flex-wrap`، فواصل شبكة `grid gap`، صف السعر `flex-wrap`، عينات الألوان `w-9/w-8` → `w-10 h-10` |
| `resources/js/Pages/Customer/Cart.vue` | فواصل `grid gap`، أزرار الكمية `w-7` → `w-9 h-9`، زر الحذف `w-9 h-9` |
| `resources/js/Pages/Customer/Checkout.vue` | فواصل `grid gap`، تسميات خيارات الدفع `flex-wrap` |
| `resources/js/Pages/Customer/Orders.vue` | رابط الطلب `min-h-[44px]` |
| `resources/js/Pages/Customer/OrderDetail.vue` | ترويسة `flex-wrap`، الـ stepper `grid-cols-4` → `grid-cols-2 md:grid-cols-4`، `min-w-0` |
| `resources/js/Pages/Customer/Account.vue` | ترويسة `flex-wrap`، زر الخروج `min-h-[44px]` |
| `resources/js/Pages/Customer/Categories.vue` | `py-16` → `py-10 sm:py-16`، حشوة البطاقة `p-8` → `p-4 sm:p-6 lg:p-8` |
| `resources/js/Pages/Customer/About.vue` | `py-16` → `py-10 sm:py-16`، `h1` استجابي، حشوة البطاقات |
| `resources/js/Pages/Customer/Contact.vue` | عناوين/حشوات استجابية |
| `StoreLayout.vue`, `Shop.vue`, `Wishlist.vue` | لا تغييرات مطلوبة |

#### ب. القوالب الثابتة والإدارية (Blade) — 20 قالباً تُجمَّع بنجاح

| الملف | التعديل |
|---|---|
| `resources/views/components/layouts/app.blade.php` | أيقونات الترويسة `w-9 h-9 sm:w-10 sm:h-10` → `w-10 h-10 min-h-[44px]` (بحث/مفضلة/سلة/طلبات/حساب/زر المنيو)؛ إخفاء المفضلة/الطلبات تحت `sm` (منع تجاوز 320px)؛ إخفاء الوصف الفرعي للشعار تحت `sm`؛ روابط قائمة الهاتف `py-2.5` → `py-3 min-h-[44px]`؛ عناصر dropdown `py-2` → `py-2.5 min-h-[44px]`؛ زر تسجيل الدخول/لوحة الإدارة `min-h-[44px]`؛ الـ toast `fixed bottom-4 inset-x-4` (يمتد كامل العرض على الهاتف بدل إزاحة يسارية) |
| `resources/views/components/layouts/admin.blade.php` | ترويسة: شعار متقلص تحت 420px (`min-[420px]:hidden`)، زر خروج `min-h-[44px]`، `gap` استجابي؛ تبويبات التنقل `py-2.5` → `py-3 min-h-[44px]` + `whitespace-nowrap` + `px-3 sm:px-4` |
| `resources/views/admin/dashboard.blade.php` | عنوان `text-3xl` → `text-2xl sm:text-3xl`، «عرض الكل» `min-h-[44px]`، زر إعدادات الربط `py-2.5` → `py-3 min-h-[44px]` |
| `resources/views/admin/products/index.blade.php` | العنوان `text-3xl` → `text-2xl sm:text-3xl`، أزرار ترويسة `py-2` → `py-3 min-h-[44px]`، أزرار المودالين `py-2` → `py-3 min-h-[44px]`، أزرار الصفوف `py-1` → `py-3 min-h-[44px]`، زر المزامنة `min-h-[44px]`، المودالان: `overflow-y-auto` + `max-h-[90vh]` + `min-h-full flex items-center justify-center p-4` (تمرير عمودي على الهاتف بدل القص) |
| `resources/views/admin/orders/index.blade.php` | العنوان `text-3xl` → `text-2xl sm:text-3xl`، زر العودة `py-2` → `py-3 min-h-[44px]` |
| `resources/views/admin/customers/index.blade.php` | العنوان `text-3xl` → `text-2xl sm:text-3xl`، زر العودة `py-2` → `py-3 min-h-[44px]` |
| `resources/views/admin/settings.blade.php` | العنوان `text-3xl` → `text-2xl sm:text-3xl`، زر العودة `py-2` → `py-3 min-h-[44px]`، زر المزامنة `py-2` → `py-3 min-h-[44px]` |
| `resources/views/static/*.blade.php` (about/contact/faq/shipping/returns/terms/privacy/track-order) | `py-16` → `py-10 sm:py-16`؛ `h1` `text-4xl` → `text-2xl sm:text-3xl lg:text-4xl`؛ حشوة البطاقات `p-8` → `p-6 sm:p-8`؛ زر track-order `min-h-[44px]` |
| `resources/views/auth/*.blade.php` (login/register/forgot-password/reset-password) | `py-14` → `py-10 sm:py-14`؛ البطاقة `p-8` → `p-6 sm:p-8` + `px-4 sm:px-0`؛ أزرار الإرسال `min-h-[44px]` |
| `resources/views/components/otp-modal.blade.php` | أزرار أسفل المودال `py-2 min-h-[44px]` |

- **التحقق:** `npm run build` ✅ + فحص تجميع جميع القوالب العشرين عبر `Blade::compileString` ✅ (الـ `view:cache` الكامل لا يعمل بسبب مكوّن `layouts::auth` المفقود — مشكلة Auth المسبقة). **لا تغيير بصري فوق `sm`.**

### 4.5 الاختبارات (Test Suite)

### 4.5 الاختبارات (Test Suite)

| النطاق | عدد الاختبارات | الحالة |
|---|---|---|
| وحدة Salla (DTOs, Manager, Clients, Authenticator) | ~30 | ✅ خضراء |
| Feature Salla (ServiceProvider) | ~10 | ✅ خضراء |
| Feature Health (نقطة `/up`) | 4 | ✅ خضراء |
| **المجموعة الكاملة** | **105 اختباراً / 83 نجح / 9 فشل** | ⚠️ الفشل كلها Auth مسبقة |

- **إخفاقات Auth المسبقة (9) — غير متعلقة بمشروع Salla:**
  1. مكوّن `layouts::auth` غير موجود (خطأ في قوالب المصادقة).
  2. `Route [dashboard] not defined`.
  3. `UserFactory::withTwoFactor()` غير موجودة.
- **ملاحظة `/up` الحي:** الخادم الفعلي يعيد 503 لأن فحصاً يفشل في بيئة التشغيل الحقيقية (على الأرجح فحص queue/mail بإعدادات env الفعلية). الاختبارات خضراء لأن `phpunit.xml` يستخدم `QUEUE_CONNECTION=sync` و `MAIL_MAILER=array`.

### 4.6 أدوات وأسس عمل المشروع
- اختبارات: PHPUnit فقط (أمر التشغيل: `php artisan test`).
- مبدأ TDD: فشل → تنفيذ → خضراء.
- **قواعد ملزمة:**
  - لا تخمين — اسأل قبل الافتراض.
  - لا حذف ملفات دون إذن صريح.
  - لا تغيير بصري دون إبلاغ.
  - npm فقط (لا pnpm).
  - شرح بالعربية، ورمز بالإنجليزية.

### 4.7 تكامل الإنتاج الكامل لـ Salla (Production-Ready Integration)

> هذه المرحلة أكملت «منطق العمل» بالكامل على طبقات Webhooks + Sync + Checkout، بتقنية TDD (اختبار أولاً). لا حاجة لبيانات اعتماد حية — كل شيء مُختبر عبر `MockSallaClient` و `Http::fake` (قرار المستخدم).

#### خريطة الملفات (Wiring Map)
| الملف | النوع | الدور |
|---|---|---|
| `app/Shared/Salla/Webhooks/SallaWebhookSignatureVerifier.php` | ✅ إنشاء | تحقق HMAC-SHA256 من التوقيع (`X-Salla-Signature`) على **الـ raw body** — يدعم بادئة `sha256=` |
| `app/Shared/Salla/Webhooks/SallaWebhookHandlerInterface.php` | ✅ إنشاء | عقد المعالج: `event`, `supports(event)`, `handle(array $event): void` |
| `app/Shared/Salla/Webhooks/SallaWebhookDispatcher.php` | ✅ إنشاء | يوّجه الحدث للمعالج الصحيح (بالبادئة `product.`/`order.`) عبر الحاوية |
| `app/Shared/Salla/Webhooks/Handlers/ProductWebhookHandler.php` | ✅ إنشاء | أحداث المنتج: created/updated/price.updated/quantity.low/status.updated/image.updated/category/brand/tags → مزامنة كاملة أو جلب مفرد |
| `app/Shared/Salla/Webhooks/Handlers/OrderWebhookHandler.php` | ✅ إنشاء | أحداث الطلب: created/updated/status.updated/cancelled/refunded/payment.updated/deleted → مزامنة أو إلغاء محلي |
| `app/Shared/Salla/Sync/ProductSyncService.php` | ✅ إنشاء | upsert منتج بـ `salla_id`: صور/خيارات/متغيرات (حذف+إعادة إنشاء)، سعر بالوحدات الصغرى، slug، حالة، توفر (نشط+كمية>0) |
| `app/Shared/Salla/Sync/OrderSyncService.php` | ✅ إنشاء | upsert طلب بـ `salla_id` + عناصره + `OrderSnapshot` (فقط عند تغيّر `version_hash`) + تعيين الحالات/الدفع |
| `app/Shared/Salla/Checkout/SallaCheckoutService.php` | ✅ إنشاء | إنشاء جلسة دفع Salla (POST لـ `checkout_base_url`) + سجل محلي في `CheckoutSession` + رابط إعادة توجيه دفاعي |
| `app/Http/Controllers/Salla/SallaWebhookController.php` | ✅ إنشاء | استقبال POST، تحقق التوقيع، تخزين الحدث بـ `event_key = sha256(rawBody)` (منع التكرار)، إطلاق `ProcessSallaWebhook` فقط عند الإضافة الجديدة |
| `app/Jobs/ProcessSallaWebhook.php` | ✅ إنشاء | معالجة الحدث في الخلفية (tries=3, backoff=60) — `processed_at`/`failed_at`/`error_message` |
| `app/Jobs/SyncSallaProducts.php` | ✅ إنشاء | المزامنة الكاملة مع الترقيم (pages) — يعيد إطلاق نفسه حتى آخر صفحة |
| `app/Shared/Salla/SallaServiceProvider.php` | 🔧 تعديل | تسجيل العقد الجديدة (Handlers, Dispatcher, Services, CheckoutService) في الحاوية |
| `routes/web.php` | 🔧 تعديل | `POST /webhooks/salla` → `SallaWebhookController` مع إعفاء CSRF (`withoutMiddleware(ValidateCsrfToken::class)`) |
| `app/Http/Controllers/Storefront/CheckoutController.php` | 🔧 تعديل | دمج `SallaCheckoutService`: للدفع الإلكتروني يُنشئ جلسة Salla ويعيد التوجيه لـ `checkout_url` (مع fallback محلي)، COD كما هو |
| `app/Http/Controllers/Admin/ProductController.php` | 🔧 تعديل | زر المزامنة في لوحة التحكم يعمل الآن عبر `SyncSallaProducts` + `ProductSyncService` (بدل `SallaService` القديم) |

#### تدفقات العمل (Flows)
1. **Webhook (توقيع + معالجة):** Salla → `POST /webhooks/salla` → تحقق HMAC → حفظ `SallaWebhookEvent` (idempotency عبر `event_key`) → `ProcessSallaWebhook` → `SallaWebhookDispatcher` → المعالج المختص → `ProductSyncService`/`OrderSyncService`.
2. **مزامنة المنتجات:** زر لوحة التحكم (أو `SyncSallaProducts`) → `SallaClientContract::getProducts()` صفحات → `ProductSyncService::sync()` upsert كامل (صور/خيارات/متغيرات/سعر/توفر/حالة) → حذف ناعم عند `product.deleted`.
3. **دورة الطلب والدفع:** Checkout المتجر → `CheckoutController` → `SallaCheckoutService` → POST لسلة Salla (Checkout API) → `checkout_url` لإكمال الدفع عند Salla → Webhook `order.*` يزامن الطلب محلياً + `OrderSnapshot` للتاريخ.
4. **الطوارئ/الدفاع:** عدم توفر `checkout_url` أو فشل الاستدعاء → fallback للتدفق المحلي الحالي (جلسة → عرض الطلب).

#### قواعد مزامنة (Sync Rules)
- **المنتج:** upsert بـ `salla_id`؛ الصور/الخيارات/المتغيرات تُستبدل (delete + recreate)؛ `slug = slug(name)-id`؛ الحالة عبر `ProductStatus::fromSalla`؛ الظهور: draft/archived → Hidden؛ `is_available = active && qty > 0`؛ السعر بالوحدات الصغرى.
- **الطلب:** upsert بـ `salla_id`؛ تعيين الحالات (pending/new/on_hold→Pending، processing/confirmed→Processing، shipped→Shipped، delivered→Delivered، completed→Completed، cancelled/failed→Cancelled، refunded/returned→Refunded)؛ الدفع من كتلة `payment` أو من slug الطلب؛ `payment_method` إلى enum `PaymentMethod` مع fallback `Other`؛ إنشاء `OrderSnapshot` **فقط** عند تغيّر `version_hash` (لا تكرار).
- **الـ payload الكامل** (منتج فيه `name`+`price`) يُزامن مباشرة؛ الحدث الجزئي (granular) يستدعي `get("products/{id}")`/`get("orders/{id}")` لجلب السجل الكامل؛ `[]` (فارغ) → يُعامل كـ null؛ `product.deleted` → حذف ناعم بـ `salla_id`؛ `order.deleted` → `OrderStatus::Cancelled` محلياً + `salla_status='deleted'`.

#### متغيرات بيئة جديدة (Launch Checklist)
| المفتاح | القيمة | ملاحظة |
|---|---|---|
| `SALLA_WEBHOOK_SECRET` | سر الويب هوك من Salla Dashboard | يُقرأ عبر `config('salla.webhooks.secret')` — مطلوب للتحقق من التوقيع |
| `SALLA_DRIVER` | `auto`/`mock`/`http` | يضاف إلى `.env.example` |
| `SALLA_CHECKOUT_API_URL` | رابط واجهة Checkout الخاصة بسلة | الأساس لـ `SallaCheckoutService` (قرار نهائي بعد تحقق حي) |

#### ملاحظات اختبارات حاسمة
- اختبارات الويب هوك تضبط `config(['salla.driver' => 'mock'])` + `queue.default=sync` في `setUp()` — وإلا يتجه العميل الحقيقي لشبكة ويفشل بصمت.
- الاختبارات التي تلمس قاعدة البيانات تحتاج `RefreshDatabase` (لا توجد في `tests/TestCase.php`).
- `MockSallaClient::genericResponse` أُصلح: اسم الـ fixture = `$segments[0]` من الـ endpoint (كان يستخدم `orders/1000` كاملاً → صفوف فارغة → `Undefined array key 0`).

### 4.9 جاهزية النشر السحابي (Railway & Vercel) وإصلاحات الاتصال والأصول الثابتة
- **دعم النشر على Railway عبر Nixpacks (`Nixpacks.toml`):**
  - تحديد بيئة التشغيل: `php = "8.3"`, `node = "20"`.
  - مراحل التثبيت والبناء: `composer install --no-dev` و `npm run build`.
  - أمر البدء: `php artisan serve --host=0.0.0.0 --port=${PORT:-8080}`.
- **حل مشكلة المحتوى المختلط (Mixed Content) وفرض HTTPS:**
  - في `AppServiceProvider::boot()`: فرض `URL::forceScheme('https')` للبيئة الإنتاجية `production` مع التحقق من عدم التشغيل عبر الـ Console (`!App::runningInConsole()`).
  - في `bootstrap/app.php`: ضبط `trustProxies(at: '*')` لكافة الترويسات المعيارية (`X-Forwarded-*`) لتمرير بروتوكول HTTPS بشكل سليم خلف البروكسي العكسي لمنصات النشر السحابي.
- **تضمين وبناء أصول الواجهة الأمامية (Inertia/Vite):**
  - تم بناء وتضمين أصول الإنتاج في مجلد `public/build` ومطابقتها مع `manifest.json` لضمان عمل واجهات Vue/Inertia مباشرة على بيئات النشر السحابي دون الحاجة لتشغيل Vite Dev Server.

---

## 5. الوضع الحالي والمهام المتبقية (Current Status & Pending Tasks)

### ✅ منجز (Completed)
- [x] إعداد البيئة: Docker (app:8000، MySQL:3307)، PHP 8.5.8، Node 24، npm 11.6.2.
- [x] إصلاح مشكلة pnpm→npm واستعادة `node_modules`؛ `npm run build` ناجح.
- [x] **المراحل 1–8** لتكامل Salla (DTOs، Authenticator، Mock/Real Client، Manager، ServiceProvider، HealthChecks + `/up`) — كل اختباراتها خضراء.
- [x] اختبار `SallaServiceProviderTest` و `HealthEndpointTest` (4/4) و 44 اختباراً جديداً (Salla + Health) خضراء.
- [x] تحسين استجابة 4 قوالب Blade (لوحة تحكم المتجر) مع الحفاظ على التصميم فوق sm.
- [x] **تدقيق الاستجابة الشامل (القسم 4.4):** واجهة Vue الكاملة (13 ملفاً) + 20 قالب Blade — أهداف لمس 44px، منع تجاوز 320px، تمرير المودالات، عناوين استجابية. `npm run build` ✅ + فحص التجميع ✅.
- [x] إنشاء `PROJECT_BRAIN.md` (هذا الملف) كمرجع أساسي وتحديثه دورياً.
- [x] **تكامل الإنتاج الكامل (القسم 4.7):** Webhook Signature Verification + Handlers (منتجات/طلبات) + ProductSyncService + OrderSyncService + SallaCheckoutService + WebhookController (idempotency) + Jobs + دمج CheckoutController و Admin ProductController + إعفاء CSRF لمسار الويب هوك.
- [x] **المجموعة الكاملة بعد التكامل:** 160 اختباراً / 138 نجح / 9 فشل (Auth مسبقة فقط) / 440 assertion.
- [x] إنشاء `SALLA_SETUP_GUIDE.md` — دليل الربط التشغيلي العربي الكامل (بوابة Partners، scopes، env vars، webhooks، أحداثه الـ14، مزامنة، دورة الطلب، checklist، استكشاف الأخطاء) — موثّق من الكود الفعلي.
- [x] **تجهيز وإصلاحات النشر السحابي (Railway / Vercel):** إعداد `Nixpacks.toml`، ضبط البروكسيات الموثوقة `trustProxies`، فرض HTTPS لتفادي Mixed Content، وتضمين أصول الإنتاج المبنية لـ Inertia/Vite.
- [x] **المرحلة 10 وأدوات سطر الأوامر (CLI & Handover Readiness):**
  - إنشاء أمر Artisan المخصص `php artisan salla:sync` (`app/Console/Commands/SallaSyncCommand.php`) لدعم المزامنة الشاملة للمنتجات والطلبات سحابياً ومحلياً مع شريط تقدم وتقارير.
  - تنظيف وتوحيد ملف `.env.example` وفق معايير الأمان الموصى بها.
  - استبدال `README.md` بالكامل بدليل تشغيلي وهندسي احترافي ثنائي اللغة خاص بـ **متجر ميرال (Miral Store)**.
  - ربط `SallaService` مع جدول المنتجات المحلي في قاعدة البيانات (`fetchProductsFromDatabase`) مع تراجع تلقائي ذكي.

### ⏳ جارٍ / خطوة تالية (Next Step)
- [ ] إجراء فحص البناء النهائي للأصول `npm run build` وتأكيد سلامة ملفات المشروع للتسليم المباشر.

### ⬜ معلّق / مهام اختيارية (Optional / Backlog)
- [ ] حسم مصير `resources/views/components/navbar.blade.php` + `footer.blade.php` (قالب «رافال» القديم غير المستخدم — **لا تحذف دون إذن**).
- [ ] إصلاح إخفاقات Auth التسعة المسبقة (إنشاء مكوّن `layouts::auth`، تعريف مسار `dashboard`، إضافة `withTwoFactor()` إلى `UserFactory`).
- [ ] التحقق من سبب عودة `/up` بـ 503 في env الفعلية (فحص queue/mail) وتعديل إعدادات التشغيل الحقيقية.
- [ ] توضيح الغرض من `oneday/` و `seeders/` (فارغان) — سؤال المستخدم.
- [ ] **التحقق الحي (بيانات اعتماد فعلية من Salla):** استجابة Checkout API الحقيقية + توقيع webhook حقيقي من Salla Dashboard — يُستكمل بعد توفير `SALLA_WEBHOOK_SECRET` و `SALLA_CHECKOUT_API_URL`.

### 🔑 مفاتيح المراجع
- واجهة المتجر الفعلية: `resources/js/Pages/Customer/*.vue` + `resources/js/Layouts/StoreLayout.vue` (Vue/Inertia — خارج نطاق تدقيق Blade).
- سجل الجلسات: `session_summary.md` (الجلسة الأخيرة `ses_ffe369346ffeErRjlA253qxg7j`).