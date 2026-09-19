@extends('layouts.admin')

@section('title', 'Plans - ' . $package->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Plans for "{{ $package->name }}"</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.packages.index') }}">Packages</a></li>
                <li class="breadcrumb-item active">Plans</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.plans.create', $package) }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create Plan
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ route('admin.plans.index', $package) }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search plans by name, slug, or description..." value="{{ $search ?? '' }}">
                        @if($search ?? false)
                        <a href="{{ route('admin.plans.index', $package) }}" class="btn btn-outline-secondary" title="Clear search">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </form>

        @if($plans->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px"></th>
                        <th style="width: 50px">#</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Services</th>
                        <th>Columns</th>
                        <th>Rows</th>
                        <th>Status</th>
                        <th style="width: 220px">Actions</th>
                    </tr>
                </thead>
                <tbody id="plansTableBody">
                    @foreach($plans as $plan)
                    <tr data-id="{{ $plan->id }}">
                        <td class="handle" style="cursor: move"><i class="fas fa-bars text-muted"></i></td>
                        <td>{{ $plan->id }}</td>
                        <td>
                            <strong>{{ $plan->name }}</strong>
                            @if($plan->description)
                            <br><small class="text-muted">{{ Str::limit($plan->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($plan->price)
                            <span class="badge bg-success">₹{{ number_format($plan->price, 2) }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ count($plan->services ?? []) }}</td>
                        <td>{{ $plan->columns_count }}</td>
                        <td>{{ $plan->rows_count }}</td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-status" type="checkbox"
                                    data-id="{{ $plan->id }}"
                                    {{ $plan->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.columns.index', $plan) }}" class="btn btn-outline-info" title="Columns">
                                    <i class="fas fa-columns"></i>
                                </a>
                                <a href="{{ route('admin.rows.index', $plan) }}" class="btn btn-outline-success" title="Rows">
                                    <i class="fas fa-list"></i>
                                </a>
                                <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger delete-btn"
                                    data-id="{{ $plan->id }}"
                                    data-name="{{ $plan->name }}"
                                    title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $plans->links() }}
        @else
        <div class="text-center py-5">
            @if($search ?? false)
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4>No Plans Found</h4>
            <p class="text-muted">No plans match your search "{{ $search }}".</p>
            <a href="{{ route('admin.plans.index', $package) }}" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Clear Search
            </a>
            @else
            <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
            <h4>No Plans Yet</h4>
            <p class="text-muted">Create your first plan for this package.</p>
            <a href="{{ route('admin.plans.create', $package) }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Plan
            </a>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteName"></strong>?</p>
                <p class="text-danger"><small>This will also delete all data in this plan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Sortable
    var el = document.getElementById('plansTableBody');
    if (el) {
        Sortable.create(el, {
            handle: '.handle',
            animation: 150,
            onEnd: function() {
                var order = [];
                document.querySelectorAll('#plansTableBody tr').forEach(function(row) {
                    order.push(row.dataset.id);
                });

                fetch('{{ route("admin.plans.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.csrfToken
                        },
                        body: JSON.stringify({
                            order: order
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Optional: Show toast
                        }
                    });
            }
        });
    }

    // Toggle Status
    document.querySelectorAll('.toggle-status').forEach(function(el) {
        el.addEventListener('change', function() {
            const id = this.dataset.id;
            fetch(`/admin/plans/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Success
                    }
                });
        });
    });

    // Delete
    document.querySelectorAll('.delete-btn').forEach(function(el) {
        el.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('deleteName').textContent = name;
            document.getElementById('deleteForm').action = `/admin/plans/${id}`;

            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@endpush