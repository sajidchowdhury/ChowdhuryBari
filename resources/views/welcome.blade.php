@extends('layouts.app')

@section('title', 'চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা')
@section('description', 'চৌধুরীপাড়ার ৯০+ ভবন ও ১৫টি রাস্তার নিরাপত্তা, পরিচ্ছন্নতা ও উন্নয়নের জন্য নিবেদিত সমাজ-চালিত সংস্থা।')

@section('content')
    @include('layouts.hero')
    @include('pages.impact')
    @include('pages.about')
    @include('pages.WhatWeDo')
    @include('pages.OurArea')
    @include('pages.team')
    @include('pages.notice')
    @include('pages.gallery')
    @include('pages.TopMember')
    @include('pages.Review')
    @include('pages.MemberApplication')
    @include('pages.ContactUs')
@endsection
