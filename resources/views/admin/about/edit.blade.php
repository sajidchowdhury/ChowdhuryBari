@extends('admin.layout')

@section('title', 'আমাদের সম্পর্কে')
@section('page-title', 'আমাদের সম্পর্কে')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div>
        <h1 class="text-3xl font-semibold">আমাদের সম্পর্কে</h1>
        <p class="text-slate-600 mt-2">Set the headline, image and short description shown in the "আমাদের সম্পর্কে" section of the public website.</p>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.about.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Headline --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Headline <span class="text-red-500">*</span></label>
                <input type="text" name="headline" value="{{ old('headline', $about->headline) }}"
                       placeholder="e.g. আমরা কারা?"
                       class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                <p class="text-xs text-slate-500 mt-1">The big title shown at the top of the section.</p>
            </div>

            {{-- Short description --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Short Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" placeholder="একটি সংক্ষিপ্ত বিবরণ লিখুন..."
                          class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>{{ old('description', $about->description) }}</textarea>
                <p class="text-xs text-slate-500 mt-1">A short paragraph describing the organisation. Line breaks are preserved on the website.</p>
            </div>

            {{-- Image --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Image</label>

                {{-- Current image preview --}}
                <div class="mt-1.5 mb-3">
                    @if($about->image_path)
                        <div class="relative inline-block">
                            <img id="aboutImagePreview" src="{{ asset($about->image_path) }}" alt="Current about image"
                                 class="w-full max-w-sm rounded-2xl border border-slate-200 object-cover aspect-[16/13]">
                            <span class="absolute top-2 left-2 bg-slate-800/80 text-white text-[10px] px-2 py-0.5 rounded-full">Current</span>
                        </div>
                    @else
                        <img id="aboutImagePreview" src="{{ asset('img/aboutus.jpg') }}" alt="Default about image"
                             class="w-full max-w-sm rounded-2xl border border-slate-200 object-cover aspect-[16/13] opacity-70">
                        <p class="text-xs text-slate-400 mt-1">Using default image — upload one to replace it.</p>
                    @endif
                </div>

                <input type="file" name="image" accept="image/*"
                       class="w-full text-sm file:mr-4 file:py-2.5 file:px-4 file:rounded-2xl file:border-0 file:bg-slate-900 file:text-white file:font-medium hover:file:bg-slate-700 cursor-pointer"
                       onchange="previewAboutImage(event)">
                <p class="text-xs text-slate-500 mt-1">JPG, PNG, WEBP, GIF — max 5MB. Recommended aspect ratio 16:13.</p>

                @if($about->image_path)
                    <label class="inline-flex items-center gap-2 text-sm mt-3 text-red-600">
                        <input type="checkbox" name="remove_image" value="1" class="rounded"> Remove current image (revert to default)
                    </label>
                @endif
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.dashboard') }}" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">Save Changes</button>
            </div>
        </form>
    </div>

    {{-- Live preview of how it will look on the website --}}
    <div class="rounded-3xl bg-slate-50 border border-slate-200 p-6">
        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Website Preview (approximate)</div>
        <div class="grid lg:grid-cols-12 gap-x-12 items-center">
            <div class="lg:col-span-7">
                <div class="inline text-emerald-700 text-sm font-semibold tracking-[1.5px] mb-2">EST. 2018</div>
                <h2 class="text-3xl lg:text-4xl leading-none tracking-tighter font-bold mb-4">{{ old('headline', $about->headline) }}</h2>
                <div class="max-w-2xl text-sm leading-relaxed text-slate-600 whitespace-pre-line">{{ old('description', $about->description) }}</div>
            </div>
            <div class="lg:col-span-5 mt-8 lg:mt-0">
                <div class="relative rounded-2xl overflow-hidden shadow-xl ring-1 ring-slate-900/10">
                    <img id="aboutPreviewImg" src="{{ $about->image_path ? asset($about->image_path) : asset('img/aboutus.jpg') }}" alt="Preview" class="w-full aspect-[16/13] object-cover">
                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <div class="text-[10px] tracking-[2px] text-white/60">OUR HOME</div>
                        <div class="text-lg font-semibold">চৌধুরীপাড়াস্থ • সমাজ উন্নায়ন সংস্থা</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewAboutImage(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('aboutImagePreview');
            const livePreview = document.getElementById('aboutPreviewImg');
            if (preview) { preview.src = e.target.result; preview.classList.remove('opacity-70'); }
            if (livePreview) { livePreview.src = e.target.result; }
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
