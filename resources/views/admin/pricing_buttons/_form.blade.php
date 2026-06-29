{{-- Shared create/edit form for a pricing button. $button may be unsaved. --}}
@csrf

<div class="card mb-3">
    <div class="card-header"><i class="fas fa-link me-2"></i>Button details</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Button text *</label>
                <input type="text" name="label" class="form-control" value="{{ old('label', $button->label) }}" placeholder="e.g. Press Release Packages" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Icon <span class="text-muted">(Font Awesome class, optional)</span></label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon', $button->icon) }}" placeholder="e.g. fas fa-newspaper">
                <small class="text-muted">Find classes at fontawesome.com/icons. Leave blank for no icon.</small>
            </div>

            <div class="col-md-8">
                <label class="form-label">URL *</label>
                <input type="text" name="url" class="form-control" value="{{ old('url', $button->url) }}" placeholder="https://… or /pricing/package/some-slug" required>
                <small class="text-muted">Full link (https://…) or an internal path (e.g. <code>/pricing/package/digital-pr</code>).</small>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $button->sort_order ?? 0) }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-check form-switch">
                    <input type="hidden" name="new_tab" value="0">
                    <input class="form-check-input" type="checkbox" id="newTab" name="new_tab" value="1" @checked(old('new_tab', $button->new_tab))>
                    <label class="form-check-label" for="newTab">New tab</label>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" @checked(old('is_active', $button->exists ? $button->is_active : true))>
                    <label class="form-check-label" for="isActive">Active (shown on the /pricing page)</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-5">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save</button>
    <a href="{{ route('admin.pricing_buttons.index') }}" class="btn btn-light">Cancel</a>
</div>
