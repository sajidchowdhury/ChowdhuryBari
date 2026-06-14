@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')

<div class="p-8">
    @if(auth()->check())
        <h1 class="text-3xl font-bold heading-serif">
            Welcome back, {{ auth()->user()->name ?? 'Admin' }}!
        </h1>
        <p class="text-gray-600 mt-1">Here's what's happening in your community.</p>
    @else
        <p class="text-red-600">You are not logged in.</p>
    @endif

    <!-- বাকি ড্যাশবোর্ড কনটেন্ট এখানে রাখো -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
        <!-- তোমার কার্ডগুলো -->
    </div>

@endsection