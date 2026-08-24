<x-layouts.admin title="إعدادات المتجر وسلة — إدارة ميرال">
  <div class="space-y-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#e2e8f0]">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0f172a] tracking-tight">إعدادات المتجر وتكامل سلة</h1>
        <p class="text-xs text-[#64748b] mt-1">تعديل معلومات المتجر والربط المباشر مع منصة سلة Salla</p>
      </div>

      <a href="{{ route('admin.dashboard') }}" class="btn-ghost text-xs px-3.5 py-2.5 min-h-[40px] font-bold whitespace-nowrap">&rarr; العودة للرئيسية</a>
    </div>

    <!-- Salla Integration Status Box -->
    <div class="p-6 rounded-2xl bg-[#0f172a] text-white shadow-sm border border-[#1e293b]"
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
          <span class="text-xs font-semibold px-3 py-1 rounded-full border border-emerald-500/30 text-emerald-300 bg-emerald-950/50 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Salla OAuth 2.0 Integration</span>
          </span>
        </div>
        
        <button @click="syncNow()" :disabled="syncing" 
                class="text-xs px-4 py-2.5 min-h-[40px] bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl flex items-center gap-2 disabled:opacity-50 transition-all whitespace-nowrap shadow-sm">
          <span x-show="!syncing">🔄 مزامنة المنتجات الآن</span>
          <span x-show="syncing" class="animate-spin">⏳</span>
        </button>
      </div>

      <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
        <span>🏪</span> متجر ميرال — مزامنة منصة سلة
      </h3>
      
      <p class="text-xs text-slate-400 mt-1 leading-relaxed">
        يستخدم النظام طبقة الربط المباشرة مع Salla APIs لجلب المنتجات وتحديث المخزون ومزامنة الطلبات لحظياً.
      </p>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-slate-800 text-xs">
        <div class="bg-slate-800/60 p-3 rounded-xl border border-slate-700/60">
          <span class="text-slate-400 block text-[10px] mb-0.5">حالة مفاتيح API</span>
          <span class="font-bold text-emerald-400">متوفرة بالبيئة ✅</span>
        </div>
        <div class="bg-slate-800/60 p-3 rounded-xl border border-slate-700/60">
          <span class="text-slate-400 block text-[10px] mb-0.5">المنتجات المرتبطة</span>
          <span class="font-bold text-amber-300">28 منتج</span>
        </div>
        <div class="bg-slate-800/60 p-3 rounded-xl border border-slate-700/60">
          <span class="text-slate-400 block text-[10px] mb-0.5">حالة التحديث</span>
          <span class="font-bold text-slate-200">فوري وتلقائي</span>
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
      <div class="p-4 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
        {{ session('status') }}
      </div>
    @endif

    <!-- Settings Form Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-white border border-[#e2e8f0] shadow-sm">
      <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
          <h3 class="text-sm font-bold text-[#0f172a] pb-2 border-b border-[#f1f5f9] mb-4">معلومات المتجر الأساسية</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-[#334155] mb-1">اسم المتجر</label>
              <input type="text" name="store_name" value="{{ $settings['store_name'] ?? 'ميرال — متجر الحلي والهدايا الفاخرة' }}" class="input-awesomic text-xs">
            </div>
            <div>
              <label class="block text-xs font-bold text-[#334155] mb-1">هاتف الدعم وخدمة العملاء</label>
              <input type="text" name="store_phone" value="{{ $settings['store_phone'] ?? '+966 50 000 0000' }}" class="input-awesomic text-xs">
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-[#334155] mb-1">البريد الإلكتروني للطلبات</label>
              <input type="email" name="store_email" value="{{ $settings['store_email'] ?? 'support@miral.sa' }}" class="input-awesomic text-xs">
            </div>
          </div>
        </div>

        <div>
          <h3 class="text-sm font-bold text-[#0f172a] pb-2 border-b border-[#f1f5f9] mb-4">خيارات الشحن والتوصيل</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-[#334155] mb-1">رسوم الشحن القياسية (ر.س)</label>
              <input type="number" name="shipping_fee" value="{{ $settings['shipping_fee'] ?? 25 }}" class="input-awesomic text-xs">
            </div>
            <div>
              <label class="block text-xs font-bold text-[#334155] mb-1">الحد الأدنى للشحن المجاني (ر.س)</label>
              <input type="number" name="free_shipping_min" value="{{ $settings['free_shipping_min'] ?? 300 }}" class="input-awesomic text-xs">
            </div>
          </div>
        </div>

        <button type="submit" class="w-full py-3 text-xs font-bold rounded-xl bg-[#0f172a] hover:bg-[#1e293b] text-white transition shadow-sm">
          حفظ إعدادات المتجر
        </button>

      </form>
    </div>

  </div>
</x-layouts.admin>
