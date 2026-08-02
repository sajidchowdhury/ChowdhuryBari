@extends('admin.layout')

@section('title', 'গ্যালারি')
@section('page-title', 'গ্যালারি')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row justify-between gap-4 items-start">
        <div>
            <h1 class="text-3xl font-semibold">গ্যালারি</h1>
            <p class="text-slate-600 mt-2">Upload community photos. The <strong>10 most recent</strong> active images appear on the public site.</p>
        </div>
        <button type="button"
            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'upload-gallery' }))"
            class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-medium hover:bg-slate-700 transition">
            <i class="fas fa-upload mr-2"></i> Upload Image
        </button>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if($items->isNotEmpty())
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($items as $item)
                <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="relative aspect-[4/3] bg-slate-100">
                        <img src="{{ asset($item->image_path) }}" alt="{{ $item->caption }}" class="w-full h-full object-cover">
                        @if(!$item->is_active)
                            <span class="absolute top-2 left-2 bg-slate-800/80 text-white text-[10px] px-2 py-0.5 rounded-full">Inactive</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-xs text-slate-400">{{ $item->created_at->format('M d, Y') }}</span>
                            @if($item->category)
                                <span class="text-[10px] px-2 py-px rounded-full bg-emerald-100 text-emerald-700 font-semibold">{{ $item->category }}</span>
                            @endif
                        </div>
                        <div class="font-semibold text-sm leading-tight">{{ $item->caption }}</div>
                        <div class="mt-3 pt-3 border-t flex items-center justify-between">
                            <a href="{{ asset($item->image_path) }}" target="_blank" class="text-xs font-medium text-emerald-700 hover:underline">
                                <i class="fas fa-external-link-alt mr-1"></i> View
                            </a>
                            <button type="button" onclick="deleteGallery({{ $item->id }})"
                                    class="rounded-xl border border-red-200 hover:bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-12 text-center">
            <i class="fas fa-images text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-slate-700">No images yet</h3>
            <p class="text-slate-500 mt-1">Click "Upload Image" to add your first photo.</p>
        </div>
    @endif
</div>

{{-- Upload modal --}}
<x-modal name="upload-gallery" maxWidth="2xl">
    <div class="bg-white p-6">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-6">
            <div>
                <h2 class="text-2xl font-semibold">Upload Image</h2>
                <p class="text-slate-500 mt-1 text-sm">Upload a photo with a short description. It will appear on the public gallery.</p>
            </div>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'upload-gallery' }))" class="text-slate-500 hover:text-slate-900">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Image <span class="text-red-500">*</span></label>
                <input type="file" name="image" accept="image/*" required
                       class="mt-1.5 w-full text-sm file:mr-4 file:py-2.5 file:px-4 file:rounded-2xl file:border-0 file:bg-slate-900 file:text-white file:font-medium hover:file:bg-slate-700 cursor-pointer">
                <p class="text-xs text-slate-500 mt-1">JPG, PNG, WEBP, GIF — max 5MB.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Short Description (Caption) <span class="text-red-500">*</span></label>
                <input type="text" name="caption" value="{{ old('caption') }}" placeholder="e.g. বার্ষিক মিলনমেলা ২০২৬"
                       class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Category (optional)</label>
                <input type="text" name="category" value="{{ old('category') }}" placeholder="e.g. ইভেন্ট, উন্নয়ন, এলাকা, সভা"
                       class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
            </div>
            <div>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded"> Active (show on website)
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'upload-gallery' }))" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">Upload</button>
            </div>
        </form>
    </div>
</x-modal>

<script>
    function deleteGallery(id) {
        if (!confirm('Delete this image? The file will be removed permanently.')) return;

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/gallery/' + id;

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                         '<input type="hidden" name="_method" value="DELETE">';

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection
