@extends('layouts.admin')

@section('title', isset($package) ? 'Edit Package' : 'Create Package')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">{{ isset($package) ? 'Edit Package' : 'Create Package' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.packages.index') }}">Packages</a></li>
                <li class="breadcrumb-item active">{{ isset($package) ? 'Edit' : 'Create' }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-box me-2"></i>Package Details
            </div>
            <div class="card-body">
                <form action="{{ isset($package) ? route('admin.packages.update', $package) : route('admin.packages.store') }}" method="POST">
                    @csrf
                    @if(isset($package))
                    @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="packageType" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="package" {{ old('type', $package->type ?? 'package') == 'package' ? 'selected' : '' }}>Package (Contains Plans)</option>
                            <option value="media" {{ old('type', $package->type ?? '') == 'media' ? 'selected' : '' }}>Media (Contains Table Directly)</option>
                        </select>
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <strong>Package:</strong> Contains Plans, each plan has its own table.<br>
                            <strong>Media:</strong> Contains a single table directly with an Order Now button.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Visibility <span class="text-danger">*</span></label>
                        <select name="visibility" id="packageVisibility" class="form-select @error('visibility') is-invalid @enderror" required>
                            <option value="public" {{ old('visibility', $package->visibility ?? 'public') == 'public' ? 'selected' : '' }}>Public (Visible on Homepage)</option>
                            <option value="private" {{ old('visibility', $package->visibility ?? '') == 'private' ? 'selected' : '' }}>Private (Accessible via Private Link Only)</option>
                        </select>
                        @error('visibility')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Private packages won't appear on the homepage and can only be accessed via a special link.</div>
                    </div>

                    @if(isset($package) && $package->isPrivate() && $package->private_token)
                    <div class="mb-3">
                        <label class="form-label">Private Access Link</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="privateLink" value="{{ $package->getPrivateUrl() }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyPrivateLink()">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                        <div class="form-text">Share this link to give access to this private package.</div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $package->name ?? '') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                            value="{{ old('slug', $package->slug ?? '') }}"
                            placeholder="Auto-generated if empty">
                        @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">URL-friendly identifier. Leave empty to auto-generate.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remark</label>
                        <textarea name="remark" class="form-control" rows="3" placeholder="Brief description">{{ old('remark', $package->remark ?? '') }}</textarea>
                        <div class="form-text">Optional note or description.</div>
                    </div>

                    <!-- Order Button Link (visible only for Media type) -->
                    <div class="mb-3" id="orderButtonLinkWrapper" style="{{ old('type', $package->type ?? 'package') == 'media' ? '' : 'display: none;' }}">
                        <label class="form-label">Order Now Button Link</label>
                        <input type="url" name="order_button_link" class="form-control @error('order_button_link') is-invalid @enderror"
                            value="{{ old('order_button_link', $package->order_button_link ?? '') }}"
                            placeholder="https://example.com/order">
                        @error('order_button_link')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">This button will appear at the bottom of the table on the frontend.</div>
                    </div>

                    <!-- Enabled Filters Section (visible only for Media type) -->
                    <div class="mb-3" id="filtersWrapper" style="{{ old('type', $package->type ?? 'package') == 'media' ? '' : 'display: none;' }}">
                        <label class="form-label">Table Filters</label>
                        <div class="border rounded p-3 bg-light">
                            <p class="text-muted small mb-2">Select which filters to show on the frontend table:</p>
                            @php
                            $allFilters = [
                            'da' => 'DA (Domain Authority)',
                            'dr' => 'DR (Domain Rating)',
                            'disclaimer' => 'Disclaimer',
                            'backlinks' => 'Backlinks',
                            'indexing' => 'Indexing',
                            'sort_az' => 'A-Z Sorting',
                            'sort_za' => 'Z-A Sorting',
                            ];
                            $enabledFilters = old('enabled_filters', isset($package) ? ($package->enabled_filters ?? array_keys($allFilters)) : array_keys($allFilters));
                            @endphp
                            <div class="row">
                                @foreach($allFilters as $key => $label)
                                <div class="col-6 col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            name="enabled_filters[]" value="{{ $key }}"
                                            id="filter_{{ $key }}"
                                            {{ in_array($key, $enabledFilters) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="filter_{{ $key }}">{{ $label }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-text">Unchecked filters will be hidden on the table page.</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        <div class="form-text">Inactive packages won't be visible on the frontend.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ isset($package) ? 'Update' : 'Create' }} Package
                        </button>
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>Info
            </div>
            <div class="card-body">
                <div id="infoPackage" style="{{ old('type', $package->type ?? 'package') == 'package' ? '' : 'display: none;' }}">
                    <p class="text-muted mb-2">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        Packages group related plans together. After creating a package, you can add plans to it.
                    </p>
                    <hr>
                    <p class="text-muted mb-0">
                        <strong>Structure:</strong> Package → Plans → Columns/Rows
                    </p>
                </div>
                <div id="infoMedia" style="{{ old('type', $package->type ?? 'package') == 'media' ? '' : 'display: none;' }}">
                    <p class="text-muted mb-2">
                        <i class="fas fa-photo-video me-2 text-info"></i>
                        Media contains a single table directly. Perfect for showcasing pricing or comparison tables.
                    </p>
                    <hr>
                    <p class="text-muted mb-0">
                        <strong>Structure:</strong> Media → Columns/Rows (Direct)
                    </p>
                </div>
            </div>
        </div>

        @if(isset($package))
        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-link me-2"></i>Quick Links
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($package->type === 'media')
                    <a href="{{ route('admin.columns.index.package', $package) }}" class="btn btn-outline-info">
                        <i class="fas fa-columns me-2"></i>Manage Table ({{ $package->columns()->count() }} cols, {{ $package->rows()->count() }} rows)
                    </a>
                    @else
                    <a href="{{ route('admin.plans.index', $package) }}" class="btn btn-outline-info">
                        <i class="fas fa-list-alt me-2"></i>Manage Plans ({{ $package->plans()->count() }})
                    </a>
                    @endif
                    <a href="{{ route('package.show', $package->slug) }}" class="btn btn-outline-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>View on Frontend
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('packageType');
        const orderButtonWrapper = document.getElementById('orderButtonLinkWrapper');
        const filtersWrapper = document.getElementById('filtersWrapper');
        const infoPackage = document.getElementById('infoPackage');
        const infoMedia = document.getElementById('infoMedia');

        function toggleFields() {
            const isMedia = typeSelect.value === 'media';
            orderButtonWrapper.style.display = isMedia ? '' : 'none';
            filtersWrapper.style.display = isMedia ? '' : 'none';
            infoPackage.style.display = isMedia ? 'none' : '';
            infoMedia.style.display = isMedia ? '' : 'none';
        }

        typeSelect.addEventListener('change', toggleFields);
        toggleFields();
    });

    function copyPrivateLink() {
        const linkInput = document.getElementById('privateLink');
        linkInput.select();
        document.execCommand('copy');
        alert('Private link copied to clipboard!');
    }
</script>
@endpush