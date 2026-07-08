@extends('admin.layout')

@section('title', 'আবেদন বিস্তারিত')
@section('page-title', 'আবেদন বিস্তারিত')

@section('content')
<div class="space-y-6 max-w-5xl">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.applications.index') }}" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> তালিকায় ফিরুন
        </a>
        <span class="text-xs px-3 py-1 rounded-full font-semibold
            @if($application->status === 'pending') bg-amber-100 text-amber-700
            @elseif($application->status === 'approved') bg-emerald-100 text-emerald-700
            @else bg-red-100 text-red-700 @endif">
            {{ $application->status_label }}
        </span>
    </div>

    {{-- Application summary --}}
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">বাড়ি</div>
                <div class="font-semibold text-slate-800 mt-1">{{ $application->building?->name ?? '—' }}</div>
                <div class="text-xs text-slate-500">{{ $application->building?->road?->name ?? '' }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">বর্তমান বিলিং পরিবার</div>
                <div class="text-2xl font-bold text-slate-800 mt-1 tabular-nums">{{ $application->current_family_count }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">অনুরোধ করা পরিবার</div>
                <div class="text-2xl font-bold text-emerald-700 mt-1 tabular-nums">{{ $application->requested_family_count }}</div>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100">
            <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">আবেদনের কারণ</div>
            <p class="text-sm text-slate-600">{{ $application->reason }}</p>
        </div>
        <div class="mt-3 text-xs text-slate-400">
            আবেদন করেছেন: {{ $application->user?->name ?? '—' }} • তারিখ: {{ $application->created_at->format('M d, Y') }}
        </div>
    </div>

    {{-- Flats + meters table (so admin can check which are vacant) --}}
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                <i class="fas fa-th-large text-emerald-600"></i> বাড়ির ফ্ল্যাট ও মিটার তালিকা
            </div>
            <span class="text-xs text-slate-400">মিটার নম্বর BPDB-তে যাচাই করুন</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs text-slate-500 uppercase tracking-wide">
                        <th class="px-4 py-3 font-semibold">ফ্লোর</th>
                        <th class="px-4 py-3 font-semibold">ফ্ল্যাট</th>
                        <th class="px-4 py-3 font-semibold">বাসিন্দা</th>
                        <th class="px-4 py-3 font-semibold">মিটার নম্বর</th>
                        <th class="px-4 py-3 font-semibold text-center">স্ট্যাটাস</th>
                        <th class="px-4 py-3 font-semibold text-right">BPDB যাচাই</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $vacantIds = $application->vacant_flat_ids ?? [];
                        $building = $application->building;
                        $flats = $building ? $building->flats()->orderBy('floor_number')->orderBy('flat_number')->get() : collect();
                    @endphp
                    @foreach($flats as $flat)
                        @php
                            $meter = $flat->meters->first();
                            $isVacant = in_array($flat->id, $vacantIds);
                            $isActive = $flat->isFamilyActive();
                        @endphp
                        <tr class="@if($isVacant) bg-amber-50/50 @endif">
                            <td class="px-4 py-3 text-slate-500">{{ $flat->floor_number ?? '—' }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">
                                {{ $flat->flat_number }}
                                @if($isVacant)
                                    <span class="ml-1 text-[10px] bg-amber-200 text-amber-800 px-1.5 py-0.5 rounded-full font-semibold">আবেদনে খালি</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $flat->resident_name ?: '—' }}
                                @if($flat->resident_phone)<div class="text-xs text-slate-400">{{ $flat->resident_phone }}</div>@endif
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-700">
                                @if($meter)
                                    {{ $meter->meter_number }}
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(!$flat->is_active)
                                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-semibold">খালি</span>
                                @elseif($isActive)
                                    <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-semibold">সক্রিয়</span>
                                @else
                                    <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-semibold">মিটার নিষ্ক্রিয়</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($meter && $meter->meter_number)
                                    <a href="https://www.bpdb.gov.bd/bill/check" target="_blank" rel="noopener"
                                       onclick="setTimeout(()=>{ navigator.clipboard && navigator.clipboard.writeText('{{ $meter->meter_number }}') }, 200)"
                                       class="inline-flex items-center gap-1 text-xs text-sky-600 hover:text-sky-800 font-medium">
                                        <i class="fas fa-external-link-alt"></i> BPDB চেক করুন
                                    </a>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Approve / Reject (only if pending) --}}
    @if($application->status === 'pending')
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6">
            <div class="font-semibold text-slate-800 mb-4 flex items-center gap-2 text-sm">
                <i class="fas fa-gavel text-amber-600"></i> সিদ্ধান্ত নিন
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                {{-- Approve --}}
                <form action="{{ route('admin.applications.approve', $application) }}" method="POST" class="space-y-3">
                    @csrf
                    <label class="block text-xs font-semibold text-slate-500">অনুমোদন নোট (ঐচ্ছিক)</label>
                    <textarea name="admin_notes" rows="2" placeholder="যেমন: মিটার যাচাই করা হয়েছে..."
                              class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm"></textarea>
                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-2xl text-sm">
                        <i class="fas fa-check mr-1"></i> অনুমোদন করুন (বিলিং = {{ $application->requested_family_count }})
                    </button>
                </form>
                {{-- Reject --}}
                <form action="{{ route('admin.applications.reject', $application) }}" method="POST" class="space-y-3">
                    @csrf
                    <label class="block text-xs font-semibold text-slate-500">প্রত্যাখ্যান কারণ (ঐচ্ছিক)</label>
                    <textarea name="admin_notes" rows="2" placeholder="যেমন: মিটার এখনো সক্রিয়..."
                              class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm"></textarea>
                    <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-2xl text-sm">
                        <i class="fas fa-times mr-1"></i> প্রত্যাখ্যান করুন
                    </button>
                </form>
            </div>
        </div>
    @else
        {{-- Already reviewed — show admin notes --}}
        @if($application->admin_notes || $application->reviewer)
            <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6">
                <div class="font-semibold text-slate-800 mb-3 text-sm flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-slate-400"></i> রিভিউ তথ্য
                </div>
                <div class="text-sm text-slate-600 space-y-1">
                    @if($application->reviewer)<div>রিভিউ করেছেন: <strong>{{ $application->reviewer?->name }}</strong></div>@endif
                    @if($application->reviewed_at)<div>তারিখ: {{ $application->reviewed_at->format('M d, Y H:i') }}</div>@endif
                    @if($application->admin_notes)<div class="mt-2 p-3 bg-slate-50 rounded-xl">{{ $application->admin_notes }}</div>@endif
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
