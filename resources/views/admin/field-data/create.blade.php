@extends('admin.layout')

@section('title', 'নতুন ডাটা সংগ্রহ')
@section('page-title', 'নতুন ডাটা সংগ্রহ')

@section('extra-styles')
<style>
    .form-step { display: none; }
    .form-step.active { display: block; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .step-indicator.active { background: #059669; color: white; }
    .step-indicator.completed { background: #10b981; color: white; }
    .flat-row { animation: fadeIn 0.2s ease; }
</style>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="fieldDataForm()">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.field-data.index') }}" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> তালিকায় ফিরুন
        </a>
    </div>

    {{-- Progress indicator --}}
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl font-semibold">বিল্ডিং ডাটা সংগ্রহ ফর্ম</h2>
            <span class="text-xs text-slate-400" x-text="'ধাপ ' + currentStep + ' / 5'"></span>
        </div>
        <div class="flex items-center gap-2 mb-6">
            <template x-for="i in 5" :key="i">
                <div class="flex-1 h-2 rounded-full transition-colors"
                     :class="i <= currentStep ? 'bg-emerald-500' : 'bg-slate-200'"></div>
            </template>
        </div>

        {{-- Step circles --}}
        <div class="flex items-center justify-between text-xs">
            <template x-for="(label, i) in ['রাস্তা', 'বিল্ডিং', 'মালিক', 'ফ্ল্যাট', 'সংরক্ষণ']" :key="i">
                <div class="flex flex-col items-center gap-1 flex-1">
                    <div class="step-indicator w-8 h-8 rounded-full flex items-center justify-center font-bold transition-all bg-slate-200 text-slate-500"
                         :class="{ 'active': currentStep === i + 1, 'completed': currentStep > i + 1 }"
                         x-text="currentStep > i + 1 ? '✓' : i + 1"></div>
                    <span class="text-slate-500" x-text="label"></span>
                </div>
            </template>
        </div>
    </div>

    <form action="{{ route('admin.field-data.store') }}" method="POST" enctype="multipart/form-data" id="fieldForm">
        @csrf

        {{-- STEP 1: Road --}}
        <div class="form-step rounded-3xl bg-white border border-slate-200 shadow-sm p-6" :class="{ 'active': currentStep === 1 }">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-road text-emerald-600"></i> রাস্তা নির্বাচন করুন
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">বিদ্যমান রাস্তা নির্বাচন করুন</label>
                    <select name="road_id" x-model="roadId" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm bg-white">
                        <option value="">— রাস্তা নির্বাচন করুন —</option>
                        @foreach($roads as $road)
                            <option value="{{ $road->id }}">{{ $road->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-center text-xs text-slate-400">— অথবা —</div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">নতুন রাস্তার নাম লিখুন</label>
                    <input type="text" name="new_road_name" x-model="newRoadName" placeholder="যেমন: রাস্তা-৫"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm" :disabled="roadId !== ''">
                    <p class="text-xs text-slate-500 mt-1">রাস্তা না থাকলে নতুন নাম লিখুন — মাইগ্রেশনে স্বয়ংক্রিয়ভাবে তৈরি হবে।</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" @click="nextStep()" :disabled="!roadId && !newRoadName"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 text-white text-sm font-medium rounded-xl transition">
                    পরবর্তী <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>

        {{-- STEP 2: Building --}}
        <div class="form-step rounded-3xl bg-white border border-slate-200 shadow-sm p-6" :class="{ 'active': currentStep === 2 }">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-building text-emerald-600"></i> বিল্ডিং তথ্য
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">বিল্ডিং এর নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="building_name" x-model="buildingName" required placeholder="যেমন: বাড়ি ১/ক"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">ফ্লোর সংখ্যা <span class="text-red-500">*</span></label>
                        <input type="number" name="floor_count" x-model.number="floorCount" min="1" max="50" value="1" required
                               class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">প্রতি ফ্লোরে পরিবার <span class="text-red-500">*</span></label>
                        <input type="number" name="families_per_floor" x-model.number="familiesPerFloor" min="1" max="20" value="1" required
                               class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">বিল্ডিং ক্যাটাগরি</label>
                    <select name="building_category" x-model="buildingCategory" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm bg-white">
                        <option value="">— নির্বাচন করুন —</option>
                        <option value="tin_shed">টিন শেড</option>
                        <option value="below_or_equal_4_floor">৪তলা বা নিচে</option>
                        <option value="above_4_floor">৪তলার উপরে</option>
                        <option value="shop">দোকান</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">বিল্ডিং এর ছবি</label>
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center cursor-pointer hover:border-emerald-300 transition"
                         onclick="document.getElementById('buildingImage').click()">
                        <input type="file" name="image" id="buildingImage" accept="image/*" class="hidden" onchange="document.getElementById('imagePreview').src = URL.createObjectURL(this.files[0]); document.getElementById('imagePreviewWrap').classList.remove('hidden')">
                        <i class="fas fa-camera text-3xl text-slate-400 mb-2"></i>
                        <div class="text-sm text-slate-600">ছবি তুলুন বা আপলোড করুন</div>
                        <div class="text-xs text-slate-400 mt-1">ক্লিক করুন</div>
                    </div>
                    <div id="imagePreviewWrap" class="hidden mt-3">
                        <img id="imagePreview" class="w-full max-h-48 object-cover rounded-2xl">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">গুগল ম্যাপ লোকেশন</label>
                    <button type="button" onclick="getLocation()" class="w-full py-3 bg-sky-50 hover:bg-sky-100 border border-sky-200 text-sky-700 text-sm font-medium rounded-2xl transition">
                        <i class="fas fa-map-marker-alt mr-1"></i> বর্তমান লোকেশন নিন
                    </button>
                    <div id="locationStatus" class="text-xs text-slate-400 mt-1 text-center"></div>
                    <input type="hidden" name="google_lt" id="googleLt">
                    <input type="hidden" name="google_ln" id="googleLn">
                </div>

                <div class="flex gap-4">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="has_security" value="1" class="rounded"> নিরাপত্তা গার্ড আছে
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="has_cleaning" value="1" class="rounded"> পরিচ্ছন্নতা সেবা আছে
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" @click="prevStep()" class="px-6 py-2.5 border border-slate-300 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50">
                    <i class="fas fa-arrow-left mr-1"></i> পূর্ববর্তী
                </button>
                <button type="button" @click="nextStep()" :disabled="!buildingName"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 text-white text-sm font-medium rounded-xl transition">
                    পরবর্তী <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>

        {{-- STEP 3: Owner --}}
        <div class="form-step rounded-3xl bg-white border border-slate-200 shadow-sm p-6" :class="{ 'active': currentStep === 3 }">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-user text-emerald-600"></i> মালিকের তথ্য
            </h3>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">মালিকের নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="owner_name" x-model="ownerName" required placeholder="মালিকের নাম"
                               class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">মালিকের ফোন <span class="text-red-500">*</span></label>
                        <input type="tel" name="owner_phone" x-model="ownerPhone" required placeholder="01XXXXXXXXX"
                               class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">কেয়ারটেকার নাম</label>
                        <input type="text" name="caretaker_name" x-model="caretakerName" placeholder="কেয়ারটেকার নাম"
                               class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">কেয়ারটেকার ফোন</label>
                        <input type="tel" name="caretaker_phone" x-model="caretakerPhone" placeholder="01XXXXXXXXX"
                               class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">অতিরিক্ত তথ্য</label>
                    <textarea name="extra_information" x-model="extraInfo" rows="2" placeholder="যেমন: পুরনো বাড়ি, সদস্য ২০১৮ সাল থেকে..."
                              class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" @click="prevStep()" class="px-6 py-2.5 border border-slate-300 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50">
                    <i class="fas fa-arrow-left mr-1"></i> পূর্ববর্তী
                </button>
                <button type="button" @click="nextStep()" :disabled="!ownerName || !ownerPhone"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 text-white text-sm font-medium rounded-xl transition">
                    পরবর্তী <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>

        {{-- STEP 4: Flats + Meters --}}
        <div class="form-step rounded-3xl bg-white border border-slate-200 shadow-sm p-6" :class="{ 'active': currentStep === 4 }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fas fa-door-open text-emerald-600"></i> ফ্ল্যাট ও মিটার তথ্য
                </h3>
                <button type="button" @click="autoGenerate()" class="text-xs px-3 py-1.5 bg-sky-50 text-sky-700 rounded-lg hover:bg-sky-100">
                    <i class="fas fa-magic mr-1"></i> স্বয়ংক্রিয় তৈরি করুন
                </button>
            </div>

            <p class="text-xs text-slate-500 mb-4">প্রতিটি ফ্ল্যাটের বাসিন্দা ও মিটার নম্বর লিখুন। অথবা "স্বয়ংক্রিয় তৈরি করুন" বাটনে চাপুন — ফ্লোর × পরিবার অনুযায়ী ফ্ল্যাট তৈরি হবে।</p>

            <div id="flatsContainer" class="space-y-3">
                {{-- Flat rows added by JS --}}
            </div>

            <button type="button" @click="addFlat()" class="mt-4 w-full py-2.5 border-2 border-dashed border-slate-200 hover:border-emerald-300 text-slate-500 hover:text-emerald-600 text-sm font-medium rounded-2xl transition">
                <i class="fas fa-plus mr-1"></i> আরেকটি ফ্ল্যাট যোগ করুন
            </button>

            <input type="hidden" name="flats_data" id="flatsDataInput">

            <div class="mt-6 flex justify-between">
                <button type="button" @click="prevStep()" class="px-6 py-2.5 border border-slate-300 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50">
                    <i class="fas fa-arrow-left mr-1"></i> পূর্ববর্তী
                </button>
                <button type="button" @click="nextStep()" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition">
                    পরবর্তী <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>

        {{-- STEP 5: Review + Submit --}}
        <div class="form-step rounded-3xl bg-white border border-slate-200 shadow-sm p-6" :class="{ 'active': currentStep === 5 }">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600"></i> পর্যালোচনা ও সংরক্ষণ
            </h3>

            <div class="bg-slate-50 rounded-2xl p-5 space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">রাস্তা:</span> <span class="font-medium" x-text="roadId ? roads.find(r => r.id == roadId)?.name : newRoadName"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">বিল্ডিং:</span> <span class="font-medium" x-text="buildingName"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">মালিক:</span> <span class="font-medium" x-text="ownerName + ' • ' + ownerPhone"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">ফ্লোর × পরিবার:</span> <span class="font-medium" x-text="floorCount + ' × ' + familiesPerFloor + ' = ' + (floorCount * familiesPerFloor)"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">ফ্ল্যাট সংখ্যা:</span> <span class="font-medium" x-text="flats.length + ' টি'"></span></div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" @click="prevStep()" class="px-6 py-2.5 border border-slate-300 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50">
                    <i class="fas fa-arrow-left mr-1"></i> পূর্ববর্তী
                </button>
                <button type="submit" @click="submitForm()" class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                    <i class="fas fa-save mr-1"></i> সংরক্ষণ করুন
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function fieldDataForm() {
    return {
        currentStep: 1,
        roadId: '',
        newRoadName: '',
        buildingName: '',
        floorCount: 1,
        familiesPerFloor: 1,
        buildingCategory: '',
        ownerName: '',
        ownerPhone: '',
        caretakerName: '',
        caretakerPhone: '',
        extraInfo: '',
        roads: @json($roads->map(fn($r) => ['id' => $r->id, 'name' => $r->name])),
        flats: [],

        nextStep() {
            if (this.currentStep < 5) {
                this.currentStep++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        addFlat(floor = 1, flatNumber = '', residentName = '', residentPhone = '', meterNumber = '') {
            const id = Date.now() + Math.random();
            this.flats.push({ id, floor, flatNumber, residentName, residentPhone, meterNumber });
            this.renderFlats();
        },

        removeFlat(id) {
            this.flats = this.flats.filter(f => f.id !== id);
            this.renderFlats();
        },

        autoGenerate() {
            if (!this.floorCount || !this.familiesPerFloor) {
                alert('প্রথমে ফ্লোর ও পরিবার সংখ্যা দিন');
                return;
            }
            this.flats = [];
            const letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
            for (let f = 1; f <= this.floorCount; f++) {
                for (let i = 0; i < this.familiesPerFloor; i++) {
                    const letter = letters[i] || (i + 1);
                    this.flats.push({
                        id: Date.now() + Math.random(),
                        floor: f,
                        flatNumber: `Floor ${f} - Flat ${letter}`,
                        residentName: '',
                        residentPhone: '',
                        meterNumber: ''
                    });
                }
            }
            this.renderFlats();
        },

        renderFlats() {
            const container = document.getElementById('flatsContainer');
            if (!container) return;
            container.innerHTML = '';
            const self = this;

            this.flats.forEach((flat, idx) => {
                const div = document.createElement('div');
                div.className = 'flat-row border border-slate-200 rounded-2xl p-4 space-y-3';
                div.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500">ফ্ল্যাট #${idx + 1}</span>
                        <button type="button" class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" placeholder="ফ্লোর" value="${flat.floor}" min="0" class="text-sm border border-slate-200 rounded-lg px-3 py-2">
                        <input type="text" placeholder="ফ্ল্যাট নম্বর" value="${flat.flatNumber}" class="text-sm border border-slate-200 rounded-lg px-3 py-2">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" placeholder="বাসিন্দার নাম" value="${flat.residentName}" class="text-sm border border-slate-200 rounded-lg px-3 py-2">
                        <input type="tel" placeholder="বাসিন্দার ফোন" value="${flat.residentPhone}" class="text-sm border border-slate-200 rounded-lg px-3 py-2">
                    </div>
                    <input type="text" placeholder="মিটার নম্বর" value="${flat.meterNumber}" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2">
                `;
                const inputs = div.querySelectorAll('input');
                inputs[0].onchange = (e) => self.flats[idx].floor = e.target.value;
                inputs[1].onchange = (e) => self.flats[idx].flatNumber = e.target.value;
                inputs[2].onchange = (e) => self.flats[idx].residentName = e.target.value;
                inputs[3].onchange = (e) => self.flats[idx].residentPhone = e.target.value;
                inputs[4].onchange = (e) => self.flats[idx].meterNumber = e.target.value;
                div.querySelector('button').onclick = () => self.removeFlat(flat.id);
                container.appendChild(div);
            });
        },

        submitForm() {
            // Serialize flats into the hidden input
            const data = this.flats.map(f => ({
                floor: parseInt(f.floor) || 1,
                flat_number: f.flatNumber || `Floor ${f.floor}`,
                resident_name: f.residentName || null,
                resident_phone: f.residentPhone || null,
                meter_number: f.meterNumber || null,
                provider: 'bpdb'
            }));
            document.getElementById('flatsDataInput').value = JSON.stringify(data);
        }
    };
}

// Geolocation
function getLocation() {
    const status = document.getElementById('locationStatus');
    status.textContent = 'লোকেশন নেওয়া হচ্ছে...';

    if (!navigator.geolocation) {
        status.textContent = 'আপনার ব্রাউজার জিপিএস সাপোর্ট করে না।';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            document.getElementById('googleLt').value = pos.coords.latitude;
            document.getElementById('googleLn').value = pos.coords.longitude;
            status.innerHTML = `<i class="fas fa-check-circle text-emerald-500"></i> লোকেশন সংরক্ষিত: ${pos.coords.latitude.toFixed(6)}, ${pos.coords.longitude.toFixed(6)}`;
        },
        (err) => {
            status.textContent = 'লোকেশন নেওয়া যায়নি: ' + err.message;
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}
</script>
@endsection
