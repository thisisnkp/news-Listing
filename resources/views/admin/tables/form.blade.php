@extends('layouts.admin')

@section('title', isset($table) ? 'Edit Package' : 'Create Package')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">{{ isset($table) ? 'Edit Package' : 'Create Package' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tables.index') }}">Packages</a></li>
                <li class="breadcrumb-item active">{{ isset($table) ? 'Edit' : 'Create' }}</li>
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
                <form action="{{ isset($table) ? route('admin.tables.update', $table) : route('admin.tables.store') }}" method="POST">
                    @csrf
                    @if(isset($table))
                    @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Package Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $table->name ?? '') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                            value="{{ old('slug', $table->slug ?? '') }}"
                            placeholder="Auto-generated if empty">
                        @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">URL-friendly identifier. Leave empty to auto-generate.</div>
                    </div>

                    <!-- Services Section -->
                    <div class="mb-3" x-data="servicesManager()">
                        <label class="form-label">Services</label>
                        <div class="border rounded p-3 bg-light">
                            <template x-for="(service, index) in services" :key="index">
                                <div class="input-group mb-2">
                                    <input type="text" :name="'services[' + index + ']'" class="form-control"
                                        x-model="services[index]" placeholder="Enter service...">
                                    <button type="button" class="btn btn-outline-danger" @click="removeService(index)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                            <button type="button" class="btn btn-outline-primary btn-sm" @click="addService()">
                                <i class="fas fa-plus me-1"></i>Add Service
                            </button>
                        </div>
                        <div class="form-text">Add services included in this package.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $table->description ?? '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <div class="input-group" style="max-width: 200px;">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="price" step="0.01" min="0"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price', $table->price ?? '') }}"
                                placeholder="Optional">
                        </div>
                        @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Leave empty if price should not be displayed.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Order Button Link <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-link"></i></span>
                            <input type="url" name="order_button_link"
                                class="form-control @error('order_button_link') is-invalid @enderror"
                                value="{{ old('order_button_link', $table->order_button_link ?? '') }}"
                                placeholder="https://example.com/order" required>
                        </div>
                        @error('order_button_link')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Link for the "Order Now" button. Users will be redirected here when clicking order.</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', $table->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        <div class="form-text">Inactive packages won't be visible on the frontend.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ isset($table) ? 'Update' : 'Create' }} Package
                        </button>
                        <a href="{{ route('admin.tables.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function servicesManager() {
            return {
                services: @json(old('services', $table->services ?? [])) || [],
                addService() {
                    this.services.push('');
                },
                removeService(index) {
                    this.services.splice(index, 1);
                }
            }
        }
    </script>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>Info
            </div>
            <div class="card-body">
                <p class="text-muted mb-2">
                    <i class="fas fa-lightbulb me-2 text-warning"></i>
                    After creating a table, you can add columns and rows.
                </p>
                <hr>
                <p class="text-muted mb-0">
                    <strong>Default Columns:</strong> Name, Price, Remark
                </p>
                <p class="text-muted">
                    You can customize columns after creation.
                </p>
            </div>
        </div>

        @if(isset($table))
        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-link me-2"></i>Quick Links
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.columns.index', $table) }}" class="btn btn-outline-info">
                        <i class="fas fa-columns me-2"></i>Manage Columns
                    </a>
                    <a href="{{ route('admin.rows.index', $table) }}" class="btn btn-outline-success">
                        <i class="fas fa-list me-2"></i>Manage Rows
                    </a>
                    <a href="{{ route('table.show', $table->slug) }}" class="btn btn-outline-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>View on Frontend
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection