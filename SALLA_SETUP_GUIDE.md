# 🔧 دليل الربط التشغيلي مع منصة سلة (Salla Setup Guide)

> هذا الدليل يشرح خطوة بخطوة كيفية ربط موقعك ببيئة **سلة** الحقيقية (Production / Sandbox) وتفعيل المزامنة المباشرة للمنتجات والطلبات.
> تمت كتابته بناءً على الكود الفعلي الموجود في المشروع (مجلد `app/Shared/Salla/`).

---

## 🧭 نظرة عامة على المعمارية

| الطبقة | الملف | الوظيفة |
|---|---|---|
| العميل (Client) | `app/Shared/Salla/SallaClient.php` | التواصل الحقيقي مع Merchant API عبر `Http` |
| المحاكاة | `app/Shared/Salla/MockSallaClient.php` | بيانات وهمية بدون شبكة (للتطوير والاختبار) |
| المُوجّه | `app/Shared/Salla/SallaManager.php` | يختار العميل تلقائياً حسب `SALLA_DRIVER` |
| المصادقة | `app/Shared/Salla/SallaAuthenticator.php` | تبادل/تحديث توكن OAuth 2.0 |
| استقبال Webhooks | `app/Http/Controllers/Salla/SallaWebhookController.php` | `POST /webhooks/salla` + التحقق من التوقيع |
| مزامنة المنتجات | `app/Shared/Salla/Sync/ProductSyncService.php` + `app/Jobs/SyncSallaProducts.php` | جلب وتحديث المنتجات محلياً |
| مزامنة الطلبات | `app/Shared/Salla/Sync/OrderSyncService.php` | تحديث الطلبات والحالات محلياً |
| الدفع عند سلة | `app/Shared/Salla/Checkout/SallaCheckoutService.php` | إنشاء جلسة Checkout عند سلة |

---

## 1️⃣ الاشتراك وإعداد تطبيق سلة (Salla Partners Setup)

### 1.1 إنشاء حساب في منصة صانعي التطبيقات

1. افتح منصة الشركاء: **https://portal.salla.partners/**
2. أنشئ حساباً جديداً (أو سجّل دخولك إذا كان لديك حساب).
3. أكمل بيانات التسجيل والتحقق من الهوية (اسم المطوّر، البريد، إلخ).

### 1.2 إنشاء تطبيق جديد (Merchant / Headless App)

1. من لوحة الشركاء انتقل إلى **My Apps → Create New App**.
2. حدد نوع التطبيق. لهذا المشروع نختار **Merchant App / Private App** (تطبيق خاص بمتجرك) أو **Headless App** حسب احتياجك:
   - **Private App:** مثالي لمتجر واحد يملكه التاجر نفسه.
   - **Public App:** إذا كنت تنوي نشر التطبيق في متجر تطبيقات سلة.
3. املأ بيانات التطبيق (الاسم، الوصف، الشعار، رابط المتجر).

### 1.3 تحديد الصلاحيات المطلوبة (Scopes)

من صفحة التطبيق اختر **App Permissions / Scopes**، وفعّل الآتي **كحد أدنى**:

| الصلاحية | الوصف | يُستخدم في |
|---|---|---|
| `products` (read) | قراءة المنتجات | المزامنة الأولى + Webhooks |
| `orders` (read) | قراءة الطلبات | مزامنة الطلبات + حالات الدفع |
| `customers` (read) | قراءة العملاء | (اختياري — لمراحل لاحقة) |
| `webhooks` (write) | تسجيل وإدارة Webhooks | تسجيل الأحداث تلقائياً |

> ⚠️ لا تمنح صلاحيات `write` للمنتجات/الطلبات إلا إذا كانت لديك ميزة تُعدّل البيانات في سلة (المشروع الحالي يقرأ فقط).

### 1.4 ضبط روابط العودة والـ Webhooks

| الإعداد | القيمة المطلوبة |
|---|---|
| **Redirect / Callback URI (OAuth)** | `https://yourdomain.com/salla/callback` |
| **Webhook URL** | `https://yourdomain.com/webhooks/salla` |

