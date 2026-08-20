<x-layouts.admin title="إدارة المنتجات — إدارة ميرال">
  <div class="space-y-8" x-data="{
    showAddModal: false,
    showEditModal: false,
    newProduct: { name: '', price: '', sale_price: '', category_name: 'السلاسل', stock: '20', description: '', thumbnail_url: '' },
    editProductData: { id: null, name: '', price: '', sale_price: '', category_name: '', stock: '', description: '', thumbnail_url: '' },

    async createProduct() {
      const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
      const res = await fetch('/api/admin/products', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken || '' },
        body: JSON.stringify(this.newProduct)
      });
      const data = await res.json();
      if (data.success) {
        location.reload();
      }
    },

    openEditModal(p) {
      this.editProductData = {
        id: p.id,
        name: p.name,
        price: p.price,
        sale_price: p.sale_price || '',
        category_name: p.category ? p.category.name : 'عام',
        stock: p.stock,
        description: p.description || '',
        thumbnail_url: p.thumbnail_url || ''
      };
      this.showEditModal = true;
    },

    async updateProduct() {
      const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
      const res = await fetch('/api/admin/products/' + this.editProductData.id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken || '' },
        body: JSON.stringify(this.editProductData)
      });
      const data = await res.json();
      if (data.success) {
        location.reload();
      }
    },

    async deleteProduct(id) {
      if (confirm('هل أنت تأكد من حذف هذا المنتج؟')) {
        const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
        await fetch('/api/admin/products/' + id, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': csrfToken || '' }
        });
        location.reload();
      }
    }
  }">
    
    <!-- Admin Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#ececee]">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#09090b] tracking-tight">إدارة المنتجات</h1>
        <p class="text-xs text-[#71717a] mt-1">إضافة، تعديل وحذف منتجات متجر ميرال</p>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="btn-ghost text-xs px-4 py-3 min-h-[44px] font-medium whitespace-nowrap">&rarr; العودة للوحة الرئيسية</a>
        <button @click="showAddModal = true" class="btn-primary text-xs px-4 py-3 min-h-[44px] font-medium whitespace-nowrap">
          + إضافة منتج جديد
        </button>
      </div>
    </div>

    <!-- Salla Sync Status Banner -->
    <div class="p-4 rounded-[16px] bg-slate-900 text-white shadow-sm border border-slate-800"
         x-data="{
           syncing: false,
           errorMessage: '',
           errorType: '',
           retryCount: 0,
           maxRetries: 3,
           async triggerSync(attempt = 1) {
             this.syncing = true;
             this.errorMessage = '';
             this.errorType = '';
             this.retryCount = attempt;
             try {
               const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
               const res = await fetch('/admin/sync/run', {
                 method: 'POST',
                 headers: { 'X-CSRF-TOKEN': csrfToken || '' }
               });
               const data = await res.json();
               if (res.ok && data.success) {
                 location.reload();
               } else {
                 this.errorMessage = data.message || 'حدث خطأ أثناء المزامنة مع منصة سلة.';
               }
             } catch (e) {
               this.errorMessage = 'تعذر الاتصال بالمزامنة حالياً.';
             } finally {
               this.syncing = false;
             }
           }
         }">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-lg">
            📦
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h3 class="font-extrabold text-xs sm:text-sm text-white">مزامنة المخزون مع منصة سلة Salla API</h3>
              <span class="text-[10px] bg-emerald-500/20 text-emerald-300 font-bold px-2 py-0.5 rounded-full border border-emerald-500/30">نشط</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">يتم جلب المنتجات تلقائياً من حساب سلة الرسمية.</p>
          </div>
        </div>

        <button @click="triggerSync(1)" :disabled="syncing" 
                class="btn-primary text-xs px-4 py-3 min-h-[44px] bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl whitespace-nowrap flex items-center gap-2 transition-all disabled:opacity-50">
          <span x-show="!syncing">🔄 مزامنة المخزون من سلة</span>
          <span x-show="syncing" class="animate-spin">⏳</span>
          <span x-show="syncing" x-text="retryCount > 1 ? `إعادة المحاولة ${retryCount}...` : 'جاري الجلب...'"></span>
        </button>
      </div>

      <div x-show="errorMessage" x-transition class="mt-3 pt-3 border-t border-slate-800 text-xs flex items-center justify-between gap-3 text-rose-300">
        <div class="flex items-center gap-2">
          <span>⚠️</span>
          <span x-text="errorMessage"></span>
        </div>
      </div>
    </div>

    <!-- Products Table Card -->
    <div class="card-awesomic p-6">
      <div class="overflow-x-auto">
        <table class="w-full text-right text-xs">
          <thead>
            <tr class="border-b border-[#ececee] text-[#71717a] font-bold">
              <th class="pb-3">المنتج</th>
              <th class="pb-3">القسم</th>
              <th class="pb-3">السعر الأصلي</th>
              <th class="pb-3">سعر الخصم</th>
              <th class="pb-3">المخزون</th>
              <th class="pb-3 text-center">الإجراءات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ececee]">
            @foreach($products as $p)
              @php
                $id = data_get($p, 'id');
                $name = data_get($p, 'name');
                $categoryName = data_get($p, 'category.name', 'عام');
                $price = data_get($p, 'price', 0);
                $salePrice = data_get($p, 'sale_price');
                $stock = data_get($p, 'stock', 0);
                $thumbnail = data_get($p, 'thumbnail_url');
              @endphp
              <tr class="hover:bg-[#fafafa] transition">
                <td class="py-3 flex items-center gap-3">
                  <img src="{{ $thumbnail }}" alt="" class="w-10 h-10 rounded-[10px] object-cover bg-[#f4f4f5] border border-[#ececee]">
                  <a href="{{ route('shop.show', $id) }}" class="font-bold text-[#09090b] hover:underline">{{ $name }}</a>
                </td>
                <td class="py-3 text-[#52525b]">{{ $categoryName }}</td>
                <td class="py-3 font-bold text-[#09090b]">{{ number_format($price, 2) }} <span class="currency-sar">ر.س</span></td>
                <td class="py-3 font-bold text-[#ff5a00]">{{ $salePrice ? number_format($salePrice, 2) . ' ر.س' : '—' }}</td>
                <td class="py-3"><span class="badge-tag">{{ $stock }} قطعة</span></td>
                <td class="py-3 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button @click="openEditModal({{ json_encode($p) }})" class="btn-ghost text-xs px-2.5 py-3 min-h-[44px] text-[#09090b] font-bold">تعديل ✏️</button>
                    <button @click="deleteProduct({{ $id }})" class="btn-ghost text-xs px-2.5 py-3 min-h-[44px] text-red-600 hover:bg-red-50 font-bold">حذف 🗑️</button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add Product Modal -->
    <div x-show="showAddModal" x-transition x-cloak class="fixed inset-0 z-50 bg-[#09090b]/60 backdrop-blur-sm overflow-y-auto">
      <div class="min-h-full flex items-center justify-center p-4">
        <div class="card-awesomic p-6 max-w-lg w-full space-y-4 bg-white shadow-2xl max-h-[90vh] overflow-y-auto" @click.away="showAddModal = false">
        <h3 class="text-base font-bold text-[#09090b] pb-2 border-b border-[#ececee]">إضافة منتج جديد</h3>

        <form @submit.prevent="createProduct()" class="space-y-3">
          <div>
            <label class="block text-xs font-medium text-[#18181b] mb-1">اسم المنتج</label>
            <input type="text" x-model="newProduct.name" required class="input-awesomic text-xs py-2">
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-[#18181b] mb-1">السعر الأصلي (ر.س)</label>
              <input type="number" x-model="newProduct.price" required class="input-awesomic text-xs py-2">
            </div>
            <div>
              <label class="block text-xs font-medium text-[#18181b] mb-1">سعر الخصم (اختياري)</label>
              <input type="number" x-model="newProduct.sale_price" class="input-awesomic text-xs py-2">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-[#18181b] mb-1">القسم</label>
              <select x-model="newProduct.category_name" class="input-awesomic text-xs py-2">
                <option value="السلاسل">السلاسل</option>
                <option value="الساعات">الساعات</option>
                <option value="بوكسات هدايا">بوكسات هدايا</option>
                <option value="عقيق يماني">عقيق يماني</option>
                <option value="ميداليات">ميداليات</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-[#18181b] mb-1">كمية المخزون</label>
              <input type="number" x-model="newProduct.stock" required class="input-awesomic text-xs py-2">
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#18181b] mb-1">رابط صورة المنتج (URL)</label>
            <input type="url" x-model="newProduct.thumbnail_url" placeholder="https://..." class="input-awesomic text-xs py-2">
          </div>

          <div>
            <label class="block text-xs font-medium text-[#18181b] mb-1">وصف المنتج</label>
            <textarea x-model="newProduct.description" rows="3" class="input-awesomic text-xs py-2"></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-[#ececee]">
            <button type="button" @click="showAddModal = false" class="btn-ghost text-xs px-4 py-3 min-h-[44px]">إلغاء</button>
            <button type="submit" class="btn-primary text-xs px-5 py-3 min-h-[44px] font-medium">حفظ المنتج</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Product Modal -->
    <div x-show="showEditModal" x-transition x-cloak class="fixed inset-0 z-50 bg-[#09090b]/60 backdrop-blur-sm overflow-y-auto">
      <div class="min-h-full flex items-center justify-center p-4">
        <div class="card-awesomic p-6 max-w-lg w-full space-y-4 bg-white shadow-2xl max-h-[90vh] overflow-y-auto" @click.away="showEditModal = false">
        <h3 class="text-base font-bold text-[#09090b] pb-2 border-b border-[#ececee]">تعديل بيانات المنتج</h3>

        <form @submit.prevent="updateProduct()" class="space-y-3">
          <div>
            <label class="block text-xs font-medium text-[#18181b] mb-1">اسم المنتج</label>
            <input type="text" x-model="editProductData.name" required class="input-awesomic text-xs py-2">
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-[#18181b] mb-1">السعر الأصلي (ر.س)</label>
              <input type="number" x-model="editProductData.price" required class="input-awesomic text-xs py-2">
            </div>
            <div>
              <label class="block text-xs font-medium text-[#18181b] mb-1">سعر الخصم (اختياري)</label>
              <input type="number" x-model="editProductData.sale_price" class="input-awesomic text-xs py-2">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-[#18181b] mb-1">القسم</label>
              <select x-model="editProductData.category_name" class="input-awesomic text-xs py-2">
                <option value="السلاسل">السلاسل</option>
                <option value="الساعات">الساعات</option>
                <option value="بوكسات هدايا">بوكسات هدايا</option>
                <option value="عقيق يماني">عقيق يماني</option>
                <option value="ميداليات">ميداليات</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-[#18181b] mb-1">كمية المخزون</label>
              <input type="number" x-model="editProductData.stock" required class="input-awesomic text-xs py-2">
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#18181b] mb-1">رابط صورة المنتج (URL)</label>
            <input type="url" x-model="editProductData.thumbnail_url" placeholder="https://..." class="input-awesomic text-xs py-2">
          </div>

          <div>
            <label class="block text-xs font-medium text-[#18181b] mb-1">وصف المنتج</label>
            <textarea x-model="editProductData.description" rows="3" class="input-awesomic text-xs py-2"></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-2 border-t border-[#ececee]">
            <button type="button" @click="showEditModal = false" class="btn-ghost text-xs px-4 py-3 min-h-[44px]">إلغاء</button>
            <button type="submit" class="btn-primary text-xs px-5 py-3 min-h-[44px] font-medium">تحديث المنتج</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</x-layouts.admin>
