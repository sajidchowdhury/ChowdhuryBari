@extends('admin.layout')

@section('title', 'আমাদের নেতৃত্ব')
@section('page-title', 'আমাদের নেতৃত্ব')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row justify-between gap-4 items-start">
        <div>
            <h1 class="text-3xl font-semibold">আমাদের নেতৃত্ব</h1>
            <p class="text-slate-600 mt-2">Manage committee members shown on the public website.</p>
        </div>
        <button type="button"
            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-member' }))"
            class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-medium hover:bg-slate-700 transition">
            <i class="fas fa-plus mr-2"></i> Add Member
        </button>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if($members->isNotEmpty())
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($members as $member)
                <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="h-44 overflow-hidden relative">
                        <img src="{{ $member->image_url }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/60"></div>
                        @if(!$member->is_active)
                            <span class="absolute top-2 right-2 text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Inactive</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="font-semibold text-lg">{{ $member->name }}</div>
                        <div class="text-emerald-700 text-sm font-medium">{{ $member->designation }}</div>
                        <div class="flex items-center justify-between text-xs text-slate-500 mt-3 pt-3 border-t">
                            <div>দায়িত্বে: {{ $member->started_from }}</div>
                            <span class="text-slate-400">Order: {{ $member->sort_order }}</span>
                        </div>
                        @if($member->phone)
                            <div class="text-xs text-slate-500 mt-1"><i class="fas fa-phone"></i> {{ $member->phone }}</div>
                        @endif
                        <div class="mt-3 flex gap-2">
                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-member-{{ $member->id }}' }))"
                                    class="flex-1 rounded-xl border border-slate-200 hover:bg-slate-50 py-2 text-xs font-medium text-slate-700">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" onclick="deleteMember({{ $member->id }})"
                                    class="rounded-xl border border-red-200 hover:bg-red-50 px-3 py-2 text-xs font-medium text-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @php
                    $memberData = [
                        'id' => $member->id,
                        'name' => $member->name,
                        'designation' => $member->designation,
                        'started_from' => $member->started_from,
                        'phone' => $member->phone,
                        'bio' => $member->bio,
                        'image_url' => $member->image_url,
                        'sort_order' => $member->sort_order,
                        'is_active' => $member->is_active,
                    ];
                @endphp
                <script type="application/json" id="member-data-{{ $member->id }}" x-ignore>{!! json_encode($memberData) !!}</script>
            @endforeach
        </div>
    @else
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-12 text-center">
            <i class="fas fa-users text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-slate-700">No members yet</h3>
            <p class="text-slate-500 mt-1">Click "Add Member" to add committee members.</p>
        </div>
    @endif
</div>

{{-- Create Member modal --}}
<x-modal name="create-member" maxWidth="2xl">
    <div class="bg-white p-6">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-6">
            <div>
                <h2 class="text-2xl font-semibold">Add Member</h2>
                <p class="text-slate-500 mt-1 text-sm">Add a committee member. They'll appear in the "আমাদের নেতৃত্ব" section.</p>
            </div>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-member' }))" class="text-slate-500 hover:text-slate-900">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Designation <span class="text-red-500">*</span></label>
                    <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. সভাপতি, সাধারণ সম্পাদক" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Started From <span class="text-red-500">*</span></label>
                    <input type="text" name="started_from" value="{{ old('started_from') }}" placeholder="e.g. ২০১৮" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="01XXXXXXXXX" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Image</label>
                    <input type="file" name="image" accept="image/*" class="mt-1.5 w-full text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Sort Order</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    <p class="text-xs text-slate-500 mt-1">Lower numbers appear first.</p>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Short Bio / Article</label>
                <textarea name="bio" rows="4" placeholder="A short article about this member — shown in the detail modal" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">{{ old('bio') }}</textarea>
            </div>
            <div>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded"> Active (show on website)
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-member' }))" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">Save Member</button>
            </div>
        </form>
    </div>
</x-modal>

{{-- Edit Member modals (one per member) --}}
@foreach($members as $member)
    <x-modal name="edit-member-{{ $member->id }}" maxWidth="2xl">
        <div class="bg-white p-6">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-6">
                <div>
                    <h2 class="text-2xl font-semibold">Edit Member</h2>
                    <p class="text-slate-500 mt-1 text-sm">{{ $member->name }}</p>
                </div>
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-member-{{ $member->id }}' }))" class="text-slate-500 hover:text-slate-900">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.members.update', $member) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                @if($member->image_path)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Current Image</label>
                        <img src="{{ $member->image_url }}" alt="{{ $member->name }}" class="h-24 w-24 rounded-2xl object-cover border border-slate-200">
                    </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $member->name) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Designation <span class="text-red-500">*</span></label>
                        <input type="text" name="designation" value="{{ old('designation', $member->designation) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Started From <span class="text-red-500">*</span></label>
                        <input type="text" name="started_from" value="{{ old('started_from', $member->started_from) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $member->phone) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Image</label>
                        <input type="file" name="image" accept="image/*" class="mt-1.5 w-full text-sm">
                        <p class="text-xs text-slate-500 mt-1">Leave blank to keep current.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Sort Order</label>
                        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $member->sort_order) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Short Bio / Article</label>
                    <textarea name="bio" rows="4" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">{{ old('bio', $member->bio) }}</textarea>
                </div>
                <div>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $member->is_active)) class="rounded"> Active (show on website)
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-member-{{ $member->id }}' }))" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">Save Changes</button>
                </div>
            </form>
        </div>
    </x-modal>
@endforeach

<script>
    function deleteMember(id) {
        if (!confirm('Delete this member?')) return;

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/members/' + id;

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                         '<input type="hidden" name="_method" value="DELETE">';

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection
