# متجر ميرال للمجوهرات الفاخرة | Miral Store 💎

<div align="center">

![Miral Store Banner](https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=1200&h=400&q=80)

**متجر إلكتروني فاخر مبني بمعمارية Headless فائق السرعة مع تكامل كامل ومباشر مع منصة سلة (Salla)**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-Inertia-9553E9?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Salla API](https://img.shields.io/badge/Salla_API-v2_Ready-004D5A?style=for-the-badge)](https://salla.dev)

</div>

---

## 🌟 نظرة عامة على المشروع (Overview)

**متجر ميرال (Miral Store)** هو تطبيق تجارة إلكترونية متقدم يعتمد على معمارية **Headless Commerce** المتطورة. تم تصميمه لتقديم تجربة تسوق راقية وسريعة الاستجابة لمنتجات المجوهرات والهدايا الفاخرة، مع ربط خلفي شامل مع منصة **سلة (Salla API)** لإدارة المخزون، الأسعار، المنتجات، والطلبات بسلاسة وأمان.

---

## 🚀 المميزات الرئيسية (Key Features)

### 💎 واجهة المتجر وتجربة العميل (Storefront)
* **تصميم فاخر وعصري (Rich Luxury Aesthetics):** واجهات أنيقة مخصصة لمنتجات الحلي والمجوهرات مع تناسق لوني وهوية بصرية موحدة.
* **تجاوب كامل 100% (Fully Responsive):** تجربة استخدام مثالية على الهواتف الذكية والأجهزة اللوحية والشاشات الكبيرة مع دعم كامل للغة العربية (RTL).
* **معمارية Inertia + Vue 3:** تصفح فائق السرعة بدون إعادة تحميل الصفحات (Single Page Application feel).
* **سلة المشتريات وخيارات الهدايا:** سلة تسوق ذكية مع إمكانية إضافة بطاقات ورسائل إهداء فاخرة لكل منتج.
* **مسار دفع مرن (Checkout):** دعم الدفع الإلكتروني عبر بوابة سلة (Salla Hosted Checkout) والدفع عند الاستلام (COD).

### 🏪 الربط والتكامل مع سلة (Salla Integration)
* **المزامنة الشاملة للكتالوج (Catalog Sync):** مزامنة المنتجات، الصور، الخيارات (الألوان والمقاسات)، التصنيفات، والماركات.
* **المزامنة الفورية عبر الويب هوك (Real-Time Webhooks):** استقبال ومعالجة 14 حدثاً من سلة مع التحقق الأمني من التوقيع الرقمي (`HMAC-SHA256`).
* **التراجع الذكي (Intelligent Fallback Driver):** دعم أوضاع التشغيل (`auto`، `http`، `mock`) لضمان عدم توقف المتجر حتى أثناء الصيانة.
* **أوامر سطر الأوامر (Artisan CLI):** أمر مخصص `php artisan salla:sync` لجدولة المزامنة التلقائية (Cron job) أو المزامنة اليدوية.

### 🛡️ لوحة التحكم والإدارة (Admin Dashboard)
* إدارة المنتجات وحالة المزامنة مع سلة لحظياً.
* زر **«مزامنة الآن»** الفوري لجلب أحدث التحديثات من سلة بنقرة واحدة.
* متابعة وتحديث حالات الطلبات وسجلات العملاء.
* شاشة إعدادات المتجر وخيارات الشحن والتوصيل.

---

## 🛠️ المعمارية التقنية (Tech Stack)

| المكوّن | التقنية المستخدمة | الوصف |
|---|---|---|
| **الواجهة الخلفية (Backend)** | PHP 8.3+ / Laravel 11.x | Domain-Driven Architecture, Service Providers, Queues |
| **الواجهة الأمامية (Frontend)** | Vue 3 + Inertia.js | تفاعلية كاملة مع بنية SPA |
| **التصميم والأنماط (Styling)** | Tailwind CSS | أنماط مخصصة، دعم RTL، واستجابة متقدمة |
| **قواعد البيانات (Database)** | MySQL 8.0+ / MariaDB | إدارة متقدمة للكتالوج والطلبات والمستخدمين |
| **محرك النشر السحابي (Cloud)** | Docker / Nixpacks / Railway | جاهز للتشغيل والإنتاج مع دعم HTTPS التلقائي |

---

## ⚡ التشغيل السريع (Local Development Setup)

### المتطلبات الأساسية
* PHP >= 8.3
* Composer
* Node.js >= 20.x & npm
* MySQL أو Docker

### خطوات التشغيل
1. **استنساخ المستودع:**
   ```bash
   git clone <repository-url>
   cd my-salla-store
   ```

2. **تثبيت الاعتماديات:**
   ```bash
   composer install
   npm install
   ```

3. **إعداد ملف البيئة:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **تشغيل المهاجرات وبناء الأصول:**
   ```bash
   php artisan migrate
   npm run build
   ```

5. **بدء خادم التطوير:**
   ```bash
   php artisan serve
   ```
   > المتجر متاح الآن عبر: `http://localhost:8000`

---

## 🔗 خطوات تفعيل الربط مع منصة سلة (Salla Setup)

1. **إنشاء تطبيق في بوابة شركاء سلة (Salla Partners Portal):**
   * سجل الدخول إلى [Salla Partners Portal](https://salla.partners).
   * أنشئ تطبيقاً جديداً من نوع **Merchant App** أو **Custom App**.
   * عيّن رابط الـ Callback إلى: `https://your-domain.com/admin/integrations/salla/callback`.

2. **إضافة المفاتيح إلى ملف `.env`:**
   ```env
   SALLA_DRIVER=auto
   SALLA_CLIENT_ID=your_client_id_here
   SALLA_CLIENT_SECRET=your_client_secret_here
   SALLA_WEBHOOK_SECRET=your_webhook_secret_here
   ```

3. **تفعيل الويب هوك (Webhooks):**
   * عيّن رابط استقبال الويب هوك في بوابة سلة إلى:
     `https://your-domain.com/webhooks/salla`
   * فعّل أحداث المنتجات والطلبات (`product.*`, `order.*`).

4. **تشغيل أول مزامنة:**
   ```bash
   php artisan salla:sync --type=all
   ```

> 📖 **للاطلاع على الدليل التشغيلي التفصيلي خطوة بخطوة:** يرجى مراجعة [SALLA_SETUP_GUIDE.md](SALLA_SETUP_GUIDE.md).

---

## ☁️ النشر السحابي على Railway و Vercel (Deployment)

المشروع مهيأ ومضبوط بالكامل للنشر بنقرة واحدة عبر **Railway**:
* ملف `Nixpacks.toml` يحدد بيئة التشغيل تلقائياً (PHP 8.3 + Node 20).
* تم ضبط معالجة البروكسي العكسي (`trustProxies('*')`) لفرض بروتوكول **HTTPS** الآمن ومنع أخطاء المحتوى المختلط (Mixed Content).
* أصول الإنتاج مبنية مسبقاً في `public/build` لضمان أقصى سرعة تحميل.

---

## ⚙️ أوامر الصيانة المفيدة (Artisan Commands)

```bash
# مزامنة المنتجات والطلبات مع سلة
php artisan salla:sync --type=all

# مزامنة المنتجات فقط
php artisan salla:sync --type=products

# فحص جاهزية وصحة النظام
curl http://localhost:8000/up

# مسح وإعادة بناء الكاش
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📁 هيكلية المشروع الأساسية

```
my-salla-store/
├── app/
│   ├── Console/Commands/       # أوامر Artisan (SallaSyncCommand)
│   ├── Domains/                # نماذج النطاق والبيانات (Catalog, Commerce, Shared DTOs)
│   ├── Http/Controllers/       # وحدات التحكم (Storefront, Admin, Salla Webhooks)
│   ├── Jobs/                   # مهام الخلفية والمزامنة غير المتزامنة
│   ├── Services/               # SallaService الموحد لواجهة المتجر
│   └── Shared/Salla/           # طبقة الربط مع سلة (Auth, Client, Sync, Checkout, Webhooks)
├── resources/
│   ├── js/Pages/Customer/      # واجهات المتجر للعملاء (Vue 3 / Inertia)
│   └── views/admin/            # لوحة تحكم الإدارة (Blade / Tailwind)
├── routes/
│   └── web.php                 # مسارات المتجر والإدارة ومستقبل الويب هوك
├── SALLA_SETUP_GUIDE.md        # الدليل التشغيلي الشامل للربط مع سلة
└── PROJECT_BRAIN.md            # المرجع الهندسي لذاكرة المشروع
```

---

## 📄 الترخيص والدعم (License & Handover)

تم تطوير هذا المشروع خصيصاً لـ **متجر ميرال (Miral Store)** بجودة برمجية فائقة، وهو جاهز تماماً للتسليم والتشغيل المباشر في بيئة الإنتاج.
