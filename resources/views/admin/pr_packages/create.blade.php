@extends('layouts.admin')

@section('title', 'New PR Package')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">New PR Package</h1>
    <a href="{{ route('admin.pr_packages.index') }}" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i> Back</a>
</div>

<form action="{{ route('admin.pr_packages.store') }}" method="POST">
    @include('admin.pr_packages._form')
</form>
@endsection
