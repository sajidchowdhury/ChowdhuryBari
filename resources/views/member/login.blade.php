<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>সদস্য লগইন — চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans Bengali', system-ui, sans-serif; }
        .heading-serif { font-family: 'Playfair Display', 'Noto Sans Bengali', Georgia, serif; font-weight: 700; letter-spacing: -0.02em; }
        .login-bg {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 35%, #047857 70%, #0d9488 100%);
        }
        .login-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(245, 158, 11, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(13, 148, 136, 0.2) 0%, transparent 50%);
            pointer-events: none;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .step-enter { animation: slideUp 0.4s cubic-bezier(0.32, 0.72, 0, 1); }
        .otp-input { letter-spacing: 0.5em; }
        input:focus { box-shadow: 0 0 0 4px rgba(6, 95, 70, 0.1); }
    </style>
</head>
<body class="login-bg min-h-screen relative flex items-center justify-center p-4 overflow-hidden">

    <!-- Decorative floating circles -->
    <div class="absolute top-10 left-10 w-32 h-32 bg-amber-400/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 right-10 w-48 h-48 bg-emerald-300/10 rounded-full blur-3xl"></div>

    <div class="relative w-full max-w-md">

        <!-- Back to home -->
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-emerald-100 hover:text-white text-sm mb-6 transition">
            <i class="fas fa-arrow-left"></i> ওয়েবসাইটে ফিরুন
        </a>

        <div class="glass-card rounded-3xl overflow-hidden step-enter">

            <!-- Header -->
            <div class="bg-gradient-to-r from-emerald-800 to-emerald-900 px-7 py-7 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-400/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center ring-2 ring-white/20">
                        <i class="fas fa-user-shield text-2xl"></i>
                    </div>
                    <div>
                        <div class="font-bold text-2xl tracking-tight heading-serif">সদস্য পোর্টাল</div>
                        <div class="text-emerald-200 text-sm">চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা</div>
                    </div>
                </div>
            </div>

            <div class="p-7">

                @if($errors->any())
                    <div class="mb-5 rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700 text-sm flex items-start gap-2">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                        <div>
                            @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
                        </div>
                    </div>
                @endif

                @if(session('status'))
                    <div class="mb-5 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- STEP 1: Phone -->
                @if($step === 'phone')
                    <div class="step-enter">
                        <div class="mb-6">
                            <div class="text-sm font-semibold text-slate-600 mb-2 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">১</span>
                                আপনার নিবন্ধিত মোবাইল নম্বর দিন
                            </div>
                            <form action="{{ route('member.login.otp') }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="relative">
                                    <i class="fas fa-phone absolute left-5 top-1/2 -translate-y-1/2 text-emerald-600"></i>
                                    <input name="phone" type="tel" required autofocus
                                           value="{{ old('phone', $phone) }}"
                                           placeholder="01XXXXXXXXX"
                                           class="w-full border border-slate-200 focus:border-emerald-700 pl-12 pr-4 py-4 rounded-2xl text-lg font-medium outline-none transition">
                                </div>
                                <div class="text-[11px] text-emerald-700 flex items-center gap-1.5">
                                    <i class="fas fa-info-circle"></i>
                                    আপনার বাড়ির নিবন্ধিত মালিকের ফোন নম্বর দিন
                                </div>

                                <button type="submit"
                                        class="w-full py-4 bg-gradient-to-r from-emerald-700 to-emerald-800 hover:from-emerald-800 hover:to-emerald-900 active:scale-[0.98] transition text-white font-semibold rounded-2xl flex items-center justify-center gap-2 shadow-lg shadow-emerald-800/30">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>OTP পাঠান</span>
                                </button>
                            </form>
                        </div>

                        <!-- Demo hint -->
                        <div class="rounded-2xl bg-amber-50 border border-amber-200 p-3 text-center">
                            <div class="text-[11px] text-amber-700">
                                <i class="fas fa-flask mr-1"></i>
                                অ্যাডমিন প্যানেলে নিবন্ধিত বাড়ির মালিকের ফোন নম্বর দিয়ে লগইন করুন • OTP: <span class="font-mono font-bold">৯৯৯৯</span>
                            </div>
                        </div>
                    </div>

                <!-- STEP 2: OTP -->
                @else
                    <div class="step-enter">
                        <div class="text-center mb-6">
                            <div class="mx-auto w-16 h-16 bg-gradient-to-br from-emerald-100 to-emerald-200 text-emerald-700 rounded-2xl flex items-center justify-center mb-4 ring-4 ring-emerald-50">
                                <i class="fas fa-sms text-3xl"></i>
                            </div>
                            <div class="font-bold text-xl text-slate-800">OTP যাচাই করুন</div>
                            <div class="text-sm text-slate-500 mt-1">
                                আপনার নম্বরে <br>
                                <span class="font-semibold text-emerald-700">{{ $phone }}</span>
                            </div>
                        </div>

                        <form action="{{ route('member.login.verify') }}" method="POST" class="space-y-5">
                            @csrf
                            {{-- Pass the encrypted phone token so step 2 doesn't depend on session persistence --}}
                            <input type="hidden" name="phone_token" value="{{ $phoneToken }}">
                            <div>
                                <div class="text-xs font-semibold text-slate-500 mb-2 text-center">৪ সংখ্যার OTP লিখুন</div>
                                <input name="otp" type="text" maxlength="4" inputmode="numeric" required autofocus
                                       value="{{ old('otp') }}"
                                       class="otp-input w-full text-center border border-slate-200 focus:border-emerald-700 rounded-2xl py-4 outline-none font-mono text-2xl font-bold text-emerald-800 transition"
                                       placeholder="••••"
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                <div class="text-[11px] text-center text-amber-600 mt-2 bg-amber-50 rounded-xl py-1.5">
                                    <i class="fas fa-flask mr-1"></i> ডেমো: OTP হলো <span class="font-mono font-bold">৯৯৯৯</span>
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full py-4 bg-gradient-to-r from-emerald-700 to-emerald-800 hover:from-emerald-800 hover:to-emerald-900 active:scale-[0.98] transition text-white font-semibold rounded-2xl flex items-center justify-center gap-2 shadow-lg shadow-emerald-800/30">
                                <i class="fas fa-check-circle"></i>
                                <span>যাচাই করে লগইন করুন</span>
                            </button>
                        </form>

                        <div class="mt-5 flex items-center justify-between text-sm">
                            <a href="{{ route('member.login') }}" class="text-slate-500 hover:text-slate-700 flex items-center gap-1">
                                <i class="fas fa-arrow-left text-xs"></i> আবার নম্বর দিন
                            </a>
                            <form method="POST" action="{{ route('member.login.otp') }}">
                                @csrf
                                <input type="hidden" name="phone" value="{{ $phone }}">
                                <button type="submit" class="text-emerald-700 hover:underline text-xs flex items-center gap-1">
                                    <i class="fas fa-redo"></i> OTP আবার পাঠান
                                </button>
                            </form>
                        </div>
                        <div class="mt-4 text-center text-[11px] text-slate-400">
                            <i class="fas fa-clock mr-1"></i> টোকেন মেয়াদ: ১০ মিনিট
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- Footer note -->
        <div class="text-center text-emerald-100/70 text-xs mt-6">
            সদস্য নন? <a href="{{ route('home') }}#join" class="underline hover:text-white">সদস্যপদের জন্য আবেদন করুন</a>
        </div>
    </div>

</body>
</html>
