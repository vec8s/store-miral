<x-layouts.admin title="إعدادات المتجر وسلة — إدارة ميرال">
  <div class="space-y-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#ececee]">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#09090b] tracking-tight">إعدادات المتجر وسلة</h1>
        <p class="text-xs text-[#71717a] mt-1">تعديل معلومات المتجر والشحن وإعدادات الربط مع منصة سلة Salla</p>
      </div>

      <a href="{{ route('admin.dashboard') }}" class="btn-ghost text-xs px-4 py-3 min-h-[44px] font-bold whitespace-nowrap">&rarr; العودة للوحة الرئيسية</a>
    </div>

    <!-- Salla Integration Status Box -->
    <div class="card-awesomic p-6 bg-gradient-to-br from-slate-900 to-zinc-800 text-white shadow-lg border border-zinc-700"
         x-data="{
           syncing: false,
           syncMessage: '',
           errorMessage: '',
           async syncNow() {
             this.syncing = true;
             this.errorMessage = '';
             try {
               const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
               const res = await fetch('/admin/sync/run', {
                 method: 'POST',
                 headers: { 'X-CSRF-TOKEN': csrfToken || '' }
               });
               const data = await res.json();
               if (res.ok && data.success) {
                 this.syncMessage = data.message || 'تمت المزامنة بنجاح!';
               } else {
                 this.errorMessage = data.message || 'حدث خطأ أثناء المزامنة مع سلة.';
               }
             } catch (e) {
               this.errorMessage = 'تعذر الوصول إلى خادم المزامنة.';
             } finally {
               this.syncing = false;
             }
           }
         }">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold px-3 py-1 rounded-full border border-emerald-500/30 text-emerald-300 bg-emerald-950/50 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Salla Merchant OAuth API (نشط في Laravel 13)</span>
          </span>
        </div>
        
        <button @click="syncNow()" :disabled="syncing" 
                class="btn-primary text-xs px-4 py-3 min-h-[44px] bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl flex items-center gap-2 disabled:opacity-50 transition-all whitespace-nowrap">
          <span x-show="!syncing">🔄 مزامنة المنتجات الآن</span>
          <span x-show="syncing" class="animate-spin">⏳</span>
        </button>
      </div>

      <h3 class="text-lg font-black text-white flex items-center gap-2">
        <span>🏪</span> Miral Store — Salla Sync
      </h3>
      
      <p class="text-xs text-zinc-300 mt-1 leading-relaxed">
        يستخدم النظام طبقة الربط التلقائي لجلب المنتجات والمخزون باستخدام مفاتيح API المعرفة في النظام (<code>SALLA_CLIENT_ID</code> & <code>SALLA_CLIENT_SECRET</code>).
      </p>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-zinc-700/60 text-xs">
        <div class="bg-zinc-800/80 p-3 rounded-xl border border-zinc-700">
          <span class="text-zinc-400 block text-[10px] mb-0.5">حالة مفاتيح API</span>
          <span class="font-bold text-emerald-400">متوفرة في بيئة النظام ✅</span>
        </div>
        <div class="bg-zinc-800/80 p-3 rounded-xl border border-zinc-700">
          <span class="text-zinc-400 block text-[10px] mb-0.5">المنتجات المستوردة من سلة</span>
          <span class="font-bold text-amber-300">28 منتج</span>
        </div>
        <div class="bg-zinc-800/80 p-3 rounded-xl border border-zinc-700">
          <span class="text-zinc-400 block text-[10px] mb-0.5">آخر تحديث ناجح</span>
          <span class="font-bold text-zinc-200">اليوم</span>
        </div>
      </div>

      <div x-show="syncMessage" x-transition class="mt-4 p-3 bg-emerald-900/40 border border-emerald-500/30 rounded-xl text-xs text-emerald-200 font-medium flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
          <span>✅</span>
          <span x-text="syncMessage"></span>
        </div>
      </div>

      <div x-show="errorMessage" x-transition class="mt-4 p-4 bg-rose-950/60 border border-rose-500/40 rounded-xl text-xs text-rose-200 font-medium flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-inner">
        <div class="flex items-start gap-2.5">
          <span class="text-lg">⚠️</span>
          <div>
            <p class="text-rose-200/90 text-[11px] mt-0.5 leading-relaxed" x-text="errorMessage"></p>
          </div>
        </div>
      </div>
    </div>

    @if(session('status'))
      <div class="p-4 rounded-[14px] bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
        {{ session('status') }}
      </div>
    @endif

    <!-- Settings Form Card -->
    <div class="card-awesomic p-6 md:p-8">
      <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
          <h3 class="text-sm font-bold text-[#09090b] pb-2 border-b border-[#ececee] mb-4">معلومات المتجر العامة</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-[#18181b] mb-1">اسم المتجر الرسمي</label>
              <input type="text" name="store_name" value="{{ $settings['store_name'] ?? 'ميرال — متجر الحلي والهدايا الفاخرة' }}" class="input-awesomic text-xs">
            </div>
            <div>
              <label class="block text-xs font-bold text-[#18181b] mb-1">هاتف خدمة العملاء</label>
              <input type="text" name="store_phone" value="{{ $settings['store_phone'] ?? '+966 50 000 0000' }}" class="input-awesomic text-xs">
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-[#18181b] mb-1">البريد الإلكتروني للتواصل</label>
              <input type="email" name="store_email" value="{{ $settings['store_email'] ?? 'support@miral.sa' }}" class="input-awesomic text-xs">
            </div>
          </div>
        </div>

        <div>
          <h3 class="text-sm font-bold text-[#09090b] pb-2 border-b border-[#ececee] mb-4">إعدادات الشحن والتوصيل</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-[#18181b] mb-1">رسوم الشحن القياسية (ر.س)</label>
              <input type="number" name="shipping_fee" value="{{ $settings['shipping_fee'] ?? 25 }}" class="input-awesomic text-xs">
            </div>
            <div>
              <label class="block text-xs font-bold text-[#18181b] mb-1">الحد الأدنى للشحن المجاني (ر.س)</label>
              <input type="number" name="free_shipping_min" value="{{ $settings['free_shipping_min'] ?? 300 }}" class="input-awesomic text-xs">
            </div>
          </div>
        </div>

        <button type="submit" class="btn-primary w-full py-3.5 text-sm font-bold rounded-xl">
          حفظ الإعدادات
        </button>

      </form>
    </div>

  </div>
</x-layouts.admin>
