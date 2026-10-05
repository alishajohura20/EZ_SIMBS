/* =====================================================================
   EZ SIMBS — Customer Storefront shared JS
   Loaded on every storefront page (dashboard, cart, wishlist, orders).
   ===================================================================== */
(function () {
    'use strict';

    var API = window.SF_API || '/api/storefront';

    /* ---------- small helpers ---------- */
    window.SF = {
        money: function (n) { return '$' + parseFloat(n || 0).toFixed(2); },
        img: function (p) {
            if (p && p.image && p.image !== '')
                return '<img src="' + (window.SF_STORE || '') + '/' + p.image + '" alt="' + (p.name || '') + '" loading="lazy">';
            return '<div class="sf-card-ph"><i class="fas fa-box-open"></i></div>';
        },
        catIcon: function (name) {
            var n = (name || '').toLowerCase();
            if (/snack|chips|kachori|chanachur|savoury/.test(n)) return 'fa-cookie-bite';
            if (/juice|beverage|drink|water|soda|cola/.test(n)) return 'fa-glass-water';
            if (/fruit|veg|vegetable|chilli|alooz|potato/.test(n)) return 'fa-apple-whole';
            if (/dairy|milk|cheese|egg/.test(n)) return 'fa-cheese';
            if (/meat|fish|chicken|beef/.test(n)) return 'fa-drumstick-bite';
            if (/bakery|bread|biscuit|cake/.test(n)) return 'fa-bread-slice';
            if (/household|clean|toilet/.test(n)) return 'fa-broom';
            if (/care|beauty|soap|body/.test(n)) return 'fa-hand-sparkles';
            if (/toys?|kids/.test(n)) return 'fa-baseball-ball';
            return 'fa-tag';
        },
        stars: function (r, count) {
            r = Math.round(parseFloat(r) || 0);
            var html = '';
            for (var i = 1; i <= 5; i++) {
                html += '<i class="fas fa-star" style="opacity:' + (i <= r ? 1 : .3) + '"></i>';
            }
            if (count) html += '<span class="rc">(' + count + ')</span>';
            return html;
        },
        toast: function (msg, type) {
            var t = document.createElement('div');
            t.className = 'sf-toast ' + (type === 'error' ? 'error' : 'success');
            t.innerHTML = '<i class="fas ' + (type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle') + '"></i><span>' + msg + '</span>';
            document.body.appendChild(t);
            requestAnimationFrame(function () { t.classList.add('show'); });
            setTimeout(function () { t.classList.remove('show'); setTimeout(function () { t.remove(); }, 320); }, 2300);
        },
        fetchJSON: function (url, opts) {
            return fetch(url, opts || {}).then(function (r) { return r.json(); });
        },
        modal: function (id) {
            return bootstrap.Modal.getOrCreateInstance(document.getElementById(id));
        }
    };

    /* ---------- header counts ---------- */
    function refreshCounts() {
        if (!window.SF_IS_CUSTOMER) { /* staff preview: still fine (APIs allow any logged-in user) */ }
        SF.fetchJSON(API + '/cart.php').then(function (j) {
            if (j && j.success) setBadge('sfCartCount', j.data && j.data.count);
        }).catch(function () {});
        if (window.SF_IS_CUSTOMER) {
            SF.fetchJSON(API + '/wishlist.php').then(function (j) {
                if (j && j.success) setBadge('sfWishCount', j.data && j.data.wishlist.length);
            }).catch(function () {});
        }
    }
    function setBadge(id, n) {
        var el = document.getElementById(id);
        if (!el) return;
        if (!n) { el.classList.add('d-none'); return; }
        el.classList.remove('d-none');
        el.textContent = n > 99 ? '99+' : n;
    }

    /* ---------- account dropdown ---------- */
    function bindHeader() {
        var acc = document.getElementById('sfAccount');
        if (acc) {
            acc.querySelector('button').addEventListener('click', function (e) {
                e.stopPropagation();
                acc.classList.toggle('open');
            });
            document.addEventListener('click', function (e) {
                if (acc && !acc.contains(e.target)) acc.classList.remove('open');
            });
        }
        document.querySelectorAll('[data-go]').forEach(function (b) {
            b.addEventListener('click', function () {
                var go = b.getAttribute('data-go');
                if (go === 'cart') location.href = window.SF_STORE + '/pages/customer/cart.php';
                if (go === 'wishlist') location.href = window.SF_STORE + '/pages/customer/wishlist.php';
            });
        });
    }

    /* ---------- product card ---------- */
    window.SF.prodCard = function (p) {
        var inStock = parseInt(p.total_stock || 0, 10) > 0;
        var isNew = p.created_at && (Date.now() - new Date(p.created_at).getTime()) < 30 * 864e5;
        var badges = '';
        if (isNew) badges += '<span class="sf-badge new">New</span>';
        badges += inStock
            ? '<span class="sf-badge ok stock"><i class="fas fa-check-circle"></i> In Stock</span>'
            : '<span class="sf-badge out stock"><i class="fas fa-times-circle"></i> Out of Stock</span>';
        return '<div class="sf-card">'
            + '<div class="sf-card-img" onclick="SF.viewProduct(' + p.id + ')">' + SF.img(p) + badges
            + '<button class="sf-wish' + (p.in_wishlist ? ' active' : '') + '" data-pid="' + p.id + '" onclick="SF.toggleWish(' + p.id + ', event)" title="Wishlist"><i class="fa' + (p.in_wishlist ? 's' : 'r') + ' fa-heart"></i></button>'
            + '</div>'
            + '<div class="sf-card-body" onclick="SF.viewProduct(' + p.id + ')">'
            + '<div class="sf-card-cat">' + (p.category_name || 'General') + '</div>'
            + '<div class="sf-card-name">' + (p.name || '') + '</div>'
            + '<div class="sf-stars">' + SF.stars(p.avg_rating, p.review_count) + '</div>'
            + '<div class="sf-card-price">' + SF.money(p.price) + '</div>'
            + '<button class="sf-card-add" data-pid="' + p.id + '" onclick="SF.addToCart(' + p.id + ', event)"' + (!inStock ? ' disabled' : '') + '><i class="fas fa-cart-plus"></i> Add to Cart</button>'
            + '</div></div>';
    };

    /* ---------- cart ---------- */
    window.SF.addToCart = function (pid, e) {
        if (e) e.stopPropagation();
        var btn = e && e.target && e.target.closest ? e.target.closest('.sf-card-add') : null;
        SF.fetchJSON(API + '/cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'add', product_id: pid, qty: 1 })
        }).then(function (j) {
            if (j.success) {
                setBadge('sfCartCount', j.data.cart && j.data.cart.count);
                if (btn && !btn.disabled) {
                    var old = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Added';
                    btn.classList.add('added');
                    setTimeout(function () { btn.innerHTML = old; btn.classList.remove('added'); }, 1400);
                }
                SF.toast('Added to cart ✓');
            } else {
                SF.toast(j.message || 'Could not add to cart', 'error');
            }
        }).catch(function () { SF.toast('Request failed', 'error'); });
    };

    /* ---------- wishlist ---------- */
    window.SF.toggleWish = function (pid, e) {
        if (e) e.stopPropagation();
        SF.fetchJSON(API + '/wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: pid })
        }).then(function (j) {
            if (j.success) {
                var added = j.data && j.data.added;
                document.querySelectorAll('.sf-wish[data-pid="' + pid + '"]').forEach(function (b) {
                    b.classList.toggle('active', added);
                    b.innerHTML = '<i class="fa' + (added ? 's' : 'r') + ' fa-heart"></i>';
                });
                if (window.SF_IS_CUSTOMER) {
                    SF.fetchJSON(API + '/wishlist.php').then(function (w) {
                        setBadge('sfWishCount', w.success ? w.data.wishlist.length : 0);
                    });
                }
                // if we are on the wishlist page, reflect removal
                if (/wishlist\.php/.test(location.pathname) && !added) {
                    var card = e && e.target && e.target.closest ? e.target.closest('.sf-col-card') : null;
                    if (card) card.remove();
                    var grid = document.getElementById('sfWishGrid');
                    if (grid && grid.querySelectorAll('.sf-col-card').length === 0) window.SF.renderEmptyWishlist && SF.renderEmptyWishlist();
                }
                SF.toast(added ? 'Added to wishlist' : 'Removed from wishlist');
            } else {
                SF.toast(j.message || 'Wishlist error', 'error');
            }
        }).catch(function () { SF.toast('Request failed', 'error'); });
    };

    /* ---------- product detail modal ---------- */
    window.SF.viewProduct = function (id) {
        var modalEl = document.getElementById('productModal');
        var body = document.getElementById('productModalBody');
        if (!modalEl || !body) return;
        body.innerHTML = '<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-2x" style="opacity:.35"></i></div>';
        SF.modal('productModal').show();
        SF.fetchJSON(API + '/product.php?id=' + id).then(function (j) {
            if (!j.success) { body.innerHTML = '<div class="p-4 text-center text-danger">Product not found</div>'; return; }
            var p = j.data.product, inW = j.data.in_wishlist;
            var inStock = parseInt(p.total_stock || 0, 10) > 0;
            var rs = p.rating_summary || { total: 0, average: 0, distribution: { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 } };
            var gallery = (p.gallery || []).slice(0, 4);
            var thumbs = gallery.length
                ? gallery.map(function (g) { return '<div class="sf-pd-thumb"><img src="' + (window.SF_STORE || '') + '/' + g.image + '"></div>'; }).join('')
                : '';
            var distBar = [5, 4, 3, 2, 1].map(function (s) {
                var c = rs.distribution[s] || 0;
                var pct = rs.total ? Math.round((c / rs.total) * 100) : 0;
                return '<div class="d-flex align-items-center gap-2 mb-1"><span style="width:16px;font-size:.78rem;color:#64748b;font-weight:700">' + s + '<i class="fas fa-star ms-1" style="font-size:.62rem;color:#f59e0b"></i></span><div class="sf-ratings-dist" style="flex:1"><div class="bar"><div style="width:' + pct + '%"></div></div></div><span style="width:26px;font-size:.76rem;color:#64748b">' + c + '</span></div>';
            }).join('');
            var reviews = ((p.reviews || []).slice(0, 3)).map(function (r) {
                return '<div style="border:1px solid var(--sf-border);border-radius:12px;padding:12px;margin-bottom:10px"><div class="sf-between"><strong style="font-size:.86rem">' + (r.user_name || 'Customer') + '</strong><small class="sf-muted">' + new Date(r.created_at).toLocaleDateString() + '</small></div><div class="sf-stars" style="margin:4px 0 0">' + SF.stars(r.rating) + '</div>' + (r.title ? '<div style="font-weight:700;font-size:.88rem;margin-top:4px">' + r.title + '</div>' : '') + (r.comment ? '<div style="font-size:.84rem;color:#475569;margin-top:2px">' + r.comment + '</div>' : '') + '</div>';
            }).join('');
            var mainImg = p.image ? '<img src="' + (window.SF_STORE || '') + '/' + p.image + '" alt="' + p.name + '">' : '<div class="sf-card-ph"><i class="fas fa-box-open"></i></div>';
            body.innerHTML =
                '<div class="modal-header border-0"><h5 class="modal-title fw-bold">' + (p.name || '') + '</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>'
                + '<div class="modal-body"><div class="row g-4">'
                + '<div class="col-md-6"><div class="sf-pd-image">' + mainImg + '</div>' + (thumbs ? '<div class="sf-pd-thumbs">' + thumbs + '</div>' : '') + '</div>'
                + '<div class="col-md-6">'
                + '<span class="sf-badge-chip"><i class="fas fa-tag"></i>' + (p.category_name || 'General') + '</span>'
                + (p.brand_name ? '<span class="sf-badge-chip ms-2"><i class="fas fa-trademark"></i>' + p.brand_name + '</span>' : '')
                + '<div class="sf-stars mt-2" style="font-size:.85rem">' + SF.stars(rs.average) + '<span class="rc">' + (rs.total ? rs.average.toFixed(1) + ' (' + rs.total + ' reviews)' : 'No reviews yet') + '</span></div>'
                + '<div class="sf-card-price mt-1" style="font-size:1.9rem">' + SF.money(p.price) + '</div>'
                + '<p class="mt-3" style="color:#475569;line-height:1.65;font-size:.9rem">' + (p.description || 'No description available for this product.') + '</p>'
                + '<div class="d-flex flex-wrap gap-2 mb-3">'
                + '<span class="sf-badge-chip"><i class="fas fa-cube"></i>' + (p.sku || '') + '</span>'
                + (p.unit_name ? '<span class="sf-badge-chip"><i class="fas fa-ruler"></i>' + p.unit_name + '</span>' : '')
                + '<span class="sf-badge-chip"><i class="fas fa-' + (inStock ? 'check-circle text-success' : 'times-circle text-danger') + '"></i>' + (inStock ? p.total_stock + ' available' : 'Out of stock') + '</span>'
                + '</div>'
                + '<div class="d-flex align-items-center gap-3 mb-3"><span class="sf-label mb-0">Qty</span><div class="sf-qty"><button onclick="SF.bumpQty(-1)"><i class="fas fa-minus"></i></button><span id="sfBuyQty">1</span><button onclick="SF.bumpQty(1)"><i class="fas fa-plus"></i></button></div></div>'
                + '<div class="d-flex gap-2">'
                + '<button class="sf-btn sf-btn-primary flex-grow-1" onclick="SF.buyNow(' + p.id + ')" ' + (!inStock ? 'disabled' : '') + '><i class="fas fa-shopping-bag"></i> Add to Cart</button>'
                + '<button class="sf-btn sf-btn-outline" onclick="SF.toggleWish(' + p.id + ')" id="sfModWish"><i class="fa' + (inW ? 's' : 'r') + ' fa-heart"></i></button>'
                + '</div></div></div>'
                + ((rs.total || p.reviews && p.reviews.length) ? '<div class="mt-4" style="border-top:1px solid var(--sf-border);padding-top:16px"><h6 class="fw-bold mb-3"><i class="fas fa-star text-warning me-1"></i>Customer Reviews</h6>' + (rs.total ? distBar + '<div class="sf-muted my-2 small">' + rs.total + ' total rating(s)</div>' : '') + reviews + '</div>' : '')
                + (window.SF_IS_CUSTOMER ? '<div class="mt-4" style="border-top:1px solid var(--sf-border);padding-top:16px"><h6 class="fw-bold mb-3">Write a Review</h6><div class="d-flex gap-1 mb-2" id="sfReviewStars">' + [1, 2, 3, 4, 5].map(function (s) { return '<i class="far fa-star" data-s="' + s + '" style="cursor:pointer;font-size:1.2rem;color:#f59e0b" onclick="SF.pickStar(' + s + ')"></i>'; }).join('') + '</div><input id="sfReviewTitle" class="sf-form mb-2" placeholder="Review title (optional)"><textarea id="sfReviewComment" class="sf-form mb-2" rows="2" placeholder="Share your thoughts (optional)"></textarea><button class="sf-btn sf-btn-primary btn-sm" onclick="SF.submitReview(' + p.id + ')"><i class="fas fa-paper-plane"></i> Submit</button></div>' : '')
                + '</div>';
            SF.picked = 5;
        }).catch(function () { body.innerHTML = '<div class="p-4 text-center text-danger">Could not load product</div>'; });
    };

    window.SF.pickStar = function (s) {
        SF.picked = s;
        document.querySelectorAll('#sfReviewStars i').forEach(function (i) {
            i.className = parseInt(i.dataset.s, 10) <= s ? 'fas fa-star' : 'far fa-star';
        });
    };
    window.SF.bumpQty = function (d) {
        var el = document.getElementById('sfBuyQty');
        if (!el) return;
        var n = Math.max(1, parseInt(el.textContent, 10) + d);
        el.textContent = n;
        window.SF._buyQty = n;
    };
    window.SF.buyNow = function (pid) {
        if (!window.SF_IS_CUSTOMER) { SF.toast('Sign in as a customer to add to cart', 'error'); return; }
        var qty = window.SF._buyQty || 1;
        SF.fetchJSON(API + '/cart.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'add', product_id: pid, qty: qty }) }).then(function (j) {
            if (j.success) { setBadge('sfCartCount', j.data.cart.count); SF.toast('Added ' + qty + ' to cart ✓'); var m = bootstrap.Modal.getInstance(document.getElementById('productModal')); if (m) m.hide(); }
            else SF.toast(j.message || 'Could not add', 'error');
        });
    };
    window.SF.submitReview = function (pid) {
        var title = document.getElementById('sfReviewTitle').value.trim();
        var comment = document.getElementById('sfReviewComment').value.trim();
        SF.fetchJSON(API + '/reviews.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ product_id: pid, rating: SF.picked || 5, title: title, comment: comment }) }).then(function (j) {
            if (j.success) { SF.toast('Review submitted — thank you!'); bootstrap.Modal.getInstance(document.getElementById('productModal'))?.hide(); }
            else SF.toast(j.message || 'Could not submit', 'error');
        });
    };

    /* ---------- boot ---------- */
    document.addEventListener('DOMContentLoaded', function () {
        bindHeader();
        refreshCounts();
        window.SF.refreshCounts = refreshCounts;
    });
})();