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
    <a href="{{ route('admin.tables.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create Package
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($tables->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Services</th>
                        <th>Rows</th>
                        <th>Status</th>
                        <th style="width: 200px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tables as $table)
                    <tr>
                        <td>{{ $table->id }}</td>
                        <td>
                            <strong>{{ $table->name }}</strong>
                            @if($table->description)
                            <br><small class="text-muted">{{ Str::limit($table->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($table->price)
                            <span class="badge bg-success">₹{{ number_format($table->price, 2) }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ count($table->services ?? []) }}</td>
                        <td>{{ $table->rows_count }}</td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-status" type="checkbox"
                                    data-id="{{ $table->id }}"
                                    {{ $table->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.columns.index', $table) }}" class="btn btn-outline-info" title="Columns">
                                    <i class="fas fa-columns"></i>
                                </a>
                                <a href="{{ route('admin.rows.index', $table) }}" class="btn btn-outline-success" title="Rows">
                                    <i class="fas fa-list"></i>
                                </a>
                                <a href="{{ route('admin.tables.edit', $table) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger delete-btn"
                                    data-id="{{ $table->id }}"
                                    data-name="{{ $table->name }}"
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

        {{ $tables->links() }}
        @else
        <div class="text-center py-5">
            <i class="fas fa-box fa-4x text-muted mb-3"></i>
            <h4>No Packages Yet</h4>
            <p class="text-muted">Create your first package to get started.</p>
            <a href="{{ route('admin.tables.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Package
            </a>
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
                <p class="text-danger"><small>This will also delete all data in this package.</small></p>
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
<script>
    // Toggle Status
    document.querySelectorAll('.toggle-status').forEach(function(el) {
        el.addEventListener('change', function() {
            const id = this.dataset.id;
            fetch(`/admin/tables/${id}/toggle`, {
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
            document.getElementById('deleteForm').action = `/admin/tables/${id}`;

            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@endpush