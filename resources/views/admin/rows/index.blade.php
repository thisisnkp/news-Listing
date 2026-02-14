@extends('layouts.admin')

@php
// Backward compatibility: use $plan if available, else $table
$entity = $plan ?? $table;
$package = $entity->package ?? null;
@endphp

@section('title', 'Rows - ' . $entity->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Rows: {{ $entity->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                @if($package)
                <li class="breadcrumb-item"><a href="{{ route('admin.packages.index') }}">Packages</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.plans.index', $package) }}">{{ $package->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">Rows</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRowModal">
            <i class="fas fa-plus me-2"></i>Add Row
        </button>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-import me-2"></i>Import
        </button>
    </div>
</div>

<!-- Language Tabs -->
@if($languages->count() > 1)
<ul class="nav nav-tabs mb-3" id="langTabs">
    @foreach($languages as $lang)
    <li class="nav-item">
        <button class="nav-link {{ $lang->is_default ? 'active' : '' }}"
            data-bs-toggle="tab"
            data-lang="{{ $lang->code }}"
            type="button">
            {{ $lang->name }}
            @if($lang->is_default)
            <span class="badge bg-primary ms-1">Default</span>
            @endif
        </button>
    </li>
    @endforeach
</ul>
@endif

<div class="card">
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ route('admin.rows.index', $entity) }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search rows..." value="{{ $search ?? '' }}">
                        @if($search ?? false)
                        <a href="{{ route('admin.rows.index', $entity) }}" class="btn btn-outline-secondary" title="Clear search">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </form>

        @if($rows->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px">S.No</th>
                        @foreach($columns as $column)
                        <th>{{ $column->name }}</th>
                        @endforeach
                        <th style="width: 120px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    @php
                    $data = $row->getTranslatedData($defaultLanguage?->code ?? 'en');
                    @endphp
                    <tr data-row-id="{{ $row->id }}">
                        <td>{{ $rows->firstItem() + $loop->index }}</td>
                        @foreach($columns as $column)
                        <td class="editable-cell"
                            data-column="{{ $column->slug }}"
                            data-type="{{ $column->type }}"
                            data-translatable="{{ $column->is_translatable ? '1' : '0' }}">
                            @if($column->type === 'currency')
                            {{ App\Models\SiteSetting::get('currency_symbol', '₹') }}{{ number_format($data[$column->slug] ?? 0, 2) }}
                            @elseif($column->type === 'button')
                            @php
                            $btnData = $data[$column->slug] ?? '';
                            $parts = explode('|', $btnData);
                            $btnText = $parts[0] ?? 'Button';
                            $btnLink = $parts[1] ?? '#';
                            @endphp
                            <a href="{{ $btnLink }}" class="btn btn-sm btn-primary" target="_blank">{{ $btnText }}</a>
                            <br><small class="text-muted">{{ Str::limit($btnLink, 30) }}</small>
                            @else
                            {{ $data[$column->slug] ?? '-' }}
                            @endif
                        </td>
                        @endforeach
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary edit-row"
                                data-row='@json(["id" => $row->id, "data" => $row->data, "translations" => $row->translations->keyBy("language_id")])'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-row"
                                data-id="{{ $row->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $rows->links() }}
        @else
        <div class="text-center py-5">
            @if($search ?? false)
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4>No Rows Found</h4>
            <p class="text-muted">No rows match your search "{{ $search }}".</p>
            <a href="{{ route('admin.rows.index', $entity) }}" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Clear Search
            </a>
            @else
            <i class="fas fa-database fa-4x text-muted mb-3"></i>
            <h4>No Rows Yet</h4>
            <p class="text-muted">Add data rows to this table.</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRowModal">
                <i class="fas fa-plus me-2"></i>Add First Row
            </button>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Add Row Modal -->
<div class="modal fade" id="addRowModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Row</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.rows.store', $entity) }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if($languages->count() > 1)
                    <ul class="nav nav-pills mb-3">
                        @foreach($languages as $lang)
                        <li class="nav-item">
                            <button class="nav-link {{ $lang->is_default ? 'active' : '' }}"
                                type="button"
                                data-bs-toggle="pill"
                                data-bs-target="#addLang{{ $lang->id }}">
                                {{ $lang->name }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <div class="tab-content">
                        @foreach($languages as $lang)
                        <div class="tab-pane fade {{ $lang->is_default ? 'show active' : '' }}" id="addLang{{ $lang->id }}">
                            @foreach($columns as $column)
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ $column->name }}
                                    @if($column->type === 'button')
                                    <small class="text-muted">(Format: Button Text|https://link.com)</small>
                                    @endif
                                </label>
                                @if($column->is_translatable)
                                @if($column->type === 'dropdown' && !empty($column->dropdown_options))
                                <select name="translations[{{ $lang->id }}][{{ $column->slug }}]" class="form-select">
                                    <option value="">-- Select --</option>
                                    @foreach($column->dropdown_options as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                                @else
                                <input type="{{ $column->type === 'number' || $column->type === 'currency' ? 'number' : 'text' }}"
                                    name="translations[{{ $lang->id }}][{{ $column->slug }}]"
                                    class="form-control"
                                    {{ $column->type === 'number' || $column->type === 'currency' ? 'step=0.01' : '' }}
                                    placeholder="{{ $column->type === 'button' ? 'Buy Now|https://example.com' : '' }}">
                                @endif
                                @else
                                @if($loop->parent->first)
                                @if($column->type === 'dropdown' && !empty($column->dropdown_options))
                                <select name="data[{{ $column->slug }}]" class="form-select">
                                    <option value="">-- Select --</option>
                                    @foreach($column->dropdown_options as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                                @else
                                <input type="{{ $column->type === 'number' || $column->type === 'currency' ? 'number' : 'text' }}"
                                    name="data[{{ $column->slug }}]"
                                    class="form-control"
                                    {{ $column->type === 'number' || $column->type === 'currency' ? 'step=0.01' : '' }}
                                    placeholder="{{ $column->type === 'button' ? 'Buy Now|https://example.com' : '' }}">
                                @endif
                                @else
                                <input type="text" class="form-control" disabled value="(Same for all languages)">
                                @endif
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Row</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Row Modal -->
<div class="modal fade" id="editRowModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Row</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRowForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @if($languages->count() > 1)
                    <ul class="nav nav-pills mb-3">
                        @foreach($languages as $lang)
                        <li class="nav-item">
                            <button class="nav-link {{ $lang->is_default ? 'active' : '' }}"
                                type="button"
                                data-bs-toggle="pill"
                                data-bs-target="#editLang{{ $lang->id }}">
                                {{ $lang->name }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <div class="tab-content">
                        @foreach($languages as $lang)
                        <div class="tab-pane fade {{ $lang->is_default ? 'show active' : '' }}" id="editLang{{ $lang->id }}">
                            @foreach($columns as $column)
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ $column->name }}
                                    @if($column->type === 'button')
                                    <small class="text-muted">(Format: Button Text|https://link.com)</small>
                                    @endif
                                </label>
                                @if($column->is_translatable)
                                <input type="{{ $column->type === 'number' || $column->type === 'currency' ? 'number' : 'text' }}"
                                    name="translations[{{ $lang->id }}][{{ $column->slug }}]"
                                    class="form-control edit-field"
                                    data-lang="{{ $lang->id }}"
                                    data-column="{{ $column->slug }}"
                                    data-translatable="1"
                                    {{ $column->type === 'number' || $column->type === 'currency' ? 'step=0.01' : '' }}>
                                @else
                                @if($loop->parent->first)
                                <input type="{{ $column->type === 'number' || $column->type === 'currency' ? 'number' : 'text' }}"
                                    name="data[{{ $column->slug }}]"
                                    class="form-control edit-field"
                                    data-column="{{ $column->slug }}"
                                    data-translatable="0"
                                    {{ $column->type === 'number' || $column->type === 'currency' ? 'step=0.01' : '' }}>
                                @else
                                <input type="text" class="form-control" disabled value="(Same for all languages)">
                                @endif
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endforeach
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.rows.import', $entity) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Upload CSV or Excel File</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                    </div>
                    <div class="alert alert-info">
                        <strong>Column Headers:</strong><br>
                        @foreach($columns as $column)
                        <code>{{ $column->slug }}</code>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                        <hr>
                        <small>For translations, use: <code>column_langcode</code> (e.g., name_en, name_hi)</small><br>
                        <small>For buttons: <code>Button Text|https://link.com</code></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteRowModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Row</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this row?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteRowForm" method="POST">
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
    // Edit Row
    document.querySelectorAll('.edit-row').forEach(btn => {
        btn.addEventListener('click', function() {
            const rowData = JSON.parse(this.dataset.row);
            document.getElementById('editRowForm').action = `/admin/rows/${rowData.id}`;

            // Fill non-translatable fields
            document.querySelectorAll('.edit-field[data-translatable="0"]').forEach(field => {
                const column = field.dataset.column;
                field.value = rowData.data?.[column] || '';
            });

            // Fill translatable fields
            document.querySelectorAll('.edit-field[data-translatable="1"]').forEach(field => {
                const langId = field.dataset.lang;
                const column = field.dataset.column;
                const translation = rowData.translations?.[langId];
                field.value = translation?.translated_data?.[column] || '';
            });

            new bootstrap.Modal(document.getElementById('editRowModal')).show();
        });
    });

    // Delete Row
    document.querySelectorAll('.delete-row').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('deleteRowForm').action = `/admin/rows/${this.dataset.id}`;
            new bootstrap.Modal(document.getElementById('deleteRowModal')).show();
        });
    });
</script>
@endpush