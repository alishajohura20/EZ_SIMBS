<?php
$pageTitle = 'My Wishlist';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['customer', 'admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/storefront-header.php';
?>

<div class="sf-container">
    <div class="sf-page-head">
        <h1 class="sf-page-title"><i class="fas fa-heart"></i>My Wishlist <span class="sf-muted" id="sfWishCountLabel"></span></h1>
        <a class="sf-btn sf-btn-outline" href="<?= APP_URL ?>/pages/customer/dashboard.php"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
    </div>
    <div class="sf-grid" id="sfWishGrid"></div>
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
let wishItems = [];

async function loadWishlist() {
    const res = await fetch(SF_API + '/wishlist.php');
    const json = await res.json();
    wishItems = json.success ? (json.data.wishlist || []) : [];
    document.getElementById('sfWishCountLabel').textContent = wishItems.length ? '(' + wishItems.length + ' saved)' : '';
    renderWish();
}
function renderWish() {
    const grid = document.getElementById('sfWishGrid');
    if (!wishItems.length) {
        grid.innerHTML = '<div class="sf-empty" style="grid-column:1/-1"><i class="fas fa-heart"></i><p class="mb-1" style="font-weight:700">No saved products yet</p><p class="sf-muted small mb-3">Tap the heart on any product to save it here.</p><a class="sf-btn sf-btn-primary" href="dashboard.php"><i class="fas fa-store"></i> Browse Products</a></div>';
        return;
    }
    grid.innerHTML = wishItems.map(p => '<div class="sf-col-card" style="display:contents">' + SF.prodCard(p) + '</div>').join('');
}
window.SF.renderEmptyWishlist = function () {
    const grid = document.getElementById('sfWishGrid');
    if (grid && grid.querySelectorAll('.sf-col-card').length === 0) {
        wishItems = []; renderWish();
    }
};
loadWishlist();
</script>

<?php require_once '../../includes/storefront-footer.php'; ?>