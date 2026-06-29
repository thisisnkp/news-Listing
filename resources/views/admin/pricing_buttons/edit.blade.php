@extends('layouts.admin')

@section('title', 'Edit Pricing Button — ' . $button->label)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Edit — {{ $button->label }}</h1>
    <a href="{{ route('admin.pricing_buttons.index') }}" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i> Back</a>
</div>

<form action="{{ route('admin.pricing_buttons.update', $button) }}" method="POST">
    @method('PUT')
    @include('admin.pricing_buttons._form')
</form>
@endsection
