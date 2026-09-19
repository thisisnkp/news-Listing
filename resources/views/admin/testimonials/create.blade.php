@extends('layouts.admin')

@section('title', 'New Testimonial')

@section('content')
<h1 class="page-title mb-3">New Testimonial</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
            @include('admin.testimonials._form')
        </form>
    </div>
</div>
@endsection
