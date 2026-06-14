@extends('layouts.admin')

@section('title', 'Edit Local Page — ' . $local->city)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">{{ $local->pageLabel() }} — {{ $local->city }}</h1>
        <small class="text-muted">
            Live at <a href="{{ $local->url() }}" target="_blank"><code>{{ $local->publicPath() }}</code></a>
        </small>
    </div>
    <a href="{{ route('admin.local_seos.index', ['page' => $local->page_slug]) }}" class="btn btn-light">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<form action="{{ route('admin.local_seos.update', $local) }}" method="POST" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.local_seos._form')
</form>
@endsection
