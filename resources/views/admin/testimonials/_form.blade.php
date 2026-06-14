{{-- Shared create/edit form. $testimonial may be null on create. --}}
@csrf
@php $t = $testimonial ?? null; @endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Type *</label>
        <select name="type" class="form-select" required>
            <option value="text" @selected(old('type', $t?->type ?? 'text') === 'text')>Text</option>
            <option value="video" @selected(old('type', $t?->type) === 'video')>Video</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Rating (0–5)</label>
        <input type="number" name="rating" min="0" max="5" class="form-control" value="{{ old('rating', $t->rating ?? 5) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Sort order</label>
        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $t->sort_order ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Person name *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $t->name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Role</label>
        <input type="text" name="role" class="form-control" value="{{ old('role', $t->role ?? '') }}" placeholder="e.g. Founder">
    </div>
    <div class="col-md-4">
        <label class="form-label">Company</label>
        <input type="text" name="company" class="form-control" value="{{ old('company', $t->company ?? '') }}" placeholder="e.g. FinEdge Capital">
    </div>

    <div class="col-md-4">
        <label class="form-label">City <span class="text-muted">(for Local SEO pages)</span></label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $t->city ?? '') }}" placeholder="e.g. Mumbai">
        <small class="text-muted">Tag a city to auto-show this on that city's Local SEO page. Leave blank for general use.</small>
    </div>

    <div class="col-md-12">
        <label class="form-label">Message (for text testimonials)</label>
        <textarea name="message" class="form-control" rows="4" placeholder="Their words…">{{ old('message', $t->message ?? '') }}</textarea>
    </div>

    <div class="col-md-12">
        <label class="form-label">Video URL (for video testimonials)</label>
        <input type="url" name="video_url" class="form-control" value="{{ old('video_url', $t->video_url ?? '') }}" placeholder="https://www.youtube.com/embed/VIDEO_ID or https://player.vimeo.com/video/ID">
        <small class="text-muted">Paste the EMBED URL (YouTube: <code>https://www.youtube.com/embed/...</code>) so it can render inside an iframe.</small>
    </div>

    <div class="col-md-6">
        <label class="form-label">Avatar image</label>
        @if($t && $t->image)
            <div class="mb-2">
                <img src="{{ $t->imageUrl() }}" alt="" style="width:64px;height:64px;border-radius:50%;object-fit:cover">
            </div>
        @endif
        <input type="file" name="image" class="form-control" accept="image/*">
        <small class="text-muted">Optional. Square works best. Max 4MB.</small>
    </div>

    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" @checked(old('is_active', $t->is_active ?? true))>
            <label class="form-check-label" for="isActive">Active (visible on the homepage)</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-1"></i> Save
    </button>
    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-light">Cancel</a>
</div>
