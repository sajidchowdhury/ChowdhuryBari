@extends('admin.layout')

@section('title', 'Navigation & Footer')
@section('page-title', 'Navigation & Footer Settings')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div>
        <h1 class="text-3xl font-semibold">Navigation &amp; Footer</h1>
        <p class="text-slate-600 mt-2">Customize the website logo, navbar color, and footer social links + address.</p>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    {{-- ===== NAVIGATION ===== --}}
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2"><i class="fas fa-bars text-teal-600"></i> Navigation</h2>
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Logo --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Logo Image</label>
                <div class="mt-1.5 mb-3 flex items-center gap-4">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center p-2">
                        <img id="logoPreview" src="{{ $settings->logo_url }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    @if($settings->logo_path)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">Custom logo</span>
                    @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-semibold">Default logo</span>
                    @endif
                </div>
                <input type="file" name="logo" accept="image/*"
                       class="w-full text-sm file:mr-4 file:py-2.5 file:px-4 file:rounded-2xl file:border-0 file:bg-slate-900 file:text-white file:font-medium hover:file:bg-slate-700 cursor-pointer"
                       onchange="previewLogo(event)">
                <p class="text-xs text-slate-500 mt-1">PNG, SVG, JPG, WEBP — max 2MB. Square recommended.</p>
                @if($settings->logo_path)
                    <label class="inline-flex items-center gap-2 text-sm mt-3 text-red-600">
                        <input type="checkbox" name="remove_logo" value="1" class="rounded"> Remove custom logo (revert to default)
                    </label>
                @endif
            </div>

            {{-- Nav color --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Navbar Background Color</label>
                <div class="mt-1.5 flex items-center gap-3">
                    <input type="color" name="nav_color" value="{{ old('nav_color', $settings->nav_color ?: '#ffffff') }}"
                           class="w-14 h-12 rounded-xl border border-slate-300 cursor-pointer p-1"
                           oninput="navColorSwatch.style.background = this.value; navColorText.value = this.value">
                    <input type="text" id="navColorText" value="{{ old('nav_color', $settings->nav_color) }}"
                           placeholder="#ffffff (leave blank for default white)"
                           class="flex-1 rounded-2xl border border-slate-300 px-4 py-2.5 text-sm"
                           oninput="document.querySelector('input[name=nav_color]').value = this.value; navColorSwatch.style.background = this.value">
                    <div id="navColorSwatch" class="w-12 h-12 rounded-xl border border-slate-300" style="background: {{ $settings->nav_color ?: '#ffffff' }}"></div>
                    <button type="button" onclick="document.querySelector('input[name=nav_color]').value=''; document.getElementById('navColorText').value=''; navColorSwatch.style.background='#ffffff'; document.querySelector('input[type=color]').value='#ffffff'"
                            class="text-xs px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600">Reset</button>
                </div>
                <p class="text-xs text-slate-500 mt-1">Choose the navbar background. Default is white. Note: very dark colors may make the nav text hard to read.</p>
            </div>

            {{-- ===== FOOTER ===== --}}
            <div class="pt-4 border-t border-slate-200">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2"><i class="fas fa-window-minimize text-teal-600"></i> Footer</h2>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700"><i class="fab fa-whatsapp text-emerald-600 mr-1"></i> WhatsApp Link</label>
                        <input type="text" name="whatsapp_link" value="{{ old('whatsapp_link', $settings->whatsapp_link) }}"
                               placeholder="https://wa.me/8801711223344"
                               class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700"><i class="fab fa-facebook text-blue-600 mr-1"></i> Facebook Link</label>
                        <input type="text" name="facebook_link" value="{{ old('facebook_link', $settings->facebook_link) }}"
                               placeholder="https://facebook.com/..."
                               class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700"><i class="fab fa-youtube text-red-600 mr-1"></i> YouTube Link</label>
                        <input type="text" name="youtube_link" value="{{ old('youtube_link', $settings->youtube_link) }}"
                               placeholder="https://youtube.com/@..."
                               class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700">Footer Address</label>
                    <textarea name="footer_address" rows="3" placeholder="চৌধুরীপাড়া, ঢাকা"
                              class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">{{ old('footer_address', $settings->footer_address) }}</textarea>
                    <p class="text-xs text-slate-500 mt-1">Shown in the footer contact column. Line breaks preserved.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.dashboard') }}" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewLogo(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
