@extends('layouts.admin')

@section('title', 'Edit SEO — ' . $page->page_label)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">SEO — {{ $page->page_label }}</h1>
        <small class="text-muted">Edit the meta values shown by Google &amp; social cards for <code>{{ $page->page_slug === 'home' ? '/' : '/' . $page->page_slug }}</code>.</small>
    </div>
    <a href="{{ route('admin.page_seos.index') }}" class="btn btn-light">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.page_seos.update', $page) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Meta title</label>
                    <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}" maxlength="255" placeholder="60–70 characters recommended">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Meta description</label>
                    <textarea name="meta_description" rows="3" class="form-control" maxlength="500" placeholder="150–160 characters recommended">{{ old('meta_description', $page->meta_description) }}</textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Meta keywords</label>
                    <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $page->meta_keywords) }}" maxlength="500" placeholder="comma, separated, keywords">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Canonical URL override</label>
                    <input type="url" name="canonical_override" class="form-control" value="{{ old('canonical_override', $page->canonical_override) }}" placeholder="Leave blank to auto-detect">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Robots</label>
                    <input type="text" name="robots" class="form-control" value="{{ old('robots', $page->robots) }}" placeholder="e.g. index, follow">
                </div>

                <div class="col-md-12">
                    <label class="form-label">OG image (social share preview)</label>
                    @if($url = $page->ogImageUrl())
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <img src="{{ $url }}" alt="" style="height:80px;border:1px solid #eee;border-radius:6px">
                            <form action="{{ route('admin.page_seos.og_image.remove', $page) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove the OG image?');">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                    @endif
                    <input type="file" name="og_image" class="form-control" accept="image/*">
                    <small class="text-muted">Recommended size 1200×630px. Max 4MB.</small>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Custom JSON-LD (optional)</label>
                    <textarea name="json_ld" rows="6" class="form-control font-monospace" placeholder='{"@@context":"https://schema.org","@@type":"WebPage", ... }'>{{ old('json_ld', $page->json_ld) }}</textarea>
                    <small class="text-muted">Pasted raw — must be valid JSON-LD. Replaces the page's default <code>$pageJsonLd</code>.</small>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Custom &lt;head&gt; HTML (optional)</label>
                    <textarea name="custom_head" rows="4" class="form-control font-monospace" placeholder='&lt;link rel="me" href="…"&gt;'>{{ old('custom_head', $page->custom_head) }}</textarea>
                    <small class="text-muted">Raw HTML appended to &lt;head&gt;. Use carefully.</small>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save
                </button>
                <a href="{{ route('admin.page_seos.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
