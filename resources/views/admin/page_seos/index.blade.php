@extends('layouts.admin')

@section('title', 'Pages SEO')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Pages SEO</h1>
    <small class="text-muted">Manage meta title, description, OG image and other SEO fields for each main-site page.</small>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Meta Title</th>
                    <th>Meta Description</th>
                    <th style="width:80px">OG Image</th>
                    <th style="width:110px" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pages as $p)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $p->page_label }}</div>
                            <small class="text-muted">/{{ $p->page_slug === 'home' ? '' : $p->page_slug }}</small>
                        </td>
                        <td><small>{{ \Illuminate\Support\Str::limit($p->meta_title, 70) ?: '—' }}</small></td>
                        <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($p->meta_description, 100) ?: '—' }}</small></td>
                        <td>
                            @if($url = $p->ogImageUrl())
                                <img src="{{ $url }}" alt="" style="width:48px;height:32px;object-fit:cover;border-radius:4px">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.page_seos.edit', $p) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
