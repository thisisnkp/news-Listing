@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Site Settings</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Settings</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-lg-8">
            <!-- General Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cog me-2"></i>General Settings
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control"
                            value="{{ $settings['site_name'] ?? 'SmartTable CMS' }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Currency Symbol</label>
                            <input type="text" name="currency_symbol" class="form-control"
                                value="{{ $settings['currency_symbol'] ?? '₹' }}" maxlength="10">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Currency Position</label>
                            <select name="currency_position" class="form-select">
                                <option value="before" {{ ($settings['currency_position'] ?? 'before') == 'before' ? 'selected' : '' }}>Before (₹100)</option>
                                <option value="after" {{ ($settings['currency_position'] ?? 'before') == 'after' ? 'selected' : '' }}>After (100₹)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Homepage Table</label>
                        <select name="homepage_table_id" class="form-select">
                            <option value="">-- Show All Tables --</option>
                            @foreach($tables as $tbl)
                            <option value="{{ $tbl->id }}" {{ ($settings['homepage_table_id'] ?? '') == $tbl->id ? 'selected' : '' }}>
                                {{ $tbl->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">Select a table to display directly on the homepage, or leave empty to show all tables.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Order Button Text</label>
                        <input type="text" name="order_button_text" class="form-control"
                            value="{{ $settings['order_button_text'] ?? 'Order Now' }}"
                            placeholder="Order Now" maxlength="50">
                        <div class="form-text">Text displayed on the order button for all packages. Default: "Order Now"</div>
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-search me-2"></i>SEO Settings
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control"
                            value="{{ $settings['meta_title'] ?? '' }}"
                            maxlength="60">
                        <div class="form-text">Recommended: 50-60 characters</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3"
                            maxlength="160">{{ $settings['meta_description'] ?? '' }}</textarea>
                        <div class="form-text">Recommended: 150-160 characters</div>
                    </div>
                </div>
            </div>

            <!-- Language Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-globe me-2"></i>Active Languages
                </div>
                <div class="card-body">
                    @if($languages->count() > 0)
                    @foreach($languages as $lang)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox"
                            name="active_languages[]"
                            value="{{ $lang->id }}"
                            id="lang{{ $lang->id }}"
                            {{ $lang->is_active ? 'checked' : '' }}
                            {{ $lang->is_default ? 'disabled checked' : '' }}>
                        <label class="form-check-label" for="lang{{ $lang->id }}">
                            {{ $lang->name }} ({{ $lang->code }})
                            @if($lang->is_default)
                            <span class="badge bg-primary ms-1">Default</span>
                            <input type="hidden" name="active_languages[]" value="{{ $lang->id }}">
                            @endif
                        </label>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted">No languages configured. <a href="{{ route('admin.languages.create') }}">Add one</a></p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Branding -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-image me-2"></i>Branding
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Site Logo</label>
                        @if($logo = App\Models\SiteSetting::getLogo())
                        <div class="mb-2 p-3 bg-light rounded text-center">
                            <img src="{{ $logo }}" alt="Logo" style="max-height: 60px;">
                        </div>
                        @endif
                        <input type="file" name="site_logo" class="form-control" accept="image/*">
                        <div class="form-text">PNG, JPG, SVG. Max 2MB.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Site Favicon</label>
                        @if($favicon = App\Models\SiteSetting::getFavicon())
                        <div class="mb-2 p-3 bg-light rounded text-center">
                            <img src="{{ $favicon }}" alt="Favicon" style="max-height: 32px;">
                        </div>
                        @endif
                        <input type="file" name="site_favicon" class="form-control" accept=".png,.ico">
                        <div class="form-text">PNG or ICO. Max 1MB.</div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Save Settings
                </button>
            </div>
        </div>
    </div>
</form>
@endsection