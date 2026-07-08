@extends('admin.layout')

@section('title', 'সেবা চার্জ')
@section('page-title', 'সেবা চার্জ')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row justify-between gap-4 items-start">
        <div>
            <h1 class="text-3xl font-semibold">সেবা চার্জ</h1>
            <p class="text-slate-600 mt-2">প্রতিটি সেবা একটি নির্দিষ্ট বাড়ির ধরনের জন্য কনফিগার করুন। সদস্য তার বাড়ির ধরন অনুযায়ী শুধু তার চার্জ দেখতে পাবে।</p>
        </div>
        <button type="button"
            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-charge' }))"
            class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-medium hover:bg-slate-700 transition">
            <i class="fas fa-plus mr-2"></i> নতুন সেবা যোগ করুন
        </button>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    {{-- Per-category total cards (4 building types) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($categories as $key => $label)
            @php $count = $charges->where('building_category', $key)->where('is_active', true)->count(); @endphp
            <div class="rounded-3xl bg-white border border-slate-200 p-5">
                <div class="text-xs text-slate-500 font-medium">{{ $label }}</div>
                <div class="text-3xl font-bold text-slate-800 mt-1 tabular-nums">৳ {{ number_format($totalsByCategory[$key]) }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">{{ $count }} টি সক্রিয় সেবা</div>
            </div>
        @endforeach
    </div>

    @if($charges->isNotEmpty())
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-left text-xs text-slate-500 uppercase tracking-wide">
                        <th class="px-6 py-3 font-semibold">সেবার নাম</th>
                        <th class="px-6 py-3 font-semibold">বাড়ির ধরন</th>
                        <th class="px-6 py-3 font-semibold text-right">পরিমাণ</th>
                        <th class="px-6 py-3 font-semibold text-center">স্ট্যাটাস</th>
                        <th class="px-6 py-3 font-semibold text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($charges as $charge)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $charge->name }}</div>
                                @if($charge->description)
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $charge->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs bg-sky-50 text-sky-700 px-2.5 py-1 rounded-full font-medium">{{ $charge->category_label }}</span>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-800 tabular-nums">৳ {{ number_format($charge->amount) }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($charge->is_active)
                                    <span class="text-xs bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full font-semibold">সক্রিয়</span>
                                @else
                                    <span class="text-xs bg-slate-100 text-slate-500 px-2.5 py-1 rounded-full font-semibold">নিষ্ক্রিয়</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-charge-{{ $charge->id }}' }))"
                                        class="rounded-xl border border-slate-200 hover:bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-700">
                                    <i class="fas fa-edit mr-1"></i> এডিট
                                </button>
                                <button type="button" onclick="deleteCharge({{ $charge->id }})"
                                        class="rounded-xl border border-red-200 hover:bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-12 text-center">
            <i class="fas fa-receipt text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-slate-700">কোনো সেবা চার্জ যোগ করা হয়নি</h3>
            <p class="text-slate-500 mt-1">"নতুন সেবা যোগ করুন" বাটনে ক্লিক করে প্রথম সেবা যোগ করুন।</p>
        </div>
    @endif
</div>

{{-- Create modal --}}
<x-modal name="create-charge" maxWidth="2xl">
    <div class="bg-white p-6">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-6">
            <div>
                <h2 class="text-2xl font-semibold">নতুন সেবা যোগ করুন</h2>
                <p class="text-slate-500 mt-1 text-sm">এই সেবাটি একটি নির্দিষ্ট বাড়ির ধরনের জন্য কনফিগার করুন।</p>
            </div>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-charge' }))" class="text-slate-500 hover:text-slate-900">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('admin.service-charges.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">বাড়ির ধরন <span class="text-red-500">*</span></label>
                <select name="building_category" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm bg-white" required>
                    <option value="" disabled selected>— বাড়ির ধরন নির্বাচন করুন —</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" @selected(old('building_category') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-1">শুধু এই ধরনের বাড়ির সদস্যরা এই চার্জ দেখতে পাবে।</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">সেবার নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. মাসিক সদস্য ফি, নিরাপত্তা চার্জ, পরিচ্ছন্নতা চার্জ"
                           class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">পরিমাণ (টাকা) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" min="0" value="{{ old('amount') }}" placeholder="e.g. 300"
                           class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">বিবরণ (ঐচ্ছিক)</label>
                <textarea name="description" rows="2" placeholder="এই চার্জের বিস্তারিত..."
                          class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">{{ old('description') }}</textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">ক্রম (sort order)</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}"
                           class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    <p class="text-xs text-slate-500 mt-1">কম নম্বর আগে দেখাবে।</p>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded"> সক্রিয় (সদস্যদের ড্যাশবোর্ডে দেখাবে)
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-charge' }))" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">বাতিল</button>
                <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">সেবা যোগ করুন</button>
            </div>
        </form>
    </div>
</x-modal>

{{-- Edit modals --}}
@foreach($charges as $charge)
    <x-modal name="edit-charge-{{ $charge->id }}" maxWidth="2xl">
        <div class="bg-white p-6">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-6">
                <h2 class="text-2xl font-semibold">সেবা এডিট করুন</h2>
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-charge-{{ $charge->id }}' }))" class="text-slate-500 hover:text-slate-900">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.service-charges.update', $charge) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-700">বাড়ির ধরন <span class="text-red-500">*</span></label>
                    <select name="building_category" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm bg-white" required>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" @selected(old('building_category', $charge->building_category) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">সেবার নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $charge->name) }}"
                               class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">পরিমাণ (টাকা) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" min="0" value="{{ old('amount', $charge->amount) }}"
                               class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">বিবরণ (ঐচ্ছিক)</label>
                    <textarea name="description" rows="2"
                              class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">{{ old('description', $charge->description) }}</textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">ক্রম (sort order)</label>
                        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $charge->sort_order) }}"
                               class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $charge->is_active)) class="rounded"> সক্রিয়
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-charge-{{ $charge->id }}' }))" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">বাতিল</button>
                    <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">পরিবর্তন সংরক্ষণ</button>
                </div>
            </form>
        </div>
    </x-modal>
@endforeach

<script>
    function deleteCharge(id) {
        if (!confirm('এই সেবা চার্জটি মুছে ফেলতে চান?')) return;

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/service-charges/' + id;

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                         '<input type="hidden" name="_method" value="DELETE">';

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection
