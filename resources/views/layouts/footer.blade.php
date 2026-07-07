@php
    $site = \App\Models\SiteSetting::cached();
    $contact = \App\Models\ContactInfo::cached();
@endphp
 <!-- ==================== FOOTER ==================== -->
    <footer class="bg-slate-950 text-slate-400 py-16 text-sm">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-12 gap-y-12">
            <div class="md:col-span-5">
                <div class="flex items-center gap-3 text-white mb-4">
                    <div class="w-9 h-9 bg-white/10 backdrop-blur flex items-center justify-center rounded-2xl overflow-hidden">
                        <img src="{{ $site->logo_url }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                    </div>
                    <span class="font-bold text-2xl heading-serif tracking-tight text-white">চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা</span>
                </div>
                <p class="max-w-xs text-slate-500">স্বচ্ছতা, সম্প্রদায় ও উন্নয়নের প্রতীক। ২০১৮ সাল থেকে চৌধুরীপাড়ার পাশে।</p>

                <div class="flex gap-4 mt-7 text-xl">
                    @if($site->facebook_link)
                        <a href="{{ $site->facebook_link }}" target="_blank" rel="noopener" class="hover:text-white"><i class="fab fa-facebook"></i></a>
                    @endif
                    @if($site->whatsapp_link)
                        <a href="{{ $site->whatsapp_link }}" target="_blank" rel="noopener" class="hover:text-white"><i class="fab fa-whatsapp"></i></a>
                    @endif
                    @if($site->youtube_link)
                        <a href="{{ $site->youtube_link }}" target="_blank" rel="noopener" class="hover:text-white"><i class="fab fa-youtube"></i></a>
                    @endif
                </div>
            </div>

            <div class="md:col-span-3">
                <div class="font-semibold text-white mb-4 tracking-wide text-xs">দ্রুত লিঙ্ক</div>
                <div class="space-y-[10px] text-[13.5px]">
                    <a href="#coverage" class="block hover:text-white">আওতাধীন এলাকা</a>
                    <a href="#notices" class="block hover:text-white">নোটিশ বোর্ড</a>
                    <a href="#gallery" class="block hover:text-white">গ্যালারি</a>
                </div>
            </div>

            <div class="md:col-span-4">
                <div class="font-semibold text-white mb-4 tracking-wide text-xs">যোগাযোগ</div>
                <div class="text-[13.5px] leading-relaxed">
                    @if($site->footer_address)
                        <span class="whitespace-pre-line">{{ $site->footer_address }}</span><br>
                    @endif
                    @if($contact->phone)
                        হটলাইন: <a href="tel:{{ $contact->phone }}" class="text-emerald-400 hover:underline">{{ $contact->phone }}</a><br>
                    @endif
                    @if($contact->email)
                        ইমেইল: <a href="mailto:{{ $contact->email }}" class="text-emerald-400 hover:underline">{{ $contact->email }}</a>
                    @endif
                </div>
                <div class="text-[11px] mt-7 text-slate-600">© {{ date('Y') }} চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা। সকল অধিকার সংরক্ষিত।</div>
            </div>
        </div>
    </footer>
