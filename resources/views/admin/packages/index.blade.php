@extends('layouts.admin')

@section('title', 'Packages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Packages</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Packages</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create Package
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ route('admin.packages.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search packages by name, type, or remark..." value="{{ $search ?? '' }}">
                        @if($search ?? false)
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary" title="Clear search">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </form>

        @if($packages->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;" title="Drag to reorder"><i class="fas fa-grip-lines text-muted"></i></th>
                        <th style="width: 50px">#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Remark</th>
                        <th>Content</th>
                        <th>Status</th>
                        <th style="width: 200px">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable-packages">
                    @foreach($packages as $package)
                    <tr data-id="{{ $package->id }}">
                        <td class="drag-handle" style="cursor: grab; text-align: center;">
                            <i class="fas fa-grip-vertical text-muted"></i>
                        </td>
                        <td>{{ $package->id }}</td>
                        <td>
                            <strong>{{ $package->name }}</strong>
                        </td>
                        <td>
                            @if($package->type === 'media')
                            <span class="badge bg-purple" style="background-color: #8b5cf6 !important;">
                                <i class="fas fa-photo-video me-1"></i>Media
                            </span>
                            @else
                            <span class="badge bg-primary">
                                <i class="fas fa-box me-1"></i>Package
                            </span>
                            @endif
                        </td>
                        <td>
                            @if($package->remark)
                            <span class="text-muted">{{ Str::limit($package->remark, 40) }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($package->type === 'media')
                            <span class="badge bg-secondary">{{ $package->columns_count ?? 0 }} Columns</span>
                            @else
                            <span class="badge bg-info">{{ $package->plans_count }} Plans</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-status" type="checkbox"
                                    data-id="{{ $package->id }}"
                                    {{ $package->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @if($package->type === 'media')
                                <a href="{{ route('admin.columns.index.package', $package) }}" class="btn btn-outline-info" title="Manage Table">
                                    <i class="fas fa-table"></i>
                                </a>
                                @else
                                <a href="{{ route('admin.plans.index', $package) }}" class="btn btn-outline-info" title="Manage Plans">
                                    <i class="fas fa-list-alt"></i>
                                </a>
                                @endif
                                <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger delete-btn"
                                    data-id="{{ $package->id }}"
                                    data-name="{{ $package->name }}"
                                    data-type="{{ $package->type }}"
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

        {{ $packages->links() }}
        @else
        <div class="text-center py-5">
            @if($search ?? false)
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4>No Packages Found</h4>
            <p class="text-muted">No packages match your search "{{ $search }}".</p>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Clear Search
            </a>
            @else
            <i class="fas fa-box fa-4x text-muted mb-3"></i>
            <h4>No Packages Yet</h4>
            <p class="text-muted">Create your first package to get started.</p>
            <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Package
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
                <p class="text-danger" id="deleteWarning"><small>This will also delete all plans in this package.</small></p>
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
<style>
    .sortable-ghost {
        opacity: 0.4;
        background-color: #f0f8ff;
    }

    .sortable-chosen {
        background-color: #e3f2fd;
    }

    .drag-handle:hover {
        cursor: grabbing;
    }

    .drag-handle i:hover {
        color: #0d6efd !important;
    }
</style>
<script>
    // Initialize SortableJS for drag-drop reordering
    const sortableList = document.getElementById('sortable-packages');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                // Get the new order of IDs
                const rows = sortableList.querySelectorAll('tr[data-id]');
                const order = Array.from(rows).map(row => row.dataset.id);

                // Send to backend
                fetch('/admin/packages/reorder', {
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
                            // Show brief success feedback
                            const handle = evt.item.querySelector('.drag-handle i');
                            if (handle) {
                                handle.classList.remove('text-muted');
                                handle.classList.add('text-success');
                                setTimeout(() => {
                                    handle.classList.remove('text-success');
                                    handle.classList.add('text-muted');
                                }, 1000);
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Reorder failed:', err);
                    });
            }
        });
    }

    // Toggle Status
    document.querySelectorAll('.toggle-status').forEach(function(el) {
        el.addEventListener('change', function() {
            const id = this.dataset.id;
            fetch(`/admin/packages/${id}/toggle`, {
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
            const type = this.dataset.type;

            document.getElementById('deleteName').textContent = name;
            document.getElementById('deleteForm').action = `/admin/packages/${id}`;

            // Update warning message based on type
            const warningEl = document.getElementById('deleteWarning');
            if (type === 'media') {
                warningEl.innerHTML = '<small>This will also delete all columns and rows in this media.</small>';
            } else {
                warningEl.innerHTML = '<small>This will also delete all plans in this package.</small>';
            }

            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@endpush