@extends('layouts.admin')

@section('title', 'Languages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Languages</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Languages</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.languages.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Language
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($languages->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Native Name</th>
                        <th>Default</th>
                        <th>Status</th>
                        <th style="width: 180px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($languages as $lang)
                    <tr>
                        <td><code>{{ $lang->code }}</code></td>
                        <td><strong>{{ $lang->name }}</strong></td>
                        <td>{{ $lang->native_name ?? '-' }}</td>
                        <td>
                            @if($lang->is_default)
                            <span class="badge bg-primary">Default</span>
                            @else
                            <button type="button" class="btn btn-sm btn-outline-secondary set-default"
                                data-id="{{ $lang->id }}">
                                Set Default
                            </button>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-status" type="checkbox"
                                    data-id="{{ $lang->id }}"
                                    {{ $lang->is_active ? 'checked' : '' }}
                                    {{ $lang->is_default ? 'disabled' : '' }}>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.languages.edit', $lang) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!$lang->is_default)
                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                data-id="{{ $lang->id }}" data-name="{{ $lang->name }}">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $languages->links() }}
        @else
        <div class="text-center py-5">
            <i class="fas fa-globe fa-4x text-muted mb-3"></i>
            <h4>No Languages</h4>
            <p class="text-muted">Add your first language to get started.</p>
            <a href="{{ route('admin.languages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Language
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
                <h5 class="modal-title">Delete Language</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteName"></strong>?</p>
                <p class="text-danger"><small>This will delete all translations in this language.</small></p>
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
    document.querySelectorAll('.toggle-status').forEach(el => {
        el.addEventListener('change', function() {
            fetch(`/admin/languages/${this.dataset.id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        this.checked = !this.checked;
                        alert(data.message);
                    }
                });
        });
    });

    // Set Default
    document.querySelectorAll('.set-default').forEach(btn => {
        btn.addEventListener('click', function() {
            fetch(`/admin/languages/${this.dataset.id}/default`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        });
    });

    // Delete
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('deleteName').textContent = this.dataset.name;
            document.getElementById('deleteForm').action = `/admin/languages/${this.dataset.id}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@endpush