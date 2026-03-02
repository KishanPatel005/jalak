@extends('layouts.admin')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold text-slate-800">Dashboard Overview</h2>
        <p class="text-slate-500 mt-1">Welcome back to JALAK FASHION control center.</p>
    </div>
    <div class="flex space-x-3">
        <button class="flex items-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Report
        </button>
        <button class="flex items-center px-4 py-2 bg-[#1C2434] text-white rounded-lg hover:bg-slate-800 transition-colors shadow-md">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Product
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <span class="text-xs font-medium text-green-500 bg-green-50 px-2 py-1 rounded-full">+12.5%</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Total Products</h3>
        <p class="text-2xl font-bold text-slate-800 mt-1">1,482</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-xs font-medium text-green-500 bg-green-50 px-2 py-1 rounded-full">+8.2%</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Monthly Rent</h3>
        <p class="text-2xl font-bold text-slate-800 mt-1">$12,850</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="text-xs font-medium text-red-500 bg-red-50 px-2 py-1 rounded-full">-3.1%</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Active Deliveries</h3>
        <p class="text-2xl font-bold text-slate-800 mt-1">45</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs font-medium text-green-500 bg-green-50 px-2 py-1 rounded-full">+15.7%</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Paid Invoices</h3>
        <p class="text-2xl font-bold text-slate-800 mt-1">328</p>
    </div>
</div>

<!-- Main Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Activity -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Recent Transactions</h3>
            <a href="#" class="text-sm text-blue-600 hover:underline">View All</a>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-slate-400 text-xs uppercase tracking-wider">
                            <th class="pb-3 pr-4">Order ID</th>
                            <th class="pb-3 pr-4">Customer</th>
                            <th class="pb-3 pr-4">Amount</th>
                            <th class="pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr class="text-sm">
                            <td class="py-4 font-medium text-slate-700">#LF-9281</td>
                            <td class="py-4 text-slate-600">Sarah Jenkins</td>
                            <td class="py-4 font-bold text-slate-800">$240.00</td>
                            <td class="py-4"><span class="px-2 py-1 bg-green-50 text-green-600 text-xs rounded-full">Completed</span></td>
                        </tr>
                        <tr class="text-sm">
                            <td class="py-4 font-medium text-slate-700">#LF-9282</td>
                            <td class="py-4 text-slate-600">Robert Vance</td>
                            <td class="py-4 font-bold text-slate-800">$185.50</td>
                            <td class="py-4"><span class="px-2 py-1 bg-amber-50 text-amber-600 text-xs rounded-full">Processing</span></td>
                        </tr>
                        <tr class="text-sm">
                            <td class="py-4 font-medium text-slate-700">#LF-9283</td>
                            <td class="py-4 text-slate-600">Emily Blunt</td>
                            <td class="py-4 font-bold text-slate-800">$592.00</td>
                            <td class="py-4"><span class="px-2 py-1 bg-green-50 text-green-600 text-xs rounded-full">Completed</span></td>
                        </tr>
                        <tr class="text-sm">
                            <td class="py-4 font-medium text-slate-700">#LF-9284</td>
                            <td class="py-4 text-slate-600">Michael Ross</td>
                            <td class="py-4 font-bold text-slate-800">$120.00</td>
                            <td class="py-4"><span class="px-2 py-1 bg-red-50 text-red-600 text-xs rounded-full">Canceled</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions / Mini Stats -->
    <div class="space-y-6">
        <div class="bg-[#1C2434] text-white p-6 rounded-xl shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-lg font-bold mb-2">Upgrade to Pro</h3>
                <p class="text-slate-400 text-sm mb-4">Get access to premium features and unlimited storage.</p>
                <button class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">Upgrade Now</button>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-blue-500 rounded-full opacity-20"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-800 mb-4">Inventory Alert</h3>
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                    <span class="text-sm text-slate-600">Wedding Gown (Red) - Low Stock</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                    <span class="text-sm text-slate-600">Silk Saree (Blue) - 2 units left</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                    <span class="text-sm text-slate-600">Tuxedo (Black) - Out of stock</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
