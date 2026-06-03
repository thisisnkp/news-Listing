{{-- Shared <head> includes — pulls fonts + main site CSS so /pricing matches the rest of rvrising.com --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#e63946">
<meta name="csrf-token" content="{{ csrf_token() }}">

@if($favicon = App\Models\SiteSetting::getFavicon())
    <link rel="icon" href="{{ $favicon }}">
@else
    <link rel="icon" type="image/png" href="https://img.icons8.com/color/96/megaphone.png">
@endif

{{-- Resource hints --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

{{-- Fonts (matches main site: Poppins + Fraunces) --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800;9..144,900&display=swap" rel="stylesheet">

{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

{{-- Bootstrap 5 (Laravel app uses this for grid + form controls). Loaded BEFORE main CSS so the site theme overrides Bootstrap on overlapping classes (.btn, .container). --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

{{-- Main site stylesheet — the source of truth for brand look-and-feel --}}
<link rel="stylesheet" href="{{ $siteRoot }}/assets/css/style.css">
