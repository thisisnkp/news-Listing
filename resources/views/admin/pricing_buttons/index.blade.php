@extends('layouts.admin')

@section('title', 'Pricing Buttons')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Pricing Buttons</h1>
        <small class="text-muted">The clickable buttons shown on the <a href="{{ url('/pricing') }}" target="_blank"><code>/pricing</code></a> page.</small>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('admin.pricing_buttons.import') }}" method="POST" onsubmit="return confirm('Create a button for each public package? Existing ones are skipped.');">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">
                <i class="fas fa-download me-1"></i> Import from packages
            </button>
        </form>
        <a href="{{ route('admin.pricing_buttons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Button
        </a>
    </div>
</div>

<div class="alert alert-info py-2 small">
    <i class="fas fa-info-circle me-1"></i>
    The /pricing page shows these buttons. If the list is empty, it automatically falls back to showing your packages —
    click <strong>Import from packages</strong> to turn those into editable buttons.
</div>

@if($buttons->count())
    {{-- Live preview of the pill cloud --}}
    <div class="card mb-3">
        <div class="card-header"><i class="fas fa-eye me-2"></i>Preview</div>
        <div class="card-body d-flex flex-wrap gap-2">
            @foreach($buttons as $b)
                <span class="badge rounded-pill {{ $b->is_active ? 'text-bg-light border' : 'text-bg-light border opacity-50' }}" style="padding:.6rem 1rem;font-size:.85rem">
                    @if($b->icon)<i class="{{ $b->icon }} me-1 text-danger"></i>@endif{{ $b->label }}
                </span>
            @endforeach
        </div>
    </div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:70px">Order</th>
                    <th>Label</th>
                    <th>URL</th>
                    <th style="width:80px">New tab</th>
                    <th style="width:80px">Active</th>
                    <th style="width:120px" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($buttons as $b)
                    <tr>
                        <td>{{ $b->sort_order }}</td>
                        <td>
                            <span class="fw-semibold">
                                @if($b->icon)<i class="{{ $b->icon }} me-1 text-danger"></i>@endif{{ $b->label }}
                            </span>
                        </td>
                        <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($b->url, 55) }}</small></td>
                        <td>{!! $b->new_tab ? '<i class="fas fa-check text-success"></i>' : '<span class="text-muted">—</span>' !!}</td>
                        <td>
                            <form action="{{ route('admin.pricing_buttons.toggle', $b) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $b->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                    {{ $b->is_active ? 'Yes' : 'No' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.pricing_buttons.edit', $b) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.pricing_buttons.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this button?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-link fa-2x mb-2 d-block"></i>
                            No buttons yet. Until you add some, the /pricing page falls back to showing your packages.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($buttons->hasPages())
        <div class="card-footer">{{ $buttons->links() }}</div>
    @endif
</div>
@endsection
