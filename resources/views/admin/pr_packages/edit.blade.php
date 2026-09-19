@extends('layouts.admin')

@section('title', 'Edit PR Package — ' . $package->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Edit — {{ $package->name }}</h1>
    <a href="{{ route('admin.pr_packages.index') }}" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i> Back</a>
</div>

<form action="{{ route('admin.pr_packages.update', $package) }}" method="POST">
    @method('PUT')
    @include('admin.pr_packages._form')
</form>
@endsection
