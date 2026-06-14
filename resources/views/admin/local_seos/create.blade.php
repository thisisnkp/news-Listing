@extends('layouts.admin')

@section('title', 'New Local Page')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">New Local SEO Page</h1>
        <small class="text-muted">A city replica of a base page — only Hero, Client Voices &amp; FAQ are overridden; everything else inherits.</small>
    </div>
    <a href="{{ route('admin.local_seos.index', ['page' => $local->page_slug]) }}" class="btn btn-light">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<form action="{{ route('admin.local_seos.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin.local_seos._form')
</form>
@endsection