> 💡 في بيئة التطوير المحلي يمكنك استخدام خدمة `ngrok` لتوفير `https://yourdomain.com` الوهمي:
> ```bash
> ngrok http 8000
> ```

---

## 2️⃣ ضبط متغيّرات البيئة في الموقع (Environment Configuration)

انقل القيم التالية من لوحة سلة إلى ملف `.env` (محلياً وعلى السيرفر):

```env
# ── سلة Salla ──────────────────────────────────────────────
SALLA_DRIVER=auto                      # auto | http | mock
SALLA_CLIENT_ID=your_client_id         # من لوحة الشركاء (App → Credentials)
SALLA_CLIENT_SECRET=your_client_secret # من لوحة الشركاء
SALLA_MERCHANT_ID=your_merchant_id     # معرف متجرك عند سلة
SALLA_REDIRECT_URI=https://yourdomain.com/salla/callback
SALLA_WEBHOOK_SECRET=your_webhook_secret

# اختياري — تغيير نقاط النهاية (تستخدم القيم الافتراضية إن لم توجد)
SALLA_API_URL=https://api.salla.dev/admin/v2
SALLA_CHECKOUT_API_URL=https://api.salla.dev/store/v2/checkout
SALLA_OAUTH_URL=https://accounts.salla.sa/oauth2/token
```

### شرح كل متغيّر

| المتغيّر | القراءة عبر | الغرض |
|---|---|---|
| `SALLA_DRIVER` | `config('salla.driver')` | `auto`: يختار العميل الحقيقي إذا وُجدت المفاتيح وإلا المحاكاة. `http`: عميل حقيقي دائماً. `mock`: محاكاة دائماً |
| `SALLA_CLIENT_ID` | `config('salla.client_id')` | معرف تطبيق OAuth من لوحة الشركاء |
| `SALLA_CLIENT_SECRET` | `config('salla.client_secret')` | السرّ المرافق للتطبيق |
| `SALLA_MERCHANT_ID` | `config('salla.merchant_id')` | يُستخدم لربط سجل التوكن في جدول `salla_tokens` |
| `SALLA_REDIRECT_URI` | `config('salla.redirect_uri')` | يجب أن يطابق Callback URI المسجّل في لوحة الشركاء |
| `SALLA_WEBHOOK_SECRET` | `config('salla.webhooks.secret')` | يُمرَّر إلى `SallaWebhookSignatureVerifier` للتحقق من التوقيع |

### بعد تعديل الملف

```bash
php artisan config:clear
php artisan config:cache   # في بيئة الإنتاج فقط
php artisan cache:clear
```

### التحقق من اختيار العميل

شغّل الأمر التالي لترى أي عميل سيُستخدم (حقيقي أم محاكاة):

```bash
php artisan tinker
```

```php
app(\App\Shared\Contracts\SallaClientContract::class)::class;
// الناتج: App\Shared\Salla\SallaClient     ← حقيقي (http)
// أو:     App\Shared\Salla\MockSallaClient ← محاكاة
```

---

## 3️⃣ ربط الـ Webhooks واستقبال التحديثات (Webhooks Activation)

### 3.1 رابط الـ Webhook الخاص بالموقع

المسار المسجّل في المشروع هو:

```
POST https://yourdomain.com/webhooks/salla
```

- المعرّف: `salla.webhook`
- **معفى من CSRF** (المصادقة تتم عبر التوقيع الإلكتروني، وليس عبر الجلسة).
- يعيد `{"status":"accepted"}` عند القبول، و `401` عند توقيع غير صحيح.

### 3.2 الأحداث (Events) الواجب تفعيلها في لوحة سلة

من لوحة الشركاء → **App → Webhooks → Events**، فعّل الأحداث التالية:

**المنتجات:**
- `product.created`
- `product.updated`
- `product.deleted`
- `product.price.updated`
- `product.quantity.low`
- `product.status.updated`
- `product.image.updated`

**الطلبات:**
- `order.created`
- `order.updated`
- `order.status.updated`
- `order.cancelled`
- `order.refunded`
- `order.payment.updated`
- `order.deleted`

