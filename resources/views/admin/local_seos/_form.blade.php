{{-- Shared create/edit form for a city local-SEO page. $local is a LocalSeo (may be unsaved). --}}
@csrf

@php
    // Seed the Alpine FAQ repeater from old() input (after a validation error) or the saved row.
    $faqInit = [];
    if (old('faq_q')) {
        foreach (old('faq_q') as $i => $q) {
            $faqInit[] = ['q' => $q, 'a' => old('faq_a')[$i] ?? ''];
        }
    } elseif ($local->exists) {
        $faqInit = $local->faqList();
    }
    if (empty($faqInit)) $faqInit = [['q' => '', 'a' => '']];
@endphp

{{-- ============ Page + City ============ --}}
<div class="card mb-3">
    <div class="card-header"><i class="fas fa-map-marker-alt me-2"></i>Page &amp; City</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Base page *</label>
                @if($local->exists)
                    <input type="text" class="form-control" value="{{ $local->pageLabel() }}" disabled>
                @else
                    <select name="page_slug" class="form-select" required>
                        @foreach(\App\Models\LocalSeo::PAGES as $slug => $label)
                            <option value="{{ $slug }}" @selected(old('page_slug', $local->page_slug) === $slug)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Which page this city version is a replica of.</small>
                @endif
            </div>
            <div class="col-md-4">
                <label class="form-label">City name *</label>
                <input type="text" name="city" class="form-control" value="{{ old('city', $local->city) }}" placeholder="e.g. Mumbai" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">URL slug</label>
                <input type="text" name="city_slug" class="form-control" value="{{ old('city_slug', $local->city_slug) }}" placeholder="auto from city">
                <small class="text-muted">
                    Final URL: <code>{{ \App\Models\LocalSeo::PAGE_PREFIX[$local->page_slug ?? 'home'] ?? '' }}/<span>{slug}</span></code> · blank = auto.
                </small>
            </div>
        </div>
    </div>
</div>

{{-- ============ Hero override ============ --}}
<div class="card mb-3">
    <div class="card-header"><i class="fas fa-heading me-2"></i>Hero (leave blank to inherit the base page)</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Hero heading (H1)</label>
                <textarea name="hero_heading" rows="2" class="form-control" maxlength="1000" placeholder="e.g. PR Services in Mumbai — Get Featured in 500+ Media">{{ old('hero_heading', $local->hero_heading) }}</textarea>
                <small class="text-muted">HTML allowed — wrap a word in <code>&lt;span class="highlight"&gt;…&lt;/span&gt;</code> to colour it like the original.</small>
            </div>
            <div class="col-md-12">
                <label class="form-label">Hero sub-text (lead paragraph)</label>
                <textarea name="hero_subheading" rows="3" class="form-control" maxlength="2000" placeholder="Short city-specific intro paragraph…">{{ old('hero_subheading', $local->hero_subheading) }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- ============ Client Voices ============ --}}
<div class="card mb-3">
    <div class="card-header"><i class="fas fa-comment-dots me-2"></i>Client Voices (city testimonials)</div>
    <div class="card-body">
        <p class="mb-2 text-muted">
            Testimonials tagged with this city show here automatically. Tag them in
            <a href="{{ route('admin.testimonials.index') }}" target="_blank">Testimonials</a> (City field).
            If none are tagged, the base page's default testimonials are shown.
        </p>
        @if(!empty($cities))
            <div class="small text-muted">Cities already used on testimonials:
                @foreach($cities as $c)<span class="badge bg-light text-dark border">{{ $c }}</span> @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ============ FAQ override (Alpine repeater) ============ --}}
<div class="card mb-3" x-data="{ faqs: @js($faqInit) }">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-question-circle me-2"></i>FAQ (leave empty to inherit / hide)</span>
        <button type="button" class="btn btn-sm btn-outline-primary" x-on:click="faqs.push({q:'',a:''})">
            <i class="fas fa-plus me-1"></i> Add FAQ
        </button>
    </div>
    <div class="card-body">
        <template x-for="(f, idx) in faqs" :key="idx">
            <div class="row g-2 mb-3 align-items-start">
                <div class="col-md-5">
                    <input type="text" class="form-control" placeholder="Question"
                           x-model="f.q" :name="`faq_q[${idx}]`">
                </div>
                <div class="col-md-6">
                    <textarea class="form-control" rows="2" placeholder="Answer"
                              x-model="f.a" :name="`faq_a[${idx}]`"></textarea>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" x-on:click="faqs.splice(idx,1)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </template>
        <p class="small text-muted mb-0">Each city should have 2–4 unique FAQs to stay clear of duplicate-content flags.</p>
    </div>
</div>

{{-- ============ SEO meta ============ --}}
<div class="card mb-3">
    <div class="card-header"><i class="fas fa-search me-2"></i>SEO meta</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Meta title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $local->meta_title) }}" maxlength="255" placeholder="60–70 characters recommended">
            </div>
            <div class="col-md-12">
                <label class="form-label">Meta description</label>
                <textarea name="meta_description" rows="3" class="form-control" maxlength="500" placeholder="150–160 characters recommended">{{ old('meta_description', $local->meta_description) }}</textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label">Meta keywords</label>
                <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $local->meta_keywords) }}" maxlength="500" placeholder="comma, separated, keywords">
            </div>
            <div class="col-md-6">
                <label class="form-label">Canonical URL override</label>
                <input type="url" name="canonical_override" class="form-control" value="{{ old('canonical_override', $local->canonical_override) }}" placeholder="Leave blank to auto-detect">
            </div>
            <div class="col-md-6">
                <label class="form-label">Robots</label>
                <input type="text" name="robots" class="form-control" value="{{ old('robots', $local->robots) }}" placeholder="e.g. index, follow">
            </div>
            <div class="col-md-12">
                <label class="form-label">OG image (social share preview)</label>
                @if($url = $local->ogImageUrl())
                    <div class="mb-2 d-flex align-items-center gap-2">
                        <img src="{{ $url }}" alt="" style="height:80px;border:1px solid #eee;border-radius:6px">
                        <form action="{{ route('admin.local_seos.og_image.remove', $local) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove the OG image?');">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </form>
                    </div>
                @endif
                <input type="file" name="og_image" class="form-control" accept="image/*">
                <small class="text-muted">Recommended 1200×630px. Max 4MB.</small>
            </div>
            <div class="col-md-12">
                <label class="form-label">Custom JSON-LD (optional)</label>
                <textarea name="json_ld" rows="5" class="form-control font-monospace" placeholder='{"@@context":"https://schema.org","@@type":"WebPage", ... }'>{{ old('json_ld', $local->json_ld) }}</textarea>
                <small class="text-muted">Raw JSON-LD — replaces the base page's default structured data.</small>
            </div>
            <div class="col-md-12">
                <label class="form-label">Custom &lt;head&gt; HTML (optional)</label>
                <textarea name="custom_head" rows="3" class="form-control font-monospace" placeholder='&lt;link rel="me" href="…"&gt;'>{{ old('custom_head', $local->custom_head) }}</textarea>
            </div>
            <div class="col-md-12">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" @checked(old('is_active', $local->exists ? $local->is_active : true))>
                    <label class="form-check-label" for="isActive">Active (page is live &amp; in the sitemap)</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-5">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-1"></i> Save
    </button>
    <a href="{{ route('admin.local_seos.index', ['page' => $local->page_slug]) }}" class="btn btn-light">Cancel</a>
</div>
