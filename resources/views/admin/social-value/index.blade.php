@extends('admin.layout')

@section('title', 'Social Value — ছবি রেটিং')
@section('page-title', 'Social Value — ছবি রেটিং')

@section('content')
<div class="space-y-6" x-data="{ ratingModal: null, selectedStars: 0 }">

    <div class="flex flex-col lg:flex-row justify-between gap-4 items-start">
        <div>
            <h1 class="text-3xl font-semibold">Social Value</h1>
            <p class="text-slate-600 mt-2">সদস্যদের আপলোড করা উঠানের ছবি রেট করুন (১-১০ স্টার)। <strong>ছবিগুলো সম্পূর্ণ বেনামী</strong> — কে পাঠিয়েছে তা আপনি দেখতে পাবেন না।</p>
        </div>
    </div>

    {{-- How scoring works (formula card) --}}
    <div class="rounded-3xl bg-emerald-50 border border-emerald-200 p-6">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="text-sm text-emerald-900">
                <div class="font-semibold mb-1">স্কোর কীভাবে কাজ করে?</div>
                <p class="text-emerald-800 leading-relaxed">প্রতিটি ছবিকে আপনি <strong>১ থেকে ১০</strong> স্টার দিন। একজন সদস্যের মাসিক <strong>Social Value</strong> = তার রেট করা ছবিগুলোর গড় স্টার × ১০ (অর্থাৎ ১০-১০০ স্কেল)। উদাহরণ: ৪টি ছবির স্টার ৮, ৭, ৯, ১০ → গড় ৮.৫ → Social Value = ৮৫/১০০। র‍্যাঙ্কিং এই স্কোরের ভিত্তিতে হবে; সমান স্কোর হলে গত মাসের স্কোর বিবেচিত হবে।</p>
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-3xl bg-white border border-slate-200 p-5">
            <div class="text-xs text-slate-500 font-medium">এই মাসের মোট ছবি</div>
            <div class="text-3xl font-bold text-slate-800 mt-1 tabular-nums">{{ $totalThisMonth }}</div>
        </div>
        <div class="rounded-3xl bg-white border border-slate-200 p-5">
            <div class="text-xs text-amber-600 font-medium">রেট করা বাকি</div>
            <div class="text-3xl font-bold text-amber-600 mt-1 tabular-nums">{{ $pendingCount }}</div>
        </div>
        <div class="rounded-3xl bg-white border border-slate-200 p-5">
            <div class="text-xs text-emerald-600 font-medium">রেট সম্পন্ন</div>
            <div class="text-3xl font-bold text-emerald-600 mt-1 tabular-nums">{{ $ratedCount }}</div>
        </div>
        <div class="rounded-3xl bg-white border border-slate-200 p-5">
            <div class="text-xs text-slate-500 font-medium">গড় স্টার</div>
            <div class="text-3xl font-bold text-slate-800 mt-1 tabular-nums">{{ $avgScore ?? '—' }}<span class="text-base text-slate-400">/১০</span></div>
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">{{ session('status') }}</div>
    @endif

    {{-- Tabs: Pending / Rated --}}
    <div x-data="{ tab: 'pending' }">
        <div class="flex border-b border-slate-200 mb-5">
            <button @click="tab='pending'" :class="tab==='pending' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500'"
                    class="px-5 py-2.5 text-sm font-medium border-b-2 transition">
                রেট করা বাকি <span class="ml-1 text-xs bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
            </button>
            <button @click="tab='rated'" :class="tab==='rated' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500'"
                    class="px-5 py-2.5 text-sm font-medium border-b-2 transition">
                রেট সম্পন্ন <span class="ml-1 text-xs bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full">{{ $ratedCount }}</span>
            </button>
        </div>

        {{-- PENDING (unrated) --}}
        <div x-show="tab==='pending'">
            @if($unrated->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($unrated as $upload)
                        <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-sm">
                            <div class="relative aspect-square bg-slate-100 cursor-pointer"
                                 @click="selectedStars = 0; ratingModal = {{ $upload->id }}; $dispatch('open-modal', { detail: 'rate-{{ $upload->id }}' })">
                                <img src="{{ $upload->image_url }}" alt="Member upload" class="w-full h-full object-cover hover:scale-105 transition">
                                <div class="absolute inset-0 bg-black/0 hover:bg-black/20 transition flex items-center justify-center">
                                    <span class="opacity-0 hover:opacity-100 bg-white/90 text-emerald-800 text-xs font-semibold px-3 py-1.5 rounded-xl transition">
                                        <i class="fas fa-star mr-1"></i> রেট করুন
                                    </span>
                                </div>
                                <span class="absolute top-2 right-2 bg-amber-100 text-amber-700 text-[10px] px-2 py-0.5 rounded-full font-semibold">বাকি</span>
                            </div>
                            @if($upload->caption)
                                <div class="p-3 text-xs text-slate-500 truncate">{{ $upload->caption }}</div>
                            @endif
                        </div>

                        {{-- Rate modal — creative star picker --}}
                        <x-modal name="rate-{{ $upload->id }}" maxWidth="lg">
                            <div class="bg-white p-6" x-data="{ rating: 0, hover: 0, labels: {1:'খুব খারাপ',2:'খারাপ',3:'নিচে থেকে',4:'মোটামুটি',5:'গড়',6:'ভালো',7:'বেশ ভালো',8:'চমৎকার',9:'দুর্দান্ত',10:'অসাধারণ!'} }">
                                <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-5">
                                    <h2 class="text-xl font-semibold flex items-center gap-2">
                                        <i class="fas fa-star text-amber-400"></i> ছবি রেট করুন
                                    </h2>
                                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'rate-{{ $upload->id }}' }))" class="text-slate-500 hover:text-slate-900">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>

                                <img src="{{ $upload->image_url }}" alt="" class="w-full max-h-72 object-contain rounded-2xl mb-5 bg-slate-50">

                                @if($upload->caption)
                                    <p class="text-sm text-slate-500 mb-4 text-center italic">"{{ $upload->caption }}"</p>
                                @endif

                                <form action="{{ route('admin.social-value.rate', $upload) }}" method="POST" class="space-y-5" onsubmit="if(rating===0){alert('দয়া করে একটি স্টার নির্বাচন করুন');return false}">
                                    @csrf
                                    <input type="hidden" name="star_rating" :value="rating">

                                    {{-- Star display --}}
                                    <div class="text-center">
                                        <div class="flex justify-center gap-1 mb-3">
                                            @for($i = 1; $i <= 10; $i++)
                                                <button type="button"
                                                        @click="rating = {{ $i }}"
                                                        @mouseenter="hover = {{ $i }}"
                                                        @mouseleave="hover = 0"
                                                        class="text-3xl transition-all duration-150 transform hover:scale-125"
                                                        :class="{{ $i }} <= (hover || rating) ? 'text-amber-400 scale-110' : 'text-slate-200'">
                                                    <i class="fas fa-star" x-show="{{ $i }} <= (hover || rating)"></i>
                                                    <i class="far fa-star" x-show="{{ $i }} > (hover || rating)"></i>
                                                </button>
                                            @endfor
                                        </div>

                                        {{-- Rating label + big number --}}
                                        <div class="min-h-[60px] flex flex-col items-center justify-center">
                                            <template x-if="(hover || rating) > 0">
                                                <div class="animate-pulse">
                                                    <div class="text-4xl font-bold text-amber-500 tabular-nums" x-text="(hover || rating)"></div>
                                                    <div class="text-sm text-slate-500 mt-1" x-text="labels[hover || rating]"></div>
                                                </div>
                                            </template>
                                            <template x-if="(hover || rating) === 0">
                                                <div class="text-slate-400 text-sm">
                                                    <i class="fas fa-hand-pointer mr-1"></i> একটি স্টারে ক্লিক করুন
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'rate-{{ $upload->id }}' }))" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">বাতিল</button>
                                        <button type="submit" class="rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 px-6 py-2.5 text-sm font-medium text-white shadow-lg shadow-amber-500/30 transition active:scale-95">
                                            <i class="fas fa-check mr-1"></i> রেট সাবমিট করুন
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl bg-white border border-slate-200 p-12 text-center">
                    <i class="fas fa-check-circle text-5xl text-emerald-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-slate-700">সব ছবি রেট করা সম্পন্ন!</h3>
                    <p class="text-slate-500 mt-1">এই মাসের সব ছবি রেট করা হয়ে গেছে। নতুন ছবি এলে এখানে দেখা যাবে।</p>
                </div>
            @endif
        </div>

        {{-- RATED --}}
        <div x-show="tab==='rated'" x-cloak>
            @if($rated->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($rated as $upload)
                        <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-sm">
                            <div class="relative aspect-square bg-slate-100">
                                <img src="{{ $upload->image_url }}" alt="Member upload" class="w-full h-full object-cover">
                                <span class="absolute top-2 right-2 bg-amber-400 text-white text-xs px-2.5 py-1 rounded-full font-bold flex items-center gap-1">
                                    <i class="fas fa-star"></i> {{ $upload->star_rating }}/১০
                                </span>
                            </div>
                            <div class="p-3 text-xs text-slate-500">
                                রেট করেছেন: {{ $upload->rater?->name ?? 'Admin' }} • {{ $upload->rated_at?->format('M d') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl bg-white border border-slate-200 p-12 text-center">
                    <i class="fas fa-star text-5xl text-slate-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-slate-700">এখনো কিছু রেট করা হয়নি</h3>
                    <p class="text-slate-500 mt-1">"রেট করা বাকি" ট্যাবে যান এবং ছবিগুলো রেট করুন।</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
