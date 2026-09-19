@extends('layouts.admin')

@section('title', 'Leads')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Form Submissions</h1>
</div>

<div class="row g-3 mb-3">
    <div class="col-sm-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-primary"><i class="fas fa-inbox fa-2x"></i></div>
                <div>
                    <div class="h4 mb-0">{{ $stats['total'] }}</div>
                    <small class="text-muted">Total leads</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-success"><i class="fas fa-paper-plane fa-2x"></i></div>
                <div>
                    <div class="h4 mb-0">{{ $stats['mailed'] }}</div>
                    <small class="text-muted">Mail sent</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-warning"><i class="fas fa-redo fa-2x"></i></div>
                <div>
                    <div class="h4 mb-0">{{ $stats['resubmits'] }}</div>
                    <small class="text-muted">Re-submitted</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list me-2"></i>All Submissions</span>
        <form method="GET" class="d-flex" style="max-width:320px">
            <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm me-2" placeholder="Search name / phone / email…">
            <button class="btn btn-sm btn-primary" type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:150px">Received</th>
                    <th>Contact</th>
                    <th>Service / Subject</th>
                    <th>Message</th>
                    <th style="width:90px">Source</th>
                    <th style="width:110px">Mail</th>
                    <th style="width:110px">Resubmit</th>
                    <th style="width:60px" class="text-end"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                    <tr>
                        <td>
                            <div class="small">{{ $lead->created_at?->format('d M Y') }}</div>
                            <small class="text-muted">{{ $lead->created_at?->format('h:i A') }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $lead->name }}</div>
                            <div class="small">
                                <a href="tel:{{ $lead->phone }}" class="text-decoration-none">{{ $lead->phone }}</a>
                            </div>
                            <div class="small">
                                <a href="mailto:{{ $lead->email }}" class="text-decoration-none text-muted">{{ $lead->email }}</a>
                            </div>
                        </td>
                        <td>
                            <div>{{ $lead->service ?: '—' }}</div>
                            @if($lead->subject)
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($lead->subject, 40) }}</small>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($lead->message, 90) ?: '—' }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $lead->source }}</span></td>
                        <td>
                            @if($lead->mail_send)
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Sent</span>
                                @if($lead->last_mail_sent_at)
                                    <div><small class="text-muted">{{ $lead->last_mail_sent_at->diffForHumans() }}</small></div>
                                @endif
                            @else
                                <span class="badge bg-light text-dark border">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($lead->form_resubmit)
                                <span class="badge bg-warning text-dark">Yes</span>
                            @else
                                <span class="badge bg-light text-dark border">No</span>
                            @endif
                            @if($lead->resubmit_count > 0)
                                <div><small class="text-muted">{{ $lead->resubmit_count }}×</small></div>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this lead?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No form submissions yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($leads->hasPages())
        <div class="card-footer">
            {{ $leads->links() }}
        </div>
    @endif
</div>
@endsection
