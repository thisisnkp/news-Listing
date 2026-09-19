@extends('layouts.admin')

@section('title', isset($language) ? 'Edit Language' : 'Add Language')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">{{ isset($language) ? 'Edit Language' : 'Add Language' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.languages.index') }}">Languages</a></li>
                <li class="breadcrumb-item active">{{ isset($language) ? 'Edit' : 'Add' }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-globe me-2"></i>Language Details
            </div>
            <div class="card-body">
                <form action="{{ isset($language) ? route('admin.languages.update', $language) : route('admin.languages.store') }}" method="POST">
                    @csrf
                    @if(isset($language))
                    @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Language Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                            value="{{ old('code', $language->code ?? '') }}"
                            placeholder="en, hi, es"
                            maxlength="10" required>
                        @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">ISO 639-1 language code (e.g., en, hi, es)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Language Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $language->name ?? '') }}"
                            placeholder="English" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Native Name</label>
                        <input type="text" name="native_name" class="form-control"
                            value="{{ old('native_name', $language->native_name ?? '') }}"
                            placeholder="हिंदी">
                        <div class="form-text">Name in the native language</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', $language->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1"
                                {{ old('is_default', $language->is_default ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">Set as Default Language</label>
                        </div>
                        <div class="form-text">This will be the fallback language.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ isset($language) ? 'Update' : 'Add' }} Language
                        </button>
                        <a href="{{ route('admin.languages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>Common Language Codes
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Language</th>
                            <th>Native</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>en</code></td>
                            <td>English</td>
                            <td>English</td>
                        </tr>
                        <tr>
                            <td><code>hi</code></td>
                            <td>Hindi</td>
                            <td>हिंदी</td>
                        </tr>
                        <tr>
                            <td><code>es</code></td>
                            <td>Spanish</td>
                            <td>Español</td>
                        </tr>
                        <tr>
                            <td><code>fr</code></td>
                            <td>French</td>
                            <td>Français</td>
                        </tr>
                        <tr>
                            <td><code>de</code></td>
                            <td>German</td>
                            <td>Deutsch</td>
                        </tr>
                        <tr>
                            <td><code>zh</code></td>
                            <td>Chinese</td>
                            <td>中文</td>
                        </tr>
                        <tr>
                            <td><code>ar</code></td>
                            <td>Arabic</td>
                            <td>العربية</td>
                        </tr>
                        <tr>
                            <td><code>pt</code></td>
                            <td>Portuguese</td>
                            <td>Português</td>
                        </tr>
                        <tr>
                            <td><code>ja</code></td>
                            <td>Japanese</td>
                            <td>日本語</td>
                        </tr>
                        <tr>
                            <td><code>ko</code></td>
                            <td>Korean</td>
                            <td>한국어</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection