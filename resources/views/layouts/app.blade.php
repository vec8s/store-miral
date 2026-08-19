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
                        canvasMist: '#f2f4f5',
                        pureWhite: '#ffffff',
                        inkBlack: '#000000',
                        faintBorder: '#ebebeb',
                        mutedGray: '#787574',
                        coolStone: '#cccccc',
                        warmFog: '#acb0aa',
                        shopViolet: '#5433eb',
                        violetWash: '#c0b5f3',
                        slateInk: '#332f2d',
                        ashVeil: '#665a54'
                    },
                    fontFamily: {
                        'gt': ["'GT Standard'", 'Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', "'Segoe UI'", 'Roboto', 'sans-serif'],
                        'shopify': ["'Shopify Sans'", 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    },
                    fontSize: {
                        'xss': ['9px', { lineHeight: '1.33', letterSpacing: '-0.058em' }],
                        'caption': ['11px', { lineHeight: '1.33', letterSpacing: '-0.017em' }],
                        'body-sm': ['12px', { lineHeight: '1.33', letterSpacing: '-0.017em' }],
                        'body': ['14px', { lineHeight: '1.33', letterSpacing: '-0.014em' }],
                        'body-lg': ['16px', { lineHeight: '1.33', letterSpacing: '-0.031em' }],
                        'display': ['20px', { lineHeight: '1.10', letterSpacing: '-0.05em' }]
                    },
                    borderRadius: {
                        'cards': '28px',
                        'inner': '20px',
                        'chips': '9999px',
                        'pills': '20px'
                    },
                    boxShadow: {
                        'soft': 'rgba(0, 0, 0, 0.06) 0px 2px 8px 0px',
                        'lift': 'rgba(0, 0, 0, 0.1) 0px 4px 6px -1px, rgba(0, 0, 0, 0.1) 0px 2px 4px -2px',
                        'deep': 'rgba(0, 0, 0, 0.12) 0px 4px 24px 0px',
                        'violet-glow': 'rgba(69, 36, 219, 0.34) 0px 4px 24px 0px'
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --color-canvas-mist: #f2f4f5;
            --color-pure-white: #ffffff;
            --color-ink-black: #000000;
            --color-faint-border: #ebebeb;
            --color-muted-gray: #787574;
            --color-cool-stone: #cccccc;
            --color-warm-fog: #acb0aa;
            --color-shop-violet: #5433eb;
            --color-violet-wash: #c0b5f3;
            --color-slate-ink: #332f2d;
            --color-ash-veil: #665a54;
        }

        * { font-family: 'GT Standard', Inter, ui-sans-serif, system-ui, sans-serif; }
        html { scroll-behavior: smooth; }
        body { 
            background-color: var(--color-canvas-mist); 
            color: var(--color-ink-black);
            font-size: 16px;
            letter-spacing: -0.031em;
            line-height: 1.33;
        }

        /* RTL adjustments */
        [dir="rtl"] body {
            letter-spacing: 0;
        }

        .tracking-shop { letter-spacing: -0.014em; }
        .tracking-shop-lg { letter-spacing: -0.031em; }
        .tracking-shop-display { letter-spacing: -0.05em; }
        .tracking-shop-meta { letter-spacing: -0.017em; }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @stack('head')
</head>
<body class="bg-canvasMist text-inkBlack font-gt text-body-lg">
    <div class="flex min-h-screen">
        <!-- Sidebar Rail -->
        @include('components.sidebar-rail')

        <div class="flex-1 flex flex-col min-w-0">
            @include('components.navbar')
            
            <main class="flex-1 w-full max-w-[1200px] mx-auto px-4 lg:px-8">
                @yield('content')
            </main>

            @include('components.footer')
        </div>
    </div>

    @include('components.cart-drawer')
    @include('components.toast')
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                items: Alpine.$persist([]).as('cart_items'),
                isOpen: false,
                add(product, quantity = 1) {
                    const existing = this.items.find(item => item.id === product.id);
                    if (existing) { existing.quantity += quantity; } 
                    else { this.items.push({ ...product, quantity }); }
                    this.notify('تمت الإضافة بنجاح', 'success');
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
