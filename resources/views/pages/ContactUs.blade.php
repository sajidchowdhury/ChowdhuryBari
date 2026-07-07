@php $contact = \App\Models\ContactInfo::cached(); @endphp
 <!-- ==================== CONTACT ==================== -->
    <section id="contact" class="max-w-7xl mx-auto px-6 py-20">
        <div class="grid lg:grid-cols-2 gap-x-16 gap-y-12">
            <div>
                <div class="uppercase text-xs tracking-[2px] text-emerald-700 font-semibold mb-2">GET IN TOUCH</div>
                <h2 class="text-6xl tracking-[-1.6px] leading-none font-bold heading-serif mb-8">যোগাযোগ করুন</h2>

                <div class="space-y-6 text-[15px]">
                    @if($contact->address)
                        <div class="flex gap-4">
                            <i class="fas fa-map-marker-alt text-emerald-700 mt-1 w-5"></i>
                            <div>
                                <div class="font-semibold">অফিস</div>
                                <div class="text-slate-600">{!! nl2br(e($contact->address)) !!}</div>
                            </div>
                        </div>
                    @endif
                    @if($contact->phone)
                        <div class="flex gap-4">
                            <i class="fas fa-phone text-emerald-700 mt-1 w-5"></i>
                            <div>
                                <div class="font-semibold">হটলাইন</div>
                                <a href="tel:{{ $contact->phone }}" class="text-emerald-700 hover:underline">{{ $contact->phone }}</a>
                                @if($contact->office_hours)
                                    <div class="text-xs text-slate-500">{{ $contact->office_hours }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if($contact->email)
                        <div class="flex gap-4">
                            <i class="fas fa-envelope text-emerald-700 mt-1 w-5"></i>
                            <div>
                                <div class="font-semibold">ইমেইল</div>
                                <a href="mailto:{{ $contact->email }}" class="text-emerald-700 hover:underline">{{ $contact->email }}</a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-9 flex gap-3">
                    @if($contact->whatsapp_url)
                        <a href="{{ $contact->whatsapp_url }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-emerald-200 hover:bg-emerald-50 text-emerald-700 text-sm font-medium">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    @endif
                    @if($contact->phone)
                        <button onclick="copyToClipboard('{{ $contact->phone }}'); this.innerText='কপি হয়েছে'"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-emerald-200 hover:bg-emerald-50 text-emerald-700 text-sm font-medium">
                            <i class="fas fa-copy"></i> নম্বর কপি করুন
                        </button>
                    @endif
                </div>
            </div>

            @if($contact->form_active)
                <!-- Contact Form (sends email to admin-configured recipient) -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 modern-shadow">
                    @if(session('contact_success'))
                        <div class="mb-5 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700 text-sm flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>{{ session('contact_success') }}</span>
                        </div>
                    @endif
                    @if(session('contact_error'))
                        <div class="mb-5 rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700 text-sm flex items-start gap-2">
                            <i class="fas fa-exclamation-circle mt-0.5"></i>
                            <span>{{ session('contact_error') }}</span>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="mb-5 rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700 text-sm">
                            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle mr-1"></i> কিছু তথ্য অসম্পূর্ণ:</p>
                            <ul class="list-disc list-inside text-xs space-y-0.5">
                                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1.5">আপনার নাম</label>
                                <input type="text" name="name" required value="{{ old('name') }}"
                                       class="w-full border border-slate-200 focus:border-emerald-700 transition rounded-2xl px-4 py-3 text-sm outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1.5">মোবাইল নম্বর</label>
                                <input type="tel" name="phone" required value="{{ old('phone') }}"
                                       class="w-full border border-slate-200 focus:border-emerald-700 transition rounded-2xl px-4 py-3 text-sm outline-none">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5">বিষয়</label>
                            <select name="subject" class="w-full border border-slate-200 focus:border-emerald-700 transition rounded-2xl px-4 py-3 text-sm outline-none">
                                <option value="সাধারণ অনুসন্ধান" @selected(old('subject') === 'সাধারণ অনুসন্ধান')>সাধারণ অনুসন্ধান</option>
                                <option value="নিরাপত্তা সংক্রান্ত" @selected(old('subject') === 'নিরাপত্তা সংক্রান্ত')>নিরাপত্তা সংক্রান্ত</option>
                                <option value="রাস্তা/ড্রেনেজ সমস্যা" @selected(old('subject') === 'রাস্তা/ড্রেনেজ সমস্যা')>রাস্তা/ড্রেনেজ সমস্যা</option>
                                <option value="সদস্যপদ / ফি" @selected(old('subject') === 'সদস্যপদ / ফি')>সদস্যপদ / ফি</option>
                                <option value="অন্যান্য" @selected(old('subject') === 'অন্যান্য')>অন্যান্য</option>
                            </select>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5">আপনার বার্তা</label>
                            <textarea name="message" rows="4" required class="w-full border border-slate-200 focus:border-emerald-700 transition rounded-3xl px-4 py-3 text-sm outline-none resize-y">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit"
                                class="mt-5 w-full py-[15px] bg-emerald-800 hover:bg-emerald-900 active:bg-black transition-all text-white font-semibold rounded-2xl text-sm tracking-wide">
                            বার্তা পাঠান
                        </button>
                        <div class="text-center text-[11px] text-slate-400 mt-3">আমরা সাধারণত ২৪ ঘণ্টার মধ্যে উত্তর দেই</div>
                    </form>
                </div>
            @else
                <div class="bg-white rounded-3xl p-8 border border-slate-100 flex flex-col items-center justify-center text-center text-slate-400">
                    <i class="fas fa-envelope-open-text text-5xl mb-4"></i>
                    <p>বর্তমানে যোগাযোগ ফর্ম নিষ্ক্রিয় আছে।</p>
                    <p class="text-sm mt-1">উপরের ঠিকানা ও নম্বরে সরাসরি যোগাযোগ করুন।</p>
                </div>
            @endif
        </div>
    </section>
