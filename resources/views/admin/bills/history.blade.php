@extends('admin.layout')

@section('title', 'পরিবার সংখ্যা পরিবর্তনের ইতিহাস')
@section('page-title', 'পরিবার সংখ্যা পরিবর্তনের ইতিহাস')

@section('content')
<div class="space-y-6">
    {{-- Back link --}}
    <a href="{{ route('admin.bills.index') }}" class="inline-flex items-center gap-2 text-sm text-teal-700 hover:text-teal-800 font-medium">
        <i class="fas fa-arrow-left"></i> বিল ব্যবস্থাপনায় ফিরে যান
    </a>

    {{-- Building info --}}
    <div class="card p-6">
        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">বাড়ির নাম</div>
                <div class="font-semibold text-slate-800 mt-1">{{ $building->name }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">রাস্তা</div>
                <div class="font-semibold text-slate-800 mt-1">{{ $building->road?->name ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wide">বর্তমান বিলিং পরিবার</div>
                <div class="font-semibold text-emerald-700 mt-1 text-lg">{{ $building->effective_billing_family_count }}</div>
            </div>
        </div>
    </div>

    {{-- History table --}}
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <i class="fas fa-history text-teal-600"></i>
            <span class="font-semibold text-slate-800 text-sm">পরিবার সংখ্যা পরিবর্তনের ইতিহাস</span>
        </div>

        @if($histories->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-[11px] text-slate-500 uppercase tracking-wide">
                            <th class="px-6 py-3 font-semibold">তারিখ</th>
                            <th class="px-6 py-3 font-semibold">পূর্বের সংখ্যা</th>
                            <th class="px-6 py-3 font-semibold">নতুন সংখ্যা</th>
                            <th class="px-6 py-3 font-semibold">পরিবর্তন</th>
                            <th class="px-6 py-3 font-semibold">কারণ</th>
                            <th class="px-6 py-3 font-semibold">পরিবর্তন করেছেন</th>
                            <th class="px-6 py-3 font-semibold">বিলিং মাস</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($histories as $history)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 text-slate-600">{{ $history->created_at->format('d M Y, h:i A') }}</td>
                                <td class="px-6 py-4 text-slate-600 font-medium tabular-nums">{{ $history->previous_count }}</td>
                                <td class="px-6 py-4 font-bold tabular-nums {{ $history->new_count > $history->previous_count ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ $history->new_count }}
                                </td>
                                <td class="px-6 py-4">
                                    @php $diff = $history->new_count - $history->previous_count; @endphp
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $diff > 0 ? 'bg-rose-50 text-rose-600' : ($diff < 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-500') }}">
                                        {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-xs max-w-xs truncate">{{ $history->reason ?? '—' }}</td>
                                <td class="px-6 py-4 text-slate-600 text-xs">{{ $history->changer?->name ?? 'সিস্টেম' }}</td>
                                <td class="px-6 py-4 text-slate-600 text-xs">{{ $history->bill?->billing_month ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $histories->withQueryString()->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-history text-4xl text-slate-300 mb-3"></i>
                <div class="text-slate-500 text-sm">এই বাড়ির জন্য কোনো পরিবার সংখ্যা পরিবর্তনের রেকর্ড নেই</div>
            </div>
        @endif
    </div>
</div>
@endsection
