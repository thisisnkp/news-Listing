@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Dashboard</h1>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stats-card bg-gradient-primary">
            <h3>{{ $stats['packages_count'] }}</h3>
            <p>Total Packages</p>
            <i class="fas fa-box icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stats-card bg-gradient-success">
            <h3>{{ $stats['plans_count'] }}</h3>
            <p>Total Plans</p>
            <i class="fas fa-file-alt icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stats-card bg-gradient-warning">
            <h3>{{ $stats['total_rows'] }}</h3>
            <p>Total Rows</p>
            <i class="fas fa-database icon"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stats-card bg-gradient-info">
            <h3>{{ $stats['languages'] }}</h3>
            <p>Active Languages</p>
            <i class="fas fa-globe icon"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-bolt me-2"></i>Quick Actions</span>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create New Package
                    </a>
                    <a href="{{ route('admin.languages.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-globe me-2"></i>Add Language
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-cog me-2"></i>Site Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-box me-2"></i>Recent Packages</span>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentPackages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Plans</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPackages as $package)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.packages.edit', $package) }}" class="text-decoration-none">
                                        {{ $package->name }}
                                    </a>
                                </td>
                                <td>{{ $package->plans_count }}</td>
                                <td>
                                    @if($package->is_active)
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No packages created yet</p>
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary btn-sm">
                        Create Your First Package
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Developer Info -->
<div class="card">
    <div class="card-body text-center py-4">
        <p class="mb-1 text-muted">SmartTable CMS</p>
        <p class="mb-0">
            Developed by <a href="https://infotechzone.in" target="_blank" class="text-primary">InfotechZone</a>
        </p>
    </div>
</div>
@endsection