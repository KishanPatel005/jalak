<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | JALAK FASHION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-[#F1F5F9] min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo/Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-black text-[#1C2434] tracking-tighter uppercase mb-2">JALAK FASHION</h1>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.3em]">Administrative Portal</p>
        </div>

        <!-- Login Card -->
        <div class="glass p-10 rounded-[40px] shadow-2xl shadow-blue-500/5">
            <h2 class="text-xl font-black text-slate-800 mb-8 tracking-tight">Login to Dashboard</h2>

            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 rounded-2xl border border-red-100 italic font-bold text-xs text-red-600">
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                    <input type="email" name="email" required placeholder="name@example.com" 
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" 
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700">
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-200 text-blue-600 focus:ring-blue-500/20">
                        <span class="ml-2 text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-slate-600 transition-colors">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-[#1C2434] text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 flex items-center justify-center space-x-2">
                    <span>Secure Access</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center mt-10 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
            &copy; {{ date('Y') }} Jalak Fashion. All Rights Reserved.
        </p>
    </div>
</body>
</html>
