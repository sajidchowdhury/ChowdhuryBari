@extends('admin.layout')

@section('title', 'নোটিশ ও ঘোষণা')
@section('page-title', 'নোটিশ ও ঘোষণা')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row justify-between gap-4 items-start">
        <div>
            <h1 class="text-3xl font-semibold">নোটিশ ও ঘোষণা</h1>
            <p class="text-slate-600 mt-2">Manage notices. Only active, non-expired notices appear on the public site.</p>
        </div>
        <button type="button"
            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-notice' }))"
            class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-medium hover:bg-slate-700 transition">
            <i class="fas fa-plus mr-2"></i> Add Notice
        </button>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if($notices->isNotEmpty())
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($notices as $notice)
                <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-block px-3 py-px text-xs font-semibold rounded-xl
                            @if(!$notice->is_active) bg-slate-100 text-slate-500
                            @elseif($notice->is_expired) bg-red-100 text-red-700
                            @else bg-emerald-100 text-emerald-700 @endif">
                            {{ $notice->type }}
                        </span>
                        <span class="text-xs text-slate-400">{{ $notice->published_at->format('M d, Y') }}</span>
                    </div>
                    <div class="font-semibold text-lg leading-tight tracking-tight">{{ $notice->headline }}</div>
                    <div class="text-sm text-slate-600 mt-2 line-clamp-2">{{ $notice->description }}</div>

                    <div class="mt-3 text-xs text-slate-500 space-y-1">
                        <div><i class="fas fa-clock"></i> {{ $notice->published_at->format('h:i A') }}</div>
                        @if($notice->active_till_date)
                            <div class="@if($notice->is_expired) text-red-600 @endif">
                                <i class="fas fa-calendar-times"></i>
                                {{ $notice->is_expired ? 'Expired' : 'Active till' }}: {{ $notice->active_till_date->format('M d, Y') }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 flex gap-2 pt-3 border-t">
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-notice-{{ $notice->id }}' }))"
                                class="flex-1 rounded-xl border border-slate-200 hover:bg-slate-50 py-2 text-xs font-medium text-slate-700">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button type="button" onclick="deleteNotice({{ $notice->id }})"
                                class="rounded-xl border border-red-200 hover:bg-red-50 px-3 py-2 text-xs font-medium text-red-600">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-12 text-center">
            <i class="fas fa-bullhorn text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-slate-700">No notices yet</h3>
            <p class="text-slate-500 mt-1">Click "Add Notice" to create your first notice.</p>
        </div>
    @endif
</div>

{{-- Create Notice modal --}}
<x-modal name="create-notice" maxWidth="2xl">
    <div class="bg-white p-6">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-6">
            <div>
                <h2 class="text-2xl font-semibold">Add Notice</h2>
                <p class="text-slate-500 mt-1 text-sm">Create a new notice. It will appear on the public site if active + not expired.</p>
            </div>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-notice' }))" class="text-slate-500 hover:text-slate-900">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('admin.notices.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Notice Type <span class="text-red-500">*</span></label>
                    <input type="text" name="type" value="{{ old('type') }}" placeholder="e.g. সাধারণ, নিরাপত্তা, পরিচ্ছন্নতা, আসন্ন" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Publish Date</label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', date('Y-m-d\TH:i')) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Headline <span class="text-red-500">*</span></label>
                <input type="text" name="headline" value="{{ old('headline') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>{{ old('description') }}</textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Active Till Date (auto-expiry)</label>
                    <input type="date" name="active_till_date" value="{{ old('active_till_date') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    <p class="text-xs text-slate-500 mt-1">Leave blank = never expires.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Sort Order</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
            </div>
            <div>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded"> Active (show on website)
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-notice' }))" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">Save Notice</button>
            </div>
        </form>
    </div>
</x-modal>

{{-- Edit Notice modals --}}
@foreach($notices as $notice)
    <x-modal name="edit-notice-{{ $notice->id }}" maxWidth="2xl">
        <div class="bg-white p-6">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-6">
                <h2 class="text-2xl font-semibold">Edit Notice</h2>
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-notice-{{ $notice->id }}' }))" class="text-slate-500 hover:text-slate-900">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.notices.update', $notice) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Notice Type <span class="text-red-500">*</span></label>
                        <input type="text" name="type" value="{{ old('type', $notice->type) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Publish Date</label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at', $notice->published_at->format('Y-m-d\TH:i')) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Headline <span class="text-red-500">*</span></label>
                    <input type="text" name="headline" value="{{ old('headline', $notice->headline) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="5" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>{{ old('description', $notice->description) }}</textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Active Till Date</label>
                        <input type="date" name="active_till_date" value="{{ old('active_till_date', $notice->active_till_date?->format('Y-m-d')) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Sort Order</label>
                        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $notice->sort_order) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                </div>
                <div>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $notice->is_active)) class="rounded"> Active (show on website)
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-notice-{{ $notice->id }}' }))" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">Save Changes</button>
                </div>
            </form>
        </div>
    </x-modal>
@endforeach

<script>
    function deleteNotice(id) {
        if (!confirm('Delete this notice?')) return;

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/notices/' + id;

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                         '<input type="hidden" name="_method" value="DELETE">';

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection
