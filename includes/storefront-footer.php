<?php
require_once __DIR__ . '/config.php';
$currentUser = currentUser();
$isCustomer  = ($_SESSION['user_role'] ?? '') === 'customer';
?>
<?php /* <main> is opened by storefront-header.php */ ?>
    </main>

    <!-- Footer -->
    <footer class="sf-footer">
        <div class="sf-container">
            <div class="sf-footer-top">
                <div>
                    <a class="sf-logo" href="<?= APP_URL ?>/pages/customer/dashboard.php" style="margin-bottom:14px">
                        <span class="sf-logo-mark"><i class="fas fa-store"></i></span>
                        <span class="sf-logo-text">
                            <strong>EZ SIMBS</strong>
                            <span>Online Super Shop</span>
                        </span>
                    </a>
                    <p class="mt-3">Shop fresh groceries, daily essentials and premium goods — all synced in real time with our smart inventory &amp; billing platform.</p>
                    <div class="sf-pay-icons">
                        <span>VISA</span><span>MasterCard</span><span>bKash</span><span>COD</span>
                    </div>
                </div>
                <div>
                    <h5>Shop</h5>
                    <a href="<?= APP_URL ?>/pages/customer/dashboard.php">All Products</a>
                    <a href="<?= APP_URL ?>/pages/customer/dashboard.php?search=lays">Savoury Snacks</a>
                    <a href="<?= APP_URL ?>/pages/customer/dashboard.php?search=juice">Beverages</a>
                    <a href="<?= APP_URL ?>/pages/customer/dashboard.php?search=chips">Daily Essentials</a>
                </div>
                <div>
                    <h5>My Account</h5>
                    <a href="<?= APP_URL ?>/pages/customer/wishlist.php">My Wishlist</a>
                    <a href="<?= APP_URL ?>/pages/customer/cart.php">My Cart</a>
                    <a href="<?= APP_URL ?>/pages/customer/orders.php">Order History</a>
                    <a href="<?= APP_URL ?>/pages/customer/returns.php">My Returns</a>
                    <a href="<?= APP_URL ?>/pages/users/profile.php">My Profile</a>
                </div>
                <div>
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled sf-footer-contact">
                        <li><i class="fas fa-phone-alt"></i><?= sanitize($currentUser['phone'] ?? '+1 (555) 000-0000') ?></li>
                        <li><i class="fas fa-envelope"></i><?= (isset($_SESSION['user_email']) && $_SESSION['user_email']) ? sanitize($_SESSION['user_email']) : 'info@ezsimbs.local' ?></li>
                        <li><i class="fas fa-map-marker-alt"></i>123 Business St, City</li>
                        <li><i class="far fa-clock"></i>Open every day · 8:00 AM – 10:00 PM</li>
                    </ul>
                </div>
            </div>
            <div class="sf-footer-bottom">
                <span>© 2026 EZ SIMBS Super Shop. All rights reserved.</span>
                <span><i class="fas fa-lock"></i> Secure checkout · Privacy Policy · Terms</span>
            </div>
        </div>
    </footer>
</body>
</html>