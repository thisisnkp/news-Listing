@extends('layouts.admin')

@php
$entity = $package;
@endphp

@section('title', 'Columns - ' . $entity->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Columns: {{ $entity->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.packages.index') }}">Packages</a></li>
                <li class="breadcrumb-item active">{{ $package->name }}</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.rows.index.package', $package) }}" class="btn btn-success">
            <i class="fas fa-list me-2"></i>Manage Rows
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-columns me-2"></i>Columns</span>
                <span class="badge bg-primary">{{ $columns->count() }} columns</span>
            </div>
            <div class="card-body">
                @if($columns->count() > 0)
                <style>
                    .sticky-table-wrapper {
                        max-height: 500px;
                        overflow: auto;
                        position: relative;
                    }

                    .sticky-table thead th {
                        position: sticky;
                        top: 0;
                        background: #f8f9fa;
                        z-index: 2;
                        box-shadow: 0 1px 0 #dee2e6;
                    }

                    .sticky-table tbody td:first-child,
                    .sticky-table thead th:first-child {
                        position: sticky;
                        left: 0;
                        background: #fff;
                        z-index: 1;
                    }

                    .sticky-table thead th:first-child {
                        z-index: 3;
                        background: #f8f9fa;
                    }

                    .sticky-table tbody tr:hover td:first-child {
                        background: #f8f9fa;
                    }
                </style>
                <div class="sticky-table-wrapper">
                    <table class="table table-hover align-middle sticky-table" id="columnsTable">
                        <thead>
                            <tr>
                                <th style="width: 40px"><i class="fas fa-grip-vertical text-muted"></i></th>
                                <th style="min-width: 150px">Name</th>
                                <th>Slug</th>
                                <th>Type</th>
                                <th>Translatable</th>
                                <th>Filterable</th>
                                <th style="width: 120px">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortableColumns">
                            @foreach($columns as $column)
                            <tr data-id="{{ $column->id }}">
                                <td class="drag-handle" style="cursor: move;"><i class="fas fa-grip-vertical text-muted"></i></td>
                                <td>
                                    <strong>{{ $column->name }}</strong>
                                    @if($column->type === 'dropdown' && !empty($column->dropdown_options))
                                    <br><small class="text-muted">{{ count($column->dropdown_options) }} options</small>
                                    @endif
                                </td>
                                <td><code>{{ $column->slug }}</code></td>
                                <td>
                                    <span class="badge bg-{{ $column->type == 'text' ? 'secondary' : ($column->type == 'number' ? 'info' : ($column->type == 'currency' ? 'success' : ($column->type == 'dropdown' ? 'warning' : 'primary'))) }}">
                                        {{ ucfirst($column->type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($column->is_translatable)
                                    <i class="fas fa-check text-success"></i>
                                    @else
                                    <i class="fas fa-times text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($column->is_filterable)
                                    <i class="fas fa-check text-success"></i>
                                    @else
                                    <i class="fas fa-times text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-column"
                                        data-column='@json($column)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-column"
                                        data-id="{{ $column->id }}" data-name="{{ $column->name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-columns fa-3x mb-3"></i>
                    <p>No columns yet. Add your first column.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Add Column Form -->
        <div class="card" x-data="columnForm()">
            <div class="card-header">
                <i class="fas fa-plus me-2"></i>Add Column
            </div>
            <div class="card-body">
                <form action="{{ route('admin.columns.store.package', $package) }}" method="POST" id="addColumnForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Column Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required x-model="selectedType">
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="currency">Currency</option>
                            <option value="button">Button (Name + Link)</option>
                            <option value="dropdown">Dropdown</option>
                        </select>
                        <div class="form-text">
                            <strong>Button:</strong> Enter <code>Button Text|https://link.com</code>
                        </div>

                        <!-- Button Name field -->
                        <div class="mt-2" x-show="selectedType === 'button'" x-cloak>
                            <label class="form-label">Button Name</label>
                            <input type="text" name="name_if_button" class="form-control"
                                value="{{ old('name_if_button') }}"
                                placeholder="e.g. Order Now, Buy, Subscribe">
                            <div class="form-text">Default text for the button</div>
                        </div>

                        <!-- Dropdown Options -->
                        <div class="mt-3" x-show="selectedType === 'dropdown'" x-cloak>
                            <label class="form-label">Dropdown Options</label>
                            <template x-for="(option, index) in dropdownOptions" :key="index">
                                <div class="input-group mb-2">
                                    <input type="text" :name="'dropdown_options[' + index + ']'"
                                        class="form-control" x-model="dropdownOptions[index]"
                                        placeholder="Option value">
                                    <button type="button" class="btn btn-outline-danger" @click="removeOption(index)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                            <button type="button" class="btn btn-outline-primary btn-sm" @click="addOption()">
                                <i class="fas fa-plus me-1"></i>Add Option
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_translatable" id="is_translatable">
                            <label class="form-check-label" for="is_translatable">Translatable</label>
                        </div>
                        <div class="form-text">Enable for Name, Remark fields</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_filterable" id="is_filterable">
                            <label class="form-check-label" for="is_filterable">Filterable</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_sortable" id="is_sortable" checked>
                            <label class="form-check-label" for="is_sortable">Sortable</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Add Column
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Add Default Columns -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-magic me-2"></i>Quick Add Columns
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2">Click to add columns:</p>
                <div class="d-grid gap-2">
                    <!-- Name -->
                    <button type="button" class="btn btn-outline-secondary btn-sm quick-add"
                        data-name="Name" data-type="text" data-translatable="1">
                        + Name (Text, Translatable)
                    </button>

                    <!-- Type Dropdown -->
                    <button type="button" class="btn btn-outline-warning btn-sm quick-add-dropdown"
                        data-name="Type"
                        data-options="English Media,Hindi Media,Hindi Package,English Package,Regional Package,International Media,English Newspaper,Hindi Newspaper,Print Magazine">
                        + Type (Media Types)
                    </button>

                    <!-- Price -->
                    <button type="button" class="btn btn-outline-success btn-sm quick-add"
                        data-name="Price" data-type="currency" data-filterable="1">
                        + Price (Currency)
                    </button>

                    <!-- Remark -->
                    <button type="button" class="btn btn-outline-secondary btn-sm quick-add"
                        data-name="Remark" data-type="text" data-translatable="1">
                        + Remark (Text)
                    </button>

                    <!-- Disclaimer Dropdown -->
                    <button type="button" class="btn btn-outline-warning btn-sm quick-add-dropdown"
                        data-name="Disclaimer"
                        data-options="Yes,No,Organic">
                        + Disclaimer (Yes/No/Organic)
                    </button>

                    <!-- Backlink Dropdown -->
                    <button type="button" class="btn btn-outline-warning btn-sm quick-add-dropdown"
                        data-name="Backlink"
                        data-options="No,1,2">
                        + Backlink (No/1/2)
                    </button>

                    <!-- Indexing Dropdown -->
                    <button type="button" class="btn btn-outline-warning btn-sm quick-add-dropdown"
                        data-name="Indexing"
                        data-options="Yes,No">
                        + Indexing (Yes/No)
                    </button>

                    <!-- DR -->
                    <button type="button" class="btn btn-outline-info btn-sm quick-add"
                        data-name="DR" data-type="number">
                        + DR (Number)
                    </button>

                    <!-- DA -->
                    <button type="button" class="btn btn-outline-info btn-sm quick-add"
                        data-name="DA" data-type="number">
                        + DA (Number)
                    </button>

                    <!-- Sample (Button) -->
                    <button type="button" class="btn btn-outline-primary btn-sm quick-add"
                        data-name="Sample" data-type="button">
                        + Sample (Link Button)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Column Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Column</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editColumnForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" x-data="editColumnForm()">
                    <div class="mb-3">
                        <label class="form-label">Column Name</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" id="editType" class="form-select" required x-model="editSelectedType">
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="currency">Currency</option>
                            <option value="button">Button</option>
                            <option value="dropdown">Dropdown</option>
                        </select>
                    </div>
                    <div class="mb-3" id="editButtonNameWrapper" x-show="editSelectedType === 'button'" x-cloak>
                        <label class="form-label">Button Name</label>
                        <input type="text" name="name_if_button" id="editButtonName" class="form-control"
                            placeholder="e.g. Order Now, Buy, Subscribe">
                        <div class="form-text">Default text for the button</div>
                    </div>
                    <div class="mb-3" id="editDropdownWrapper" x-show="editSelectedType === 'dropdown'" x-cloak>
                        <label class="form-label">Dropdown Options</label>
                        <template x-for="(option, index) in editDropdownOptions" :key="index">
                            <div class="input-group mb-2">
                                <input type="text" :name="'dropdown_options[' + index + ']'"
                                    class="form-control" x-model="editDropdownOptions[index]"
                                    placeholder="Option value">
                                <button type="button" class="btn btn-outline-danger" @click="editRemoveOption(index)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                        <button type="button" class="btn btn-outline-primary btn-sm" @click="editAddOption()">
                            <i class="fas fa-plus me-1"></i>Add Option
                        </button>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_translatable" id="editTranslatable">
                        <label class="form-check-label" for="editTranslatable">Translatable</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_filterable" id="editFilterable">
                        <label class="form-check-label" for="editFilterable">Filterable</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_sortable" id="editSortable">
                        <label class="form-check-label" for="editSortable">Sortable</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Column</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteName"></strong>?</p>
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
    // Alpine.js component for Add Column form
    function columnForm() {
        return {
            selectedType: '{{ old("type", "text") }}',
            dropdownOptions: [''],
            addOption() {
                this.dropdownOptions.push('');
            },
            removeOption(index) {
                this.dropdownOptions.splice(index, 1);
                if (this.dropdownOptions.length === 0) {
                    this.dropdownOptions.push('');
                }
            }
        }
    }

    // Alpine.js component for Edit Column modal
    function editColumnForm() {
        return {
            editSelectedType: 'text',
            editDropdownOptions: [''],
            editAddOption() {
                this.editDropdownOptions.push('');
            },
            editRemoveOption(index) {
                this.editDropdownOptions.splice(index, 1);
                if (this.editDropdownOptions.length === 0) {
                    this.editDropdownOptions.push('');
                }
            }
        }
    }

    // Sortable columns
    if (document.getElementById('sortableColumns')) {
        new Sortable(document.getElementById('sortableColumns'), {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function() {
                const order = Array.from(document.querySelectorAll('#sortableColumns tr')).map(tr => tr.dataset.id);
                fetch('{{ route("admin.columns.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify({
                        order: order
                    })
                });
            }
        });
    }

    // Quick Add (regular columns)
    document.querySelectorAll('.quick-add').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = document.getElementById('addColumnForm');
            form.querySelector('[name="name"]').value = this.dataset.name;
            form.querySelector('[name="type"]').value = this.dataset.type;
            form.querySelector('[name="is_translatable"]').checked = this.dataset.translatable === '1';
            form.querySelector('[name="is_filterable"]').checked = this.dataset.filterable === '1';
            form.submit();
        });
    });

    // Quick Add (dropdown columns)
    document.querySelectorAll('.quick-add-dropdown').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = document.getElementById('addColumnForm');
            form.querySelector('[name="name"]').value = this.dataset.name;
            form.querySelector('[name="type"]').value = 'dropdown';

            // Add hidden inputs for dropdown options
            const options = this.dataset.options.split(',');
            options.forEach((opt, index) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'dropdown_options[' + index + ']';
                input.value = opt.trim();
                form.appendChild(input);
            });

            form.submit();
        });
    });

    // Edit Column
    document.querySelectorAll('.edit-column').forEach(btn => {
        btn.addEventListener('click', function() {
            const column = JSON.parse(this.dataset.column);
            document.getElementById('editColumnForm').action = '{{ url("admin/columns") }}/' + column.id;
            document.getElementById('editName').value = column.name;
            document.getElementById('editType').value = column.type;
            document.getElementById('editButtonName').value = column.name_if_button || '';
            document.getElementById('editTranslatable').checked = column.is_translatable;
            document.getElementById('editFilterable').checked = column.is_filterable;
            document.getElementById('editSortable').checked = column.is_sortable;

            new bootstrap.Modal(document.getElementById('editModal')).show();
        });
    });

    // Delete Column
    document.querySelectorAll('.delete-column').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('deleteName').textContent = this.dataset.name;
            document.getElementById('deleteForm').action = '{{ url("admin/columns") }}/' + this.dataset.id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
</script>
@endpush