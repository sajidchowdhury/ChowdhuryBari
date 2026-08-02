@extends('admin.layout')

@section('title', 'অ্যাপ্লিকেশন')
@section('page-title', 'অ্যাপ্লিকেশন')

@section('content')
<div class="space-y-6" x-data="{ status: '{{ $status }}' }">

    <div>
        <h1 class="text-3xl font-semibold">অ্যাপ্লিকেশন</h1>
        <p class="text-slate-600 mt-2">সদস্যদের পরিবার কমানোর আবেদন। মিটার নম্বর BPDB-তে যাচাই করে অনুমোদন বা প্রত্যাখ্যান করুন।</p>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700">{{ session('error') }}</div>
    @endif

    {{-- Status tabs --}}
    <div class="flex border-b border-slate-200">
        <a href="?status=pending" class="px-5 py-2.5 text-sm font-medium border-b-2 {{ $status === 'pending' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            অপেক্ষমাণ <span class="ml-1 text-xs bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full">{{ $counts['pending'] }}</span>
        </a>
        <a href="?status=approved" class="px-5 py-2.5 text-sm font-medium border-b-2 {{ $status === 'approved' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            অনুমোদিত <span class="ml-1 text-xs bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full">{{ $counts['approved'] }}</span>
        </a>
        <a href="?status=rejected" class="px-5 py-2.5 text-sm font-medium border-b-2 {{ $status === 'rejected' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            প্রত্যাখ্যাত <span class="ml-1 text-xs bg-red-100 text-red-700 px-1.5 py-0.5 rounded-full">{{ $counts['rejected'] }}</span>
        </a>
    </div>

    @if($applications->isNotEmpty())
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-left text-xs text-slate-500 uppercase tracking-wide">
                        <th class="px-6 py-3 font-semibold">বাড়ি</th>
                        <th class="px-6 py-3 font-semibold text-center">বর্তমান পরিবার</th>
                        <th class="px-6 py-3 font-semibold text-center">অনুরোধ করা</th>
                        <th class="px-6 py-3 font-semibold text-center">পার্থক্য</th>
                        <th class="px-6 py-3 font-semibold">তারিখ</th>
                        <th class="px-6 py-3 font-semibold text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($applications as $app)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $app->building?->name ?? '—' }}</div>
                                <div class="text-xs text-slate-400">{{ $app->building?->road?->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-medium text-slate-700 tabular-nums">{{ $app->current_family_count }}</td>
                            <td class="px-6 py-4 text-center font-bold text-emerald-700 tabular-nums">{{ $app->requested_family_count }}</td>
                            <td class="px-6 py-4 text-center">
                                @php $diff = $app->current_family_count - $app->requested_family_count; @endphp
                                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-semibold tabular-nums">−{{ $diff }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">{{ $app->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.applications.show', $app) }}"
                                   class="rounded-xl bg-slate-900 hover:bg-slate-700 px-4 py-1.5 text-xs font-medium text-white">
                                    <i class="fas fa-eye mr-1"></i> বিস্তারিত
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="rounded-3xl bg-white border border-slate-200 p-12 text-center">
            <i class="fas fa-file-alt text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-slate-700">কোনো {{ $status === 'pending' ? 'অপেক্ষমাণ' : ($status === 'approved' ? 'অনুমোদিত' : 'প্রত্যাখ্যাত') }} আবেদন নেই</h3>
            <p class="text-slate-500 mt-1">নতুন আবেদন এলে এখানে দেখা যাবে।</p>
        </div>
    @endif
</div>
@endsection