> ✅ المعالجة تدعم جميع الأحداث المذكورة عبر `SallaWebhookDispatcher` (البادئات `product.*` و `order.*`). أي حدث غير معروف يُتجاهل بأمان ويسجَّل في اللوغ.

### 3.3 الحصول على Webhook Secret وربطه بالتحقق من التوقيع

1. في لوحة الشركاء → **App → Webhooks**، فعّل خيار **Signature** كاستراتيجية أمان.
2. انسخ **Webhook Secret** المعروض.
3. ضعه في `.env`:
   ```env
   SALLA_WEBHOOK_SECRET=copy_the_secret_here
   ```
4. يُقرأ تلقائياً في `SallaServiceProvider` ويمرَّر إلى `SallaWebhookSignatureVerifier`:
   - الخوارزمية: **HMAC-SHA256** على **الـ raw body** (وليس الـ JSON المُعاد بناؤه).
   - الرأس: `X-Salla-Signature`.
   - المقارنة بـ `hash_equals` (timing-safe).

### 3.4 اختبار الـ Webhook محلياً (اختياري)

شغّل الخادم ثم أرسل طلباً بمفتاح سري معلوم:

```bash
SECRET="my-test-secret"
SIG=$(printf '%s' '{"event":"product.updated","data":{"id":1}}' | openssl dgst -sha256 -hmac "$SECRET" -hex | awk '{print $2}')
curl -X POST https://yourdomain.com/webhooks/salla \
  -H "Content-Type: application/json" \
  -H "X-Salla-Signature: $SIG" \
  -d '{"event":"product.updated","data":{"id":1}}'
# التوقع: {"status":"accepted"}
```

> ℹ️ إذا كان السجل `SallaWebhookEvent` يحمل نفس `event_key` (sha256 لـ raw body) فسيُتجاهل الطلب المكرر (idempotency).

---

## 4️⃣ تشغيل المزامنة الأولى للمنتجات (Initial Products Sync)

### 4.1 من لوحة التحكم (الأسهل)

1. سجّل دخولك في لوحة تحكم الموقع: `/admin`
2. انتقل إلى **إدارة المنتجات** → زر **«🔄 مزامنة المخزون من سلة»**.
3. الزر يستدعي `POST /admin/sync/run` الذي يشغّل `SyncSallaProducts` عبر `ProductSyncService`.

### 4.2 عبر Artisan / Tinker (لمزامنة كبيرة من السيرفر)

شغّل الـ Job مباشرة (متزامن) عبر Tinker:

```bash
php artisan tinker
```

```php
SyncSallaProducts::dispatchSync();   // تنفيذ فوري، يمرّ على كل الصفحات تلقائياً
```

أو عبر `dispatch` إذا كان لديك `queue:work` يعمل:

```bash
php artisan queue:work --stop-when-empty
```

> ℹ️ الـ Job يجلب المنتجات **صفحة بصفحة** (`per_page=50`) ويعيد إطلاق نفسه تلقائياً حتى آخر صفحة.

### 4.3 التحقق من إدراج البيانات محلياً

بعد المزامنة تأكد من أن كل عنصر موجود في قاعدة البيانات:

```bash
php artisan tinker
```

```php
// عدد المنتجات
\App\Domains\Catalog\Models\Product::count();

// منتج محدد مع متغيراته وخياراته وصوره
$p = \App\Domains\Catalog\Models\Product::first();
$p->variants;  // المتغيرات (مثل: مقاسات/ألوان)
$p->options;   // الخيارات
$p->images;    // الصور

// هل الأسعار بالوحدات الصغرى صحيحة؟ (مثال: 34900 = 349.00 ر.س)
$p->price;
```

ما يُدرج عند المزامنة:
- ✅ الاسم، الوصف، الكمية (المخزون)، القسم، العلامة
- ✅ **المتغيرات (Variants)** — تُحذف وتُعاد إنشاؤها مع كل مزامنة
- ✅ **الخيارات (Options)** — نفس السلوك
- ✅ **الصور (Images)** — تُستبدل بالكامل
- ✅ **الأسعار** بالوحدات الصغرى + سعر التخفيض
- ✅ **الحالة** (`ProductStatus::fromSalla`) و **الظهور** (draft/archived → Hidden)
- ✅ **التوفر** (`is_available = active && quantity > 0`)

