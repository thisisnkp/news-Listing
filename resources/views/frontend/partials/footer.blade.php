{{-- Pre-Footer CTA Strip --}}
<section class="cta-strip">
    <div class="container cta-strip-inner">
        <div>
            <h3>Ready to amplify your brand?</h3>
            <p>Get a free 15-minute strategy call with our PR experts today.</p>
        </div>
        <div class="cta-strip-actions">
            <a href="tel:9594643234" class="btn btn-light"><i class="fas fa-phone-alt"></i> Call Now</a>
            <a href="{{ $siteRoot }}/contact" class="btn btn-outline-light">Send Inquiry</a>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-col">
            <a href="{{ $siteRoot }}/" class="logo logo-footer">
                <span class="logo-mark">RV</span>
                <span class="logo-text">Rising <small>Media</small></span>
            </a>
            <p class="footer-about">A leading PR agency and production house headquartered in Mumbai. Since 2017, we help brands, founders, and celebrities build trusted reputations across digital and traditional media.</p>
            <div class="footer-social">
                <a href="#" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://instagram.com/officialrahulmishra_" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://linkedin.com/in/rahul-varun-106b65155" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul class="footer-links">
                <li><a href="{{ $siteRoot }}/"><i class="fas fa-chevron-right"></i> Home</a></li>
                <li><a href="{{ $siteRoot }}/about"><i class="fas fa-chevron-right"></i> About Us</a></li>
                <li><a href="{{ $siteRoot }}/services"><i class="fas fa-chevron-right"></i> Our Services</a></li>
                <li><a href="{{ $siteRoot }}/pr-services"><i class="fas fa-chevron-right"></i> PR Services</a></li>
                <li><a href="{{ $siteRoot }}/studio"><i class="fas fa-chevron-right"></i> Podcast Studio</a></li>
                <li><a href="{{ url('/') }}"><i class="fas fa-chevron-right"></i> Pricing</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Our Services</h4>
            <ul class="footer-links">
                <li><a href="{{ $siteRoot }}/services"><i class="fas fa-chevron-right"></i> Public Relations</a></li>
                <li><a href="{{ $siteRoot }}/services"><i class="fas fa-chevron-right"></i> Digital PR</a></li>
                <li><a href="{{ $siteRoot }}/services"><i class="fas fa-chevron-right"></i> Celebrity Management</a></li>
                <li><a href="{{ $siteRoot }}/services"><i class="fas fa-chevron-right"></i> Press Conference</a></li>
                <li><a href="{{ $siteRoot }}/services"><i class="fas fa-chevron-right"></i> Ad &amp; Corporate Films</a></li>
                <li><a href="{{ $siteRoot }}/services"><i class="fas fa-chevron-right"></i> Influencer Marketing</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Reach Us</h4>
            <ul class="footer-contact">
                <li><i class="fas fa-map-marker-alt"></i> <span>G-93, Oshiwara, Andheri West, Mumbai, Maharashtra - 400053</span></li>
                <li><i class="fas fa-phone-alt"></i> <a href="tel:9594643234">+91 95946 43234</a></li>
                <li><i class="fas fa-envelope"></i> <a href="mailto:contact@rvrising.com">contact@rvrising.com</a></li>
                <li><i class="fas fa-envelope"></i> <a href="mailto:media@rvrising.com">media@rvrising.com</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p>&copy; {{ date('Y') }} RV Rising Media Pvt Ltd. All rights reserved.</p>
            <p class="legal-mini">CIN: U63910MH2024PTC424570 &nbsp;|&nbsp; GST: 27AANCR6069G1ZV</p>
        </div>
    </div>
</footer>


{{-- Main site JS — gives Laravel pricing pages the same nav toggle, smooth scroll, etc. --}}
<script src="{{ $siteRoot }}/assets/js/main.js" defer></script>

{{-- Tawk.to live chat --}}
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/62b82d297b967b1179968ca4/1g6fn0rug';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
{{-- End Tawk.to --}}
