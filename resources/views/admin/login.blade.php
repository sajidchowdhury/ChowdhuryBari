@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-3xl overflow-hidden border border-gray-200">
            <div class="px-8 py-10 sm:px-10">
                <h1 class="text-3xl font-semibold text-gray-900">অ্যাডমিন লগইন</h1>
                <p class="mt-2 text-sm text-gray-500">দয়া করে আপনার ইমেল ও পাসওয়ার্ড ব্যবহার করে লগইন করুন।</p>

                <form method="POST" action="{{ route('admin.login.post') }}" class="mt-8 space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">ইমেল</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                               class="mt-2 block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">পাসওয়ার্ড</label>
                        <input id="password" name="password" type="password" required
                               class="mt-2 block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                            আমাকে মনে রাখুন
                        </label>

                        <span class="text-sm text-gray-500">পাসওয়ার্ড ভুলে গেলে, অ্যাডমিন থেকে যোগাযোগ করুন।</span>
                    </div>

                    <div>
                        <button type="submit" class="w-full rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800">
                            লগইন করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
