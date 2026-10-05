<?php
$pageTitle = 'Shop';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['customer', 'admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/storefront-header.php';
$STORE = APP_URL;
?>

<!-- Hero slider -->
<div class="sf-hero" id="sfHero">
    <div class="sf-hero-track" id="sfHeroTrack"></div>
    <div class="sf-hero-dots" id="sfHeroDots"></div>
    <button class="sf-hero-arrows prev" onclick="SF.heroMove(-1)"><i class="fas fa-chevron-left"></i></button>
    <button class="sf-hero-arrows next" onclick="SF.heroMove(1)"><i class="fas fa-chevron-right"></i></button>
</div>

<div class="sf-container">

    <!-- New Arrivals -->
    <section class="sf-section" id="sfNewSection">
        <div class="sf-sec-head">
            <h2 class="sf-sec-title"><span class="sf-sec-icon"><i class="fas fa-fire"></i></span>New Arrivals</h2>
            <a class="sf-sec-link" href="#sfShop" onclick="resetAll();return false;">Browse all <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="sf-grid" id="sfNewArrivals"></div>
    </section>

    <!-- All Products / Shop -->
    <section class="sf-section" id="sfShop">
        <div class="sf-sec-head">
            <h2 class="sf-sec-title"><span class="sf-sec-icon"><i class="fas fa-store"></i></span>All Products</h2>
            <div class="sf-sec-toolbar" id="sfShopToolbar"></div>
        </div>
        <div class="sf-grid" id="sfShopGrid"></div>
        <nav class="sf-pagination" id="sfShopPagination"></nav>
    </section>
</div>

<!-- Product detail modal -->
<div class="modal fade sf-modal" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body p-0" id="productModalBody"></div>
        </div>
    </div>
</div>

<script>
/* ---------- State ---------- */
let heroIdx = 0;

/* ---------- Hero ---------- */
function slideGrad(i) {
    const grads = [
        'linear-gradient(120deg,#312e81,#4f46e5)', 'linear-gradient(120deg,#4338ca,#6d28d9)',
        'linear-gradient(120deg,#0e7490,#0891b2)', 'linear-gradient(120deg,#b45309,#f59e0b)'
    ];
    return grads[i % grads.length];
}
function slideImage(b, i) {
    if (b && b.image) return window.SF_STORE + '/' + b.image;
    const ads = [
        window.SF_STORE + '/assets/images/ads/hero-ad-1.svg',
        window.SF_STORE + '/assets/images/ads/hero-ad-2.svg'
    ];
    return ads[i % ads.length];
}
async function loadHero() {
    const res = await fetch(SF_API + '/index.php');
    const json = await res.json();
    if (!json.success) return;
    const banners = json.data.banners || [];
    const cats = json.data.categories || [];
    const featured = json.data.featured || [];

    const defSlides = [
        {title:'Everyday savings on fresh groceries', subtitle:'Fruits, snacks, beverages & daily essentials delivered to your door.', link:'#sfShop'},
        {title:'Welcome to EZ SIMBS Super Shop', subtitle:'One system keeps your inventory live and your storefront fresh — shop it now.', link:'#sfShop'}
    ];
    const slides = banners.length ? banners : defSlides;
    document.getElementById('sfHeroTrack').innerHTML = slides.map((b, i) => `
        <div class="sf-hero-slide" style="background:${slideGrad(i)}">
            <div class="sf-hero-deco" style="width:340px;height:340px;background:rgba(255,255,255,.08);top:-120px;right:8%"></div>
            <div class="sf-hero-deco" style="width:220px;height:220px;background:rgba(255,255,255,.06);bottom:-90px;right:26%"></div>
            <div class="sf-container sf-hero-inner">
                <div class="sf-hero-copy">
                    <span class="sf-hero-kicker"><i class="fas fa-bolt"></i> SHOP THE LATEST</span>
                    <h1>${b.title || ''}</h1>
                    <p>${b.subtitle || ''}</p>
                    <button class="sf-hero-btn" onclick="goLink('${(b.link||'#sfShop').replace(/'/g,"")}')">Shop Now <i class="fas fa-arrow-right"></i></button>
                </div>
                <div class="sf-hero-media">
                    <img src="${slideImage(b, i)}" alt="${(b.title || 'Promotion').replace(/"/g,'"')}" loading="lazy">
                </div>
            </div>
        </div>`).join('');
    document.getElementById('sfHeroDots').innerHTML = slides.map((_, i) => `<button class="${i===0?'active':''}" onclick="SF.heroTo(${i})"></button>`).join('');

    // New arrivals
    const grid = document.getElementById('sfNewArrivals');
    grid.innerHTML = featured.slice(0, 8).map(p => SF.prodCard(p)).join('');
    if (!featured.length) grid.innerHTML = '<div class="sf-empty" style="grid-column:1/-1"><i class="fas fa-box-open"></i><p class="mb-0" style="font-weight:700">No products yet.</p></div>';
}
SF.heroMove = function (d) { SF.heroTo(heroIdx + d); };
SF.heroTo = function (i) {
    const track = document.getElementById('sfHeroTrack');
    const dots = document.querySelectorAll('#sfHeroDots button');
    if (!track) return;
    const n = Math.max(0, Math.min(track.children.length - 1, i));
    track.style.transform = `translateX(-${n * 100}%)`;
    dots.forEach((d, k) => d.classList.toggle('active', k === n));
    heroIdx = n;
};
setInterval(() => { if (document.getElementById('sfHeroTrack')) SF.heroMove(1); }, 6000);
function goLink(href) { if (!href || href === '#sfShop') { document.getElementById('sfShop').scrollIntoView({behavior:'smooth'}); } else { location.href = href; } }

/* ---------- Boot ---------- */
loadHero();

/* ---------- Shop / Product Catalog ---------- */
let shopState = {
    search: new URLSearchParams(window.location.search).get('search') || '',
    category_id: new URLSearchParams(window.location.search).get('category_id') || '',
    sort: 'newest',
    page: 1,
    per_page: 24
};

function buildShopToolbar() {
    const toolbar = document.getElementById('sfShopToolbar');
    if (!toolbar) return;
    toolbar.innerHTML = `
        <div class="sf-toolbar-group">
            <select id="sfSortSelect" class="sf-form sf-form-sm" style="width:auto">
                <option value="newest"${shopState.sort==='newest'?' selected':''}>Newest</option>
                <option value="price_asc"${shopState.sort==='price_asc'?' selected':''}>Price: Low to High</option>
                <option value="price_desc"${shopState.sort==='price_desc'?' selected':''}>Price: High to Low</option>
                <option value="name"${shopState.sort==='name'?' selected':''}>Name: A-Z</option>
            </select>
        </div>
    `;
    document.getElementById('sfSortSelect').addEventListener('change', function(e) {
        shopState.sort = e.target.value;
        shopState.page = 1;
        loadShopProducts();
    });
}

async function loadShopProducts() {
    const grid = document.getElementById('sfShopGrid');
    const pagination = document.getElementById('sfShopPagination');
    if (!grid) return;
    
    grid.innerHTML = '<div class="sf-loading" style="grid-column:1/-1;text-align:center;padding:40px"><i class="fas fa-spinner fa-spin fa-2x" style="opacity:.35"></i></div>';
    if (pagination) pagination.innerHTML = '';

    const params = new URLSearchParams();
    if (shopState.search) params.set('search', shopState.search);
    if (shopState.category_id) params.set('category_id', shopState.category_id);
    if (shopState.sort) params.set('sort', shopState.sort);
    params.set('page', shopState.page);
    params.set('per_page', shopState.per_page);

    try {
        const res = await fetch(`${window.SF_API}/catalog.php?${params.toString()}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.message || 'Failed to load products');

        const { products, pagination: pg } = json.data;
        
        if (!products.length) {
            grid.innerHTML = '<div class="sf-empty" style="grid-column:1/-1"><i class="fas fa-search"></i><p class="mb-0" style="font-weight:700">No products found</p></div>';
            return;
        }

        grid.innerHTML = products.map(p => window.SF.prodCard(p)).join('');
        
        // Pagination
        if (pagination && pg.total_pages > 1) {
            let html = '';
            if (pg.current_page > 1) {
                html += `<button class="sf-page" data-page="${pg.current_page - 1}"><i class="fas fa-chevron-left"></i></button>`;
            }
            for (let i = 1; i <= pg.total_pages; i++) {
                if (i === 1 || i === pg.total_pages || (i >= pg.current_page - 2 && i <= pg.current_page + 2)) {
                    html += `<button class="sf-page${i === pg.current_page ? ' active' : ''}" data-page="${i}">${i}</button>`;
                } else if (i === pg.current_page - 3 || i === pg.current_page + 3) {
                    html += `<span class="sf-page-ellipsis">...</span>`;
                }
            }
            if (pg.current_page < pg.total_pages) {
                html += `<button class="sf-page" data-page="${pg.current_page + 1}"><i class="fas fa-chevron-right"></i></button>`;
            }
            pagination.innerHTML = html;
            pagination.querySelectorAll('.sf-page').forEach(btn => {
                btn.addEventListener('click', function() {
                    shopState.page = parseInt(this.dataset.page, 10);
                    loadShopProducts();
                    window.scrollTo({top: document.getElementById('sfShop').offsetTop - 80, behavior: 'smooth'});
                });
            });
        }
    } catch (e) {
        grid.innerHTML = '<div class="sf-empty" style="grid-column:1/-1"><i class="fas fa-exclamation-triangle"></i><p class="mb-0" style="font-weight:700">Failed to load products</p></div>';
        console.error(e);
    }
}

function resetAll() {
    // Reset state
    shopState.search = '';
    shopState.category_id = '';
    shopState.sort = 'newest';
    shopState.page = 1;
    
    // Clear search input
    const searchInput = document.getElementById('sfSearchInput');
    if (searchInput) searchInput.value = '';
    
    // Update URL
    const url = new URL(window.location);
    url.searchParams.delete('search');
    url.searchParams.delete('category_id');
    window.history.replaceState({}, '', url);
    
    // Rebuild toolbar and reload products (works even if shop not fully initialized)
    if (typeof buildShopToolbar === 'function') buildShopToolbar();
    if (typeof loadShopProducts === 'function') loadShopProducts();
    
    // Scroll to shop section
    const shopSection = document.getElementById('sfShop');
    if (shopSection) shopSection.scrollIntoView({behavior: 'smooth'});
    
    return false;
}

// Initialize shop when SF is ready
function initShop() {
    buildShopToolbar();
    loadShopProducts();
    
    // Handle search form submit - prevent default and use our load function
    const searchForm = document.getElementById('sfSearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('sfSearchInput');
            shopState.search = input.value.trim();
            shopState.page = 1;
            // Update URL without reload
            const url = new URL(window.location);
            if (shopState.search) url.searchParams.set('search', shopState.search);
            else url.searchParams.delete('search');
            url.searchParams.delete('category_id');
            window.history.replaceState({}, '', url);
            loadShopProducts();
            document.getElementById('sfShop').scrollIntoView({behavior: 'smooth'});
        });
    }
}

// Wait for SF to be available (storefront.js loads after this script in DOM)
function waitForSF() {
    if (window.SF && window.SF.prodCard) {
        console.log('[Shop] SF ready, initializing shop');
        initShop();
    } else {
        console.log('[Shop] SF not ready, waiting...', !!window.SF, !!window.SF?.prodCard);
        setTimeout(waitForSF, 50);
    }
}

// Start waiting
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', waitForSF);
} else {
    waitForSF();
}

// Also handle search form immediately (in case SF loads later)
function setupSearchForm() {
    const searchForm = document.getElementById('sfSearchForm');
    if (searchForm && !searchForm.dataset.listenerAdded) {
        searchForm.dataset.listenerAdded = 'true';
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('[Shop] Search form submitted');
            const input = document.getElementById('sfSearchInput');
            shopState.search = input.value.trim();
            shopState.page = 1;
            const url = new URL(window.location);
            if (shopState.search) url.searchParams.set('search', shopState.search);
            else url.searchParams.delete('search');
            url.searchParams.delete('category_id');
            window.history.replaceState({}, '', url);
            if (window.loadShopProducts) loadShopProducts();
            const shopSection = document.getElementById('sfShop');
            if (shopSection) shopSection.scrollIntoView({behavior: 'smooth'});
        });
        console.log('[Shop] Search form listener added');
    }
}

// Try to setup search form immediately
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupSearchForm);
} else {
    setupSearchForm();
}
</script>

<?php require_once '../../includes/storefront-footer.php'; ?>