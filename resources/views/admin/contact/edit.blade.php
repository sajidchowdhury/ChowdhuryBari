@extends('admin.layout')

@section('title', 'Get In Touch')
@section('page-title', 'Get In Touch — যোগাযোগ')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div>
        <h1 class="text-3xl font-semibold">Get In Touch</h1>
        <p class="text-slate-600 mt-2">Set the contact details shown in the public "যোগাযোগ করুন" section. The contact form emails submissions to the recipient address below.</p>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.contact.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Office Address</label>
                    <input type="text" name="address" value="{{ old('address', $contact->address) }}"
                           placeholder="e.g. চৌধুরীপাড়া"
                           class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Hotline / Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $contact->phone) }}"
                           placeholder="e.g. ০১৭১১-২২৩৩৪৪"
                           class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Public Email</label>
                    <input type="email" name="email" value="{{ old('email', $contact->email) }}"
                           placeholder="e.g. info@chowdhuripara.org"
                           class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">WhatsApp Number</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $contact->whatsapp) }}"
                           placeholder="e.g. 8801711223344"
                           class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    <p class="text-xs text-slate-500 mt-1">Digits only with country code. Shown as a wa.me link.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Office Hours</label>
                <input type="text" name="office_hours" value="{{ old('office_hours', $contact->office_hours) }}"
                       placeholder="e.g. সকাল ৮টা — রাত ১০টা"
                       class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
            </div>

            <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4">
                <label class="block text-sm font-medium text-amber-800">Recipient Email (where form submissions are sent) <i class="fas fa-info-circle ml-1"></i></label>
                <input type="email" name="recipient_email" value="{{ old('recipient_email', $contact->recipient_email) }}"
                       placeholder="e.g. admin@chowdhuripara.org"
                       class="mt-1.5 w-full rounded-2xl border border-amber-300 bg-white px-4 py-2.5 text-sm">
                <p class="text-xs text-amber-700 mt-1.">When a visitor submits the contact form, the message is emailed here. Leave blank to fall back to the public email above.</p>
                <p class="text-xs text-amber-700 mt-1">⚠️ Mail sending must be configured in <code>.env</code> (<code>MAIL_MAILER=smtp</code> + SMTP credentials). Default is <code>log</code> (messages logged, not sent).</p>
            </div>

            <div>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="form_active" value="1" @checked(old('form_active', $contact->form_active)) class="rounded"> Contact form active (accept submissions on website)
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.dashboard') }}" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
