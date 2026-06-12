@extends('layouts.admin')

@section('title', 'Testimonials')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Testimonials</h1>
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> New Testimonial
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:80px">Type</th>
                    <th style="width:64px">Avatar</th>
                    <th>Person</th>
                    <th>Excerpt</th>
                    <th style="width:80px">Rating</th>
                    <th style="width:90px">Order</th>
                    <th style="width:80px">Active</th>
                    <th style="width:140px" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($testimonials as $t)
                    <tr>
                        <td>
                            @if($t->type === 'video')
                                <span class="badge bg-info"><i class="fas fa-video me-1"></i>Video</span>
                            @else
                                <span class="badge bg-secondary"><i class="fas fa-quote-right me-1"></i>Text</span>
                            @endif
                        </td>
                        <td>
                            @if($url = $t->imageUrl())
                                <img src="{{ $url }}" alt="" style="width:42px;height:42px;border-radius:50%;object-fit:cover">
                            @else
                                <i class="fas fa-user-circle text-muted" style="font-size:2rem"></i>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $t->name }}</div>
                            <small class="text-muted">{{ $t->role }}{{ $t->role && $t->company ? ' — ' : '' }}{{ $t->company }}</small>
                        </td>
                        <td>
                            @if($t->type === 'video')
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($t->video_url, 60) }}</small>
                            @else
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($t->message, 80) }}</small>
                            @endif
                        </td>
                        <td>
                            @for($i = 0; $i < 5; $i++)
                                <i class="fas fa-star {{ $i < $t->rating ? 'text-warning' : 'text-muted' }}" style="font-size:0.8rem"></i>
                            @endfor
                        </td>
                        <td>{{ $t->sort_order }}</td>
                        <td>
                            <form action="{{ route('admin.testimonials.toggle', $t) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $t->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                    {{ $t->is_active ? 'Yes' : 'No' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this testimonial?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fas fa-comment-dots fa-2x mb-2 d-block"></i>
                            No testimonials yet — add the first one above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($testimonials->hasPages())
        <div class="card-footer">
            {{ $testimonials->links() }}
        </div>
    @endif
</div>
@endsection
