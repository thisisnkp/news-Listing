?{{-- Top Bar — mirrors main site /includes/header.php --}}
<div class="top-bar">
    <div class="container top-bar-inner">
        <div class="top-bar-left">
            <a href="mailto:contact@rvrising.com"><i class="fas fa-envelope"></i> contact@rvrising.com</a>
            <span class="divider">|</span>
            <a href="tel:9594643234"><i class="fas fa-phone-alt"></i> +91 95946 43234</a>
        </div>
        <div class="top-bar-right">
            <a href="#" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://instagram.com/officialrahulmishra_" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://linkedin.com/in/rahul-varun-106b65155" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
        </div>
    </div>
</div>

{{-- Main Header / Navigation --}}
<header class="site-header" id="siteHeader">
    <div class="container header-inner">
        <a href="{{ $siteRoot }}/" class="logo">
            <span class="logo-mark">RV</span>
            <span class="logo-text">Rising <small>Media</small></span>
        </a>

        <nav class="main-nav" id="mainNav">
            <button class="nav-close" id="navClose" aria-label="Close menu"><i class="fas fa-times"></i></button>
            <ul>
                <li><a href="{{ $siteRoot }}/">Home</a></li>
                <li><a href="{{ $siteRoot }}/about">About Us</a></li>
                <li><a href="{{ $siteRoot }}/services">Our Services</a></li>
                <li><a href="{{ $siteRoot }}/pr-services">PR Services</a></li>
                <li><a href="{{ $siteRoot }}/studio">Studio</a></li>
                <li><a href="{{ url('/') }}" class="active">Pricing</a></li>
                <li><a href="{{ $siteRoot }}/blog">Blog</a></li>
            </ul>
            <a href="{{ $siteRoot }}/contact" class="btn btn-primary nav-cta">Get a Quote <i class="fas fa-arrow-right"></i></a>
        </nav>

        <button class="nav-toggle" id="navToggle" aria-label="Open menu"><i class="fas fa-bars"></i></button>
    </div>
</header>
