@extends('admin.layout')

@section('title', 'ফিল্ড ডাটা সংগ্রহ')
@section('page-title', 'ফিল্ড ডাটা সংগ্রহ')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row justify-between gap-4 items-start">
        <div>
            <h1 class="text-3xl font-semibold">ফিল্ড ডাটা সংগ্রহ</h1>
            <p class="text-slate-600 mt-2">প্রতিটি বিল্ডিং এর তথ্য সংগ্রহ করুন — দিন শেষে একসাথে মেইন ডাটাবেসে মাইগ্রেট করুন।</p>
        </div>
        <div class="flex gap-2">
            @if($draftCount > 0)
                <form action="{{ route('admin.field-data.migrate-all') }}" method="POST" onsubmit="return confirm('সব ড্রাফট ডাটা মেইন ডাটাবেসে মাইগ্রেট করতে চান? {{ $draftCount }} টি বিল্ডিং। এটি আর পূর্বাবস্থায় ফেরানো যাবে না।')">
                    @csrf
                    <button type="submit" class="rounded-2xl bg-emerald-600 hover:bg-emerald-700 px-5 py-3 text-white font-medium transition">
                        <i class="fas fa-database mr-2"></i> সব মাইগ্রেট করুন ({{ $draftCount }})
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.field-data.create') }}" class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-medium hover:bg-slate-700 transition">
                <i class="fas fa-plus mr-2"></i> নতুন ডাটা সংগ্রহ
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700">{{ session('error') }}</div>
    @endif

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="rounded-3xl bg-white border border-slate-200 p-5">
            <div class="text-xs text-slate-500 font-medium">মোট বিল্ডিং</div>
            <div class="text-3xl font-bold text-slate-800 mt-1 tabular-nums">{{ $totalBuildings }}</div>
        </div>
        <div class="rounded-3xl bg-white border border-slate-200 p-5">
            <div class="text-xs text-emerald-600 font-medium">ড্রাফট</div>
            <div class="text-3xl font-bold text-emerald-600 mt-1 tabular-nums">{{ $draftCount }}</div>
        </div>
        <div class="rounded-3xl bg-white border border-slate-200 p-5">
            <div class="text-xs text-sky-600 font-medium">মাইগ্রেটেড</div>
            <div class="text-3xl font-bold text-sky-600 mt-1 tabular-nums">{{ $migratedCount }}</div>
        </div>
        <div class="rounded-3xl bg-white border border-slate-200 p-5">
            <div class="text-xs text-slate-500 font-medium">মোট ফ্ল্যাট</div>
            <div class="text-3xl font-bold text-slate-800 mt-1 tabular-nums">{{ $totalFlats }}</div>
        </div>
        <div class="rounded-3xl bg-white border border-slate-200 p-5">
            <div class="text-xs text-amber-600 font-medium">মোট মিটার</div>
            <div class="text-3xl font-bold text-amber-600 mt-1 tabular-nums">{{ $totalMeters }}</div>
        </div>
        <div class="rounded-3xl bg-white border border-slate-200 p-5">
            <div class="text-xs text-slate-500 font-medium">রাস্তা</div>
            <div class="text-3xl font-bold text-slate-800 mt-1 tabular-nums">{{ $roadsCovered }}</div>
        </div>
    </div>

    {{-- List --}}
    @if($collections->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($collections as $item)
                <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden @if($item->status === 'migrated') opacity-75 @endif">
                    {{-- Image --}}
                    <div class="relative h-32 bg-slate-100">
                        @if($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->building_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="fas fa-building text-4xl"></i>
                            </div>
                        @endif
                        <span class="absolute top-2 right-2 text-[10px] px-2 py-0.5 rounded-full font-semibold
                            @if($item->status === 'migrated') bg-sky-100 text-sky-700
                            @else bg-amber-100 text-amber-700 @endif">
                            @if($item->status === 'migrated') মাইগ্রেটেড @else ড্রাফট @endif
                        </span>
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <div class="font-semibold text-slate-800 text-sm">{{ $item->building_name }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $item->road_name }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $item->owner_name }} • {{ $item->owner_phone }}</div>

                        <div class="flex items-center gap-3 mt-3 text-xs text-slate-500">
                            <span><i class="fas fa-layer-group mr-0.5"></i> {{ $item->floor_count }} তলা</span>
                            <span><i class="fas fa-door-open mr-0.5"></i> {{ $item->flat_count }} ফ্ল্যাট</span>
                            <span><i class="fas fa-bolt mr-0.5 text-amber-500"></i> {{ $item->meter_count }} মিটার</span>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-4 pt-3 border-t flex items-center gap-2">
                            <a href="{{ route('admin.field-data.edit', $item) }}" class="flex-1 text-center text-xs font-medium text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-lg py-1.5">
                                <i class="fas fa-edit mr-0.5"></i> এডিট
                            </a>
                            @if($item->status === 'draft')
                                <form action="{{ route('admin.field-data.migrate-one', $item) }}" method="POST" onsubmit="return confirm('এই বিল্ডিং মাইগ্রেট করতে চান?')">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg px-2.5 py-1.5">
                                        <i class="fas fa-database"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.field-data.destroy', $item) }}" method="POST" onsubmit="return confirm('মুছে ফেলতে চান?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 border border-red-200 hover:bg-red-50 rounded-lg px-2.5 py-1.5">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-sky-600 px-2"><i class="fas fa-check-circle"></i> সম্পন্ন</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-12 text-center">
            <i class="fas fa-clipboard-list text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-slate-700">এখনো কোনো ডাটা সংগ্রহ করা হয়নি</h3>
            <p class="text-slate-500 mt-1">"নতুন ডাটা সংগ্রহ" বাটনে ক্লিক করে প্রথম বিল্ডিং এর তথ্য যোগ করুন।</p>
        </div>
    @endif
</div>
@endsection