---

## 5️⃣ اختبار دورة الطلب والدفع (End-to-End Testing Flow)

> الموقع يتكامل مع **Salla Hosted Checkout**: العميل يُكمل الشراء في صفحة سلة، ثم يعود الموقع ويحدّث الطلب تلقائياً عبر Webhooks.

### 5.1 شروط مسبقة

- `SALLA_DRIVER=http` أو `auto` مع مفاتيح صحيحة.
- `SALLA_CHECKOUT_API_URL` مضبوط (الافتراضي `https://api.salla.dev/store/v2/checkout`).
- Webhooks مفعّلة كما في القسم 3.
- مشغّل الطابور يعمل (لأن `ProcessSallaWebhook` هو Job).

### 5.2 خطوات الاختبار

1. **أضف منتجاً إلى السلة** عبر واجهة المتجر: `GET /shop/{id}` ثم إضافة للسلة.
2. **افتح صفحة الدفع**: `GET /checkout`.
3. **اختر طريقة دفع إلكترونية** (أحد القيم: `mada`، `cc`، `credit_card`، `stc_pay`، `apple_pay`) ثم أرسل النموذج `POST /checkout`.
4. `CheckoutController::placeOrder` سيقوم بـ:
   - حفظ لقطة الطلب في الجلسة (كما هو الحال حالياً).
   - إنشاء جلسة `CheckoutSession` محلياً.
   - استدعاء `SallaCheckoutService::createSession` الذي يُنشئ سلة عند سلة عبر `POST` إلى `checkout_base_url`.
5. **إذا نجح الإنشاء** (`status == 'created'` مع `checkout_url`) يتم إعادة توجيه العميل (301) إلى صفحة سلة لإكمال الدفع.
6. **أكمل الدفع** في بيئة سلة (وضع الاختبار / Sandbox).
7. **تحقق من وصول Webhook**: عند نجاح الدفع ترسل سلة حدث `order.payment.updated` (ثم `order.status.updated`) إلى `/webhooks/salla`.
8. **تحقق من تحديث الطلب محلياً**: `OrderWebhookHandler` يجرّب جلب الطلب الكامل من سلة ويحدّثه عبر `OrderSyncService`، ويُنشئ `OrderSnapshot` عند تغيّر الحالة/البيانات.

### 5.3 التحقق من النتيجة

```bash
php artisan tinker
```

```php
\App\Domains\Webhook\Models\SallaWebhookEvent::latest()->first();
// → event_name: order.payment.updated , processed_at: 2026-08-20 10:30:00

\App\Domains\Commerce\Models\Order::whereNotNull('salla_id')->latest()->first();
// → الطلب موجود بمطابقة salla_id، و local_status محدّثة
```

### 5.4 حالات الطوارئ (Fallback)

- إذا فشل الاتصال بسلة (أو لم تُرجع `checkout_url`) → **يعود الموقع للتدفق المحلي الحالي** (يعرض الطلب في المتجر مباشرة، وسيلة الدفع تحفظ في الجلسة).
- الدفع **عند الاستلام (COD)** لا يمر عبر سلة إطلاقاً (يبقى محلياً).

---

## 6️⃣ قائمة التحقق قبل الإطلاق الحي (Checklist Before Launch)

### 🔐 الأمان والمصادقة

- [ ] `SALLA_CLIENT_ID` و `SALLA_CLIENT_SECRET` من لوحة الشركاء (وليس نسخاً تجريبية).
- [ ] `SALLA_REDIRECT_URI` يطابق تماماً Callback URI المسجّل في سلة (تطابق حرفي).
- [ ] `SALLA_WEBHOOK_SECRET` مُضبوط ومطابق لاستراتيجية **Signature** في لوحة سلة.
- [ ] `SALLA_MERCHANT_ID` مضبوط (يُربط به سجل التوكن في `salla_tokens`).
- [ ] القيم في `.env` على السيرفر ولا توجد في أي ملف مُتتبَّع في Git (`.env.example` فارغ إلا من عناوين توضيحية).

