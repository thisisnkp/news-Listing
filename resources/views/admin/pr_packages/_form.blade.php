{{-- Shared create/edit form for a PR pricing card. $package may be unsaved. --}}
@csrf

@php
    // Seed the Alpine features repeater from old() input or the saved row.
    $featInit = [];
    if (old('features')) {
        foreach (old('features') as $f) { $featInit[] = $f; }
    } elseif ($package->exists) {
        $featInit = $package->featureList();
    }
    if (empty($featInit)) $featInit = [''];
@endphp

<div class="card mb-3">
    <div class="card-header"><i class="fas fa-tag me-2"></i>Card details</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tier label <span class="text-muted">(small, uppercase)</span></label>
                <input type="text" name="label" class="form-control" value="{{ old('label', $package->label) }}" placeholder="e.g. Starter / Premium">
            </div>
            <div class="col-md-8">
                <label class="form-label">Card name (heading) *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $package->name) }}" placeholder="e.g. Trial Pack" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Original price <span class="text-muted">(struck)</span></label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="text" name="original_price" class="form-control" value="{{ old('original_price', $package->original_price) }}" placeholder="1,999">
                </div>
                <small class="text-muted">Dummy “was” price. Leave blank to hide.</small>
            </div>
            <div class="col-md-3">
                <label class="form-label">Actual price *</label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="text" name="price" class="form-control" value="{{ old('price', $package->price) }}" placeholder="999" required>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Subtitle</label>
                <input type="text" name="sub" class="form-control" value="{{ old('sub', $package->sub) }}" placeholder="One-line description">
            </div>

            <div class="col-md-4">
                <label class="form-label">Ribbon / badge text</label>
                <input type="text" name="badge" class="form-control" value="{{ old('badge', $package->badge) }}" placeholder="e.g. Most Popular">
                <small class="text-muted">Blank = no ribbon.</small>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $package->sort_order ?? 0) }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_popular" value="0">
                    <input class="form-check-input" type="checkbox" id="isPopular" name="is_popular" value="1" @checked(old('is_popular', $package->is_popular))>
                    <label class="form-check-label" for="isPopular">Highlight (dark “popular” card)</label>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" @checked(old('is_active', $package->exists ? $package->is_active : true))>
                    <label class="form-check-label" for="isActive">Active (shown on site)</label>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Features repeater --}}
<div class="card mb-3" x-data="{ feats: @js($featInit) }">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list-check me-2"></i>Features (bullet points)</span>
        <button type="button" class="btn btn-sm btn-outline-primary" x-on:click="feats.push('')">
            <i class="fas fa-plus me-1"></i> Add feature
        </button>
    </div>
    <div class="card-body">
        <template x-for="(f, idx) in feats" :key="idx">
            <div class="input-group mb-2">
                <span class="input-group-text"><i class="fas fa-check text-success"></i></span>
                <input type="text" class="form-control" placeholder="e.g. 250+ Outlets" x-model="feats[idx]" :name="`features[${idx}]`">
                <button type="button" class="btn btn-outline-danger" x-on:click="feats.splice(idx,1)"><i class="fas fa-trash"></i></button>
            </div>
        </template>
    </div>
</div>

<div class="mb-5">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save</button>
    <a href="{{ route('admin.pr_packages.index') }}" class="btn btn-light">Cancel</a>
</div>
