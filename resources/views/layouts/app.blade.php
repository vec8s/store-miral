<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'رافال — متجر حلي فاخر')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Readex+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fef5f0', 100: '#fce8de', 200: '#f8d5c3', 300: '#f4b8a0',
                            400: '#ef8c67', 500: '#e85d2f', 600: '#d84a1b', 700: '#b83816',
                            800: '#943016', 900: '#7a2815'
                        }
                    },
                    fontFamily: { cairo: ['Cairo', 'sans-serif'], readex: ['Readex Pro', 'sans-serif'] }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Cairo', 'Readex Pro', sans-serif; }
        html { scroll-behavior: smooth; }
        body { background-color: #fafafa; }
    </style>
    @stack('head')
</head>
<body class="bg-gray-50">
    @include('components.navbar')
    <main class="flex-1">
        @yield('content')
    </main>
    @include('components.cart-drawer')
    @include('components.toast')
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                items: [],
                isOpen: false,
                add(product, quantity = 1) {
                    const existing = this.items.find(item => item.id === product.id);
                    if (existing) { existing.quantity += quantity; } 
                    else { this.items.push({ ...product, quantity }); }
                    this.notify('تمت الإضافة بنجاح ✓', 'success');
                },
                remove(productId) { this.items = this.items.filter(item => item.id !== productId); },
                updateQuantity(productId, quantity) {
                    if (quantity <= 0) { this.remove(productId); } 
                    else {
                        const item = this.items.find(item => item.id === productId);
                        if (item) item.quantity = quantity;
                    }
                },
                get total() { return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0); },
                get itemCount() { return this.items.reduce((sum, item) => sum + item.quantity, 0); },
                notify(message, type = 'info') { Alpine.store('toast').show(message, type); }
            });
            Alpine.store('toast', {
                messages: [],
                show(message, type = 'info') {
                    const id = Date.now();
                    this.messages.push({ id, message, type });
                    setTimeout(() => { this.messages = this.messages.filter(m => m.id !== id); }, 3000);
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