### 🚀 التطبيق والتشغيل

- [ ] `SALLA_DRIVER=auto` (أو `http`) — وليس `mock` في الإنتاج.
- [ ] مشغّل الطابور (Queue Worker) يعمل: `php artisan queue:work` (أو Supervisor) — مطلوب لـ `ProcessSallaWebhook` و `SyncSallaProducts`.
- [ ] `php artisan config:cache` و `route:cache` منفّذان بعد تغيير الإعدادات.
- [ ] قاعدة بيانات `salla_tokens` جاهزة (migration منفّذة) و `SallaToken` مُخزَّن بعد أول OAuth.
- [ ] نقطة `/up` تعيد `200` مع `{"status":"ok"}` (نظام Health Checks).

### 🔔 الـ Webhooks

- [ ] `POST /webhooks/salla` يعيد `{"status":"accepted"}` عند اختباره بتوقيع صحيح.
- [ ] الأحداث الأربعة عشر (قسم 3.2) مفعّلة في لوحة الشركاء.
- [ ] حدث تجريبي (`product.updated`) وصل وأُدرج `SallaWebhookEvent` جديد.

### 📦 المزامنة

- [ ] المزامنة الأولى تمت والمنتجات موجودة محلياً مع المتغيرات/الخيارات/الصور/الأسعار.
- [ ] تعديل منتج في سلة انعكس على الموقع خلال ثوانٍ (عبر `product.updated`).
- [ ] حذف منتج في سلة ظهر كحذف ناعم محلياً (`product.deleted`).

### 💳 الدفع والطلبات

- [ ] طلب بدفع إلكتروني أُعيد توجيهه إلى صفحة سلة (Hosted Checkout).
- [ ] بعد إتمام الدفع وصل `order.payment.updated` وحدّث الحالة محلياً.
- [ ] `OrderSnapshot` يُنشأ فقط عند تغيّر الحالة (لا تكرار).
- [ ] طلب COD ما زال يعمل محلياً دون المرور عبر سلة.
- [ ] إلغاء طلب في سلة ظهر كمُلغى محلياً (`order.cancelled` / `order.deleted`).

### 🧪 اختبارات أتمتة (قبل النشر)

- [ ] `php artisan test --testsuite=Unit --filter="Salla"` → أخضر بالكامل.
- [ ] `php artisan test tests/Feature/Salla` → أخضر.
- [ ] `php artisan test` → لا توجد إخفاقات جديدة (الإخفاقات التسع الحالية Auth مسبقة وليست متعلقة بسلة).

---

## 🛟 استكشاف الأخطاء الشائعة

| المشكلة | السبب المحتمل | الحل |
|---|---|---|
| `401 Invalid signature` | `SALLA_WEBHOOK_SECRET` غير مطابق، أو التوقيع حُسب على body معدّل | تأكد من التطابق الحرفي للسر واستخدام raw body |
| العميل يعود `MockSallaClient` رغم ضبط المفاتيح | `SALLA_DRIVER=mock` أو المفاتيح فارغة | اضبط `auto`/`http` وتأكد من `client_id` و `client_secret` |
| `SallaToken` غير موجود عند `getAccessToken()` | لم يكتمل تدفق OAuth (`exchangeCode`) أو `SALLA_MERCHANT_ID` خاطئ | أكمل التثبيت عبر رابط OAuth وتأكد من `merchant_id` |
| المنتجات لا تظهر بعد المزامنة | الطابور لا يعمل، أو `SALLA_DRIVER=mock` | شغّل `queue:work` واضبط الـ driver |
| `order.*` تصل لكن لا تحديث محلياً | الطلب يُجلب بالكامل من سلة (`get('orders/{id}')`) و `[]` يُعتبر null | تأكد من أن صلاحية `orders.read` مفعّلة، وأن معرف الطلب موجود في الـ payload |

---

*آخر تحديث: أغسطس 2026 — طابق هذا الدليل تنفيذ `app/Shared/Salla/*` و `app/Http/Controllers/Salla/*` الحالي.*