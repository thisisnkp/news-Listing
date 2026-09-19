@extends('layouts.admin')

@section('title', 'Edit Testimonial')

@section('content')
<h1 class="page-title mb-3">Edit Testimonial</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.testimonials._form')
        </form>
    </div>
</div>
@endsection
