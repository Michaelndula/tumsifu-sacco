@extends('user.layouts.master')

@section('content')
@php
    $policyPages = getContent('policy_pages.element');
@endphp
<section class="pt-120 pb-120">
	<div class="container">
        <div class="text-center mb-5">
            <a href="" class="logo mb-4 pb-1 d-inline-block">
                <img src="{{ getImage(imagePath()['logoIcon']['path'] .'/logo.png') }}" alt="logo">
            </a>
            <h2 class="title" style="font-family: Exo, sans-serif; font-weight: 700;">{{ __($pageTitle) }}</h2>
        </div>
        @php
            echo $description
        @endphp
	</div>
</section>
<div class="footer-wrapper">
    <ul class="links d-flex flex-wrap justify-content-center">
        @foreach ($policyPages as $policyPage)
            <a href="{{ route('policy', [$policyPage, slug($policyPage->data_values->title)]) }}">
                {{ __($policyPage->data_values->title) }}@if(!$loop->last), @endif
            </a>
        @endforeach
    </ul>
</div>
@endsection

@push('style')
<style>
    .footer-wrapper {
        text-align: center;
        border-top: 1px solid #e4e4e6;
        padding: 8px 0;
    }
    .footer-wrapper .links {
        gap: 5px 25px
    }
    .footer-wrapper .links a {
        color: rgb(92, 92, 92);
    }
    .footer-wrapper .links a:hover {
        text-decoration: underline
    }
    .logo img{
        max-width: 170px;
        max-height: 60px;
        object-fit: contain
    }
</style>
    
@endpush