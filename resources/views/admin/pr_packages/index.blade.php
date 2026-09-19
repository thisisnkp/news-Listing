@extends('layouts.admin')

@section('title', 'PR Packages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">PR Packages</h1>
        <small class="text-muted">Pricing cards shown on the <code>/pr-services</code> page.</small>
    </div>
    <a href="{{ route('admin.pr_packages.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> New Package
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:70px">Order</th>
                    <th>Package</th>
                    <th>Price</th>
                    <th>Features</th>
                    <th style="width:90px">Popular</th>
                    <th style="width:80px">Active</th>
                    <th style="width:120px" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages as $p)
                    <tr>
                        <td>{{ $p->sort_order }}</td>
                        <td>
                            <div class="fw-semibold">{{ $p->name }}</div>
                            <small class="text-muted text-uppercase">{{ $p->label }}</small>
                            @if($p->badge)<span class="badge bg-warning text-dark ms-1">{{ $p->badge }}</span>@endif
                        </td>
                        <td>
                            @if($p->original_price)
                                <span class="text-muted text-decoration-line-through me-1">₹{{ $p->original_price }}</span>
                            @endif
                            <span class="fw-bold">₹{{ $p->price }}</span>
                        </td>
                        <td><small class="text-muted">{{ count($p->featureList()) }} items</small></td>
                        <td>
                            @if($p->is_popular)
                                <span class="badge bg-dark"><i class="fas fa-star me-1"></i>Yes</span>
                            @else
                                <span class="badge bg-light text-dark border">No</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.pr_packages.toggle', $p) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $p->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                    {{ $p->is_active ? 'Yes' : 'No' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.pr_packages.edit', $p) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.pr_packages.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this package?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-rupee-sign fa-2x mb-2 d-block"></i>
                            No PR packages yet — add the first one above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($packages->hasPages())
        <div class="card-footer">{{ $packages->links() }}</div>
    @endif
</div>
@endsection
