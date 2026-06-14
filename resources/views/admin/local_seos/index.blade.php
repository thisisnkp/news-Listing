@extends('layouts.admin')

@section('title', 'Local SEO')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Local SEO</h1>
        <small class="text-muted">City landing pages for Home, PR Services &amp; Studio.</small>
    </div>
    <a href="{{ route('admin.local_seos.create', ['page' => $page ?? 'home']) }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Create City Page
    </a>
</div>

{{-- Page filter tabs --}}
<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !$page ? 'active' : '' }}" href="{{ route('admin.local_seos.index') }}">
            All <span class="badge bg-light text-dark ms-1">{{ $counts->sum() }}</span>
        </a>
    </li>
    @foreach(\App\Models\LocalSeo::PAGES as $slug => $label)
        <li class="nav-item">
            <a class="nav-link {{ $page === $slug ? 'active' : '' }}" href="{{ route('admin.local_seos.index', ['page' => $slug]) }}">
                {{ $label }} <span class="badge bg-light text-dark ms-1">{{ $counts[$slug] ?? 0 }}</span>
            </a>
        </li>
    @endforeach
</ul>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:140px">Page</th>
                    <th>City</th>
                    <th>URL</th>
                    <th>Meta title</th>
                    <th style="width:80px">Active</th>
                    <th style="width:120px" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locals as $l)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $l->pageLabel() }}</span></td>
                        <td class="fw-semibold">{{ $l->city }}</td>
                        <td>
                            <a href="{{ $l->url() }}" target="_blank" class="text-decoration-none">
                                <code>{{ $l->publicPath() }}</code> <i class="fas fa-external-link-alt fa-xs"></i>
                            </a>
                        </td>
                        <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($l->meta_title, 50) ?: '—' }}</small></td>
                        <td>
                            @if($l->is_active)
                                <span class="badge bg-success">Live</span>
                            @else
                                <span class="badge bg-light text-dark border">Off</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.local_seos.edit', $l) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.local_seos.destroy', $l) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this city page?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-map-marked-alt fa-2x mb-2 d-block"></i>
                            No city pages yet — click “Create City Page” to add one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($locals->hasPages())
        <div class="card-footer">{{ $locals->links() }}</div>
    @endif
</div>
@endsection
