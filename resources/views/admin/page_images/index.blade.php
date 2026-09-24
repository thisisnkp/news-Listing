@extends('layouts.admin')

@section('title', 'Page Images')

@section('content')
@php use App\Models\PageImage; @endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Page Images</h1>
        <small class="text-muted">Swap the pictures used inside the section blocks on the public pages.</small>
    </div>
    <a href="{{ PageImage::liveUrl($page) }}" target="_blank" class="btn btn-outline-secondary">
        <i class="fas fa-external-link-alt me-1"></i> View live page
    </a>
</div>

{{-- Page tabs — Studio is the default --}}
<ul class="nav nav-pills mb-4">
    @foreach(PageImage::PAGES as $slug => $label)
        <li class="nav-item">
            <a class="nav-link {{ $page === $slug ? 'active' : '' }}"
               href="{{ route('admin.page_images.index', ['page' => $slug]) }}">
                {{ $label }}
            </a>
        </li>
    @endforeach
</ul>

@if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

@foreach($sections as $key => $meta)
    @php $items = $rows[$key] ?? collect(); @endphp

    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-1">{{ $meta['label'] }}</h5>
            <small class="text-muted">{{ $meta['hint'] }}</small>
        </div>
        <div class="card-body">

            {{-- ==================== VIDEO SLOT ==================== --}}
            @if($meta['type'] === 'video')
                @php $item = $items->first(); @endphp
                <form action="{{ route('admin.page_images.update', $item) }}" method="POST" class="row g-3">
                    @csrf @method('PUT')
                    <div class="col-md-5">
                        @if($item->video_url)
                            <iframe src="{{ $item->video_url }}" title="{{ $meta['label'] }}" frameborder="0"
                                    allowfullscreen loading="lazy"
                                    style="width:100%; aspect-ratio:9/16; max-height:360px; border:0; border-radius:8px; background:#000;"></iframe>
                        @else
                            <div class="text-muted text-center border rounded py-5">No video set</div>
                        @endif
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">YouTube embed URL</label>
                        <input type="url" name="video_url" class="form-control mb-1"
                               value="{{ old('video_url', $item->video_url) }}"
                               placeholder="https://www.youtube.com/embed/VIDEO_ID" required>
                        <div class="form-text mb-3">
                            Use the <strong>embed</strong> form. A normal watch link
                            (<code>youtube.com/watch?v=ABC123</code>) has to become
                            <code>youtube.com/embed/ABC123</code> or it will not play.
                        </div>

                        <label class="form-label">Title / alt text</label>
                        <input type="text" name="alt" class="form-control mb-3"
                               value="{{ old('alt', $item->alt) }}" placeholder="RV Rising Studio Tour">

                        <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Video</button>
                    </div>
                </form>

            {{-- ==================== SINGLE IMAGE SLOT ==================== --}}
            @elseif($meta['type'] === 'single')
                @php $item = $items->first(); @endphp
                <form action="{{ route('admin.page_images.update', $item) }}" method="POST"
                      enctype="multipart/form-data" class="row g-3 align-items-start">
                    @csrf @method('PUT')
                    <div class="col-md-4">
                        @if($item->displayUrl())
                            <img src="{{ $item->displayUrl() }}" alt=""
                                 style="width:100%; max-height:220px; object-fit:contain; border-radius:8px; border:1px solid #e5e7eb; background:#f8f9fa;">
                            <div class="mt-2">
                                @if($item->isUploaded())
                                    <span class="badge bg-success">Uploaded</span>
                                @else
                                    <span class="badge bg-light text-dark border">Original</span>
                                @endif
                            </div>
                        @else
                            <div class="text-muted text-center border rounded py-5">No image</div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Replace image</label>
                        <input type="file" name="image" class="form-control mb-1" accept="image/*">
                        <div class="form-text mb-3">JPG, PNG or WebP &middot; up to 6 MB. Leave empty to keep the current one.</div>

                        <label class="form-label">Alt text <span class="text-muted">(for SEO &amp; screen readers)</span></label>
                        <input type="text" name="alt" class="form-control mb-3" value="{{ old('alt', $item->alt) }}">

                        <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Save</button>
                        @if($item->isUploaded())
                            <button type="submit" form="revert-{{ $item->id }}" class="btn btn-outline-secondary"
                                    onclick="return confirm('Remove the uploaded image and show the original again?');">
                                <i class="fas fa-undo me-1"></i> Revert to original
                            </button>
                        @endif
                    </div>
                </form>
                @if($item->isUploaded())
                    <form id="revert-{{ $item->id }}" action="{{ route('admin.page_images.upload.remove', $item) }}"
                          method="POST" class="d-none">@csrf @method('DELETE')</form>
                @endif

            {{-- ==================== GALLERY ==================== --}}
            @else
                <form action="{{ route('admin.page_images.store') }}" method="POST"
                      enctype="multipart/form-data" class="row g-2 align-items-end mb-4">
                    @csrf
                    <input type="hidden" name="page_slug" value="{{ $page }}">
                    <input type="hidden" name="section_key" value="{{ $key }}">
                    <div class="col-md-8">
                        <label class="form-label">Add images</label>
                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
                        <div class="form-text">Pick several at once &middot; up to 6 MB each &middot; new uploads go to the top.</div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100"><i class="fas fa-upload me-1"></i> Upload</button>
                    </div>
                </form>

                @if($items->isEmpty())
                    <div class="text-center text-muted py-5 border rounded">
                        <i class="fas fa-images fa-2x mb-2 d-block"></i>
                        No images here yet &mdash; the page is still showing the files sitting in
                        <code>assets/imgs/studio</code>.
                    </div>
                @else
                    <form action="{{ route('admin.page_images.bulk_update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="page_slug" value="{{ $page }}">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:110px">Image</th>
                                        <th>Alt text</th>
                                        <th style="width:100px">Order</th>
                                        <th style="width:90px">Shown</th>
                                        <th style="width:60px" class="text-end"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>
                                                <a href="{{ $item->displayUrl() }}" target="_blank" rel="noopener">
                                                    <img src="{{ $item->displayUrl() }}" alt=""
                                                         style="width:90px; height:64px; object-fit:cover; border-radius:6px; border:1px solid #e5e7eb;">
                                                </a>
                                            </td>
                                            <td>
                                                <input type="text" name="alt[{{ $item->id }}]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $item->alt }}" placeholder="Describe the photo">
                                            </td>
                                            <td>
                                                <input type="number" name="sort_order[{{ $item->id }}]" min="0" max="9999"
                                                       class="form-control form-control-sm" value="{{ $item->sort_order }}">
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="active[{{ $item->id }}]" value="1" @checked($item->is_active)>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <button type="submit" form="del-{{ $item->id }}"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Remove this image from the gallery?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted">
                                Newly uploaded images go to the top by themselves. Lower &ldquo;Order&rdquo; shows first,
                                so edit these numbers to rearrange. The first 9 visible images load with the page;
                                the rest sit behind &ldquo;View All Photos&rdquo;.
                            </small>
                            <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Gallery</button>
                        </div>
                    </form>

                    @foreach($items as $item)
                        <form id="del-{{ $item->id }}" action="{{ route('admin.page_images.destroy', $item) }}"
                              method="POST" class="d-none">@csrf @method('DELETE')</form>
                    @endforeach
                @endif
            @endif

        </div>
    </div>
@endforeach
@endsection
