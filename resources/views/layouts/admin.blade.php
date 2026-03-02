<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JALAK FASHION - Admin Panel</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-slate-900" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-20'" 
            class="fixed inset-y-0 left-0 z-50 flex flex-col bg-[#1C2434] transition-all duration-300 ease-in-out lg:static lg:inset-0"
        >
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between px-6 py-6 h-20 border-b border-[#313D4A]">
                <a href="/" class="flex items-center space-x-3" :class="!sidebarOpen && 'hidden lg:flex'">
                    <span class="text-white text-xl font-bold tracking-wider">JALAK FASHION</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Content -->
            <nav class="flex-1 overflow-y-auto mt-5 scrollbar-hide">
                <div class="px-4 space-y-2">
                    <!-- Dashboard -->
                    <a href="/" class="flex items-center px-4 py-3 rounded-md transition-all duration-300 group {{ request()->is('/') ? 'bg-[#333A48] text-white' : 'text-[#DEE4EE] hover:bg-[#333A48]' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span :class="!sidebarOpen && 'hidden lg:hidden'">Dashboard</span>
                    </a>

                    <!-- Products -->
                    <a href="/products" class="flex items-center px-4 py-3 rounded-md transition-all duration-300 group {{ request()->is('products*') || request()->is('manage-product*') ? 'bg-[#333A48] text-white' : 'text-[#DEE4EE] hover:bg-[#333A48]' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span :class="!sidebarOpen && 'hidden lg:hidden'">Products</span>
                    </a>

                    <!-- Rent -->
                    <a href="/rent" class="flex items-center px-4 py-3 rounded-md transition-all duration-300 group {{ (request()->is('rent*') || request()->is('manage-booking*')) && request('status') != 'finished' ? 'bg-[#333A48] text-white' : 'text-[#DEE4EE] hover:bg-[#333A48]' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span :class="!sidebarOpen && 'hidden lg:hidden'">Rent Management</span>
                    </a>

                    <!-- Delivery -->
                    <a href="/deliveries" class="flex items-center px-4 py-3 rounded-md transition-all duration-300 group {{ request()->is('deliveries*') || request()->is('manage-delivery*') ? 'bg-[#333A48] text-white' : 'text-[#DEE4EE] hover:bg-[#333A48]' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span :class="!sidebarOpen && 'hidden lg:hidden'">Delivery Hub</span>
                    </a>

                    <!-- Return -->
                    <a href="/returns" class="flex items-center px-4 py-3 rounded-md transition-all duration-300 group {{ request()->is('returns*') || request()->is('manage-return*') ? 'bg-[#333A48] text-white' : 'text-[#DEE4EE] hover:bg-[#333A48]' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2" />
                        </svg>
                        <span :class="!sidebarOpen && 'hidden lg:hidden'">Return Center</span>
                    </a>

                    <!-- Invoices -->
                    <a href="{{ route('invoices.index') }}" class="flex items-center px-4 py-3 rounded-md transition-all duration-300 group {{ request()->is('invoices*') ? 'bg-[#333A48] text-white' : 'text-[#DEE4EE] hover:bg-[#333A48]' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span :class="!sidebarOpen && 'hidden lg:hidden'">Invoice Center</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden transition-all duration-300">
            <!-- Header -->
            <header class="sticky top-0 z-40 flex w-full bg-white shadow-sm h-20 transition-all duration-300">
                <div class="flex flex-grow items-center justify-between px-6">
                    <div class="flex items-center lg:hidden">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-900 border border-slate-200 rounded p-1.5 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center">
                        <h1 class="text-2xl font-extrabold text-[#1C2434] tracking-tight">JALAK FASHION</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="relative flex items-center space-x-3">
                            <div class="hidden text-right lg:block">
                                <p class="text-sm font-black text-[#1C2434] uppercase tracking-tight">{{ Auth::user()->name }}</p>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[9px] font-black text-slate-400 uppercase tracking-widest hover:text-red-600 transition-colors">Sign Out</button>
                                </form>
                            </div>
                            <img class="h-10 w-10 rounded-full border-2 border-[#1C2434]/5 object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1C2434&color=fff" alt="User">
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="mx-auto w-full max-w-screen-2xl p-6 md:p-10">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
