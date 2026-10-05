<?php
/*
 * EZ SIMBS — Customer Storefront layout header (UniMart-style).
 * Self-contained: requires config+functions, then renders the full <head>,
 * top utility bar, sticky main header (logo / search / wishlist-cart-account),
 * category nav strip and an open <main> tag.
 *
 * Pages must call requireRole(...) BEFORE including this partial.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$currentUser = currentUser();
$isCustomer   = ($_SESSION['user_role'] ?? '') === 'customer';
$pageTitle    = $pageTitle ?? 'Shop';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle) ?> · EZ SIMBS Super Shop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= APP_URL ?>/assets/css/storefront.css" rel="stylesheet">
    <script>
        window.SF_API   = '<?= APP_URL ?>/api/storefront';
        window.SF_STORE = '<?= APP_URL ?>';
        window.SF_IS_CUSTOMER = <?= $isCustomer ? 'true' : 'false' ?>;
    </script>
</head>
<body class="sf-body">

    <!-- Top utility bar -->
    <div class="sf-topbar">
        <div class="sf-container">
            <div class="sf-flex" style="gap:18px">
                <span><i class="fas fa-phone-alt"></i><?= sanitize($currentUser['phone'] ?? '') ? 'Hotline: ' . sanitize($currentUser['phone']) : 'Free delivery over $50' ?></span>
                <span class="d-none d-sm-inline"><i class="fas fa-shield-alt"></i>100% Secure Payment</span>
            </div>
            <div class="sf-topbar-links">
                <a href="<?= APP_URL ?>/pages/customer/orders.php" class="d-none d-md-inline"><i class="fas fa-truck"></i>Track Order</a>
                <?php if ($isCustomer): ?>
                    <span class="sf-topbar-user"><i class="fas fa-user-circle"></i>Hi, <?= sanitize($currentUser['name'] ?? 'there') ?></span>
                    <a class="btn" href="<?= APP_URL ?>/pages/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
                <?php else: ?>
                    <a class="btn" href="<?= APP_URL ?>/pages/auth/login.php"><i class="fas fa-user"></i> Sign In</a>
                    <a class="btn" href="<?= APP_URL ?>/landing.php"><i class="fas fa-store"></i> Storefront</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main header -->
    <header class="sf-header">
        <div class="sf-container sf-header-inner">
            <a class="sf-logo" href="<?= APP_URL ?>/pages/customer/dashboard.php">
                <span class="sf-logo-mark"><i class="fas fa-store"></i></span>
                <span class="sf-logo-text">
                    <strong>EZ SIMBS</strong>
                    <span>Online Super Shop</span>
                </span>
            </a>

            <form class="sf-search" id="sfSearchForm" action="<?= APP_URL ?>/pages/customer/dashboard.php" method="get" role="search">
                <div class="input-group">
                    <button type="submit" class="sf-search-btn" aria-label="Search"><i class="fas fa-search"></i></button>
                    <input type="text" name="search" id="sfSearchInput" placeholder="Search for products, brands..." autocomplete="off"
                           value="<?= sanitize($_GET['search'] ?? '') ?>">
                </div>
            </form>

            <div class="sf-icons">
                <button class="sf-icon-btn" data-go="wishlist" title="Wishlist">
                    <i class="far fa-heart"></i>
                    <span class="sf-badge-count d-none" id="sfWishCount">0</span>
                </button>
                <button class="sf-icon-btn" data-go="cart" title="Cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="sf-badge-count d-none" id="sfCartCount">0</span>
                </button>
                <div class="sf-account" id="sfAccount">
                    <button class="sf-icon-btn" title="Account"><i class="fas fa-user"></i></button>
                    <div class="sf-acc-menu">
                        <?php if ($isCustomer): ?>
                            <div class="sf-acc-head">
                                <strong><?= sanitize($currentUser['name'] ?? 'Customer') ?></strong>
                                <span><?= sanitize($currentUser['email'] ?? '') ?></span>
                            </div>
                        <?php else: ?>
                            <div class="sf-acc-head">
                                <strong>Staff preview</strong>
                                <span>You are browsing as staff</span>
                            </div>
                        <?php endif; ?>
                        <a href="<?= APP_URL ?>/pages/customer/profile.php"><i class="fas fa-user"></i>My Profile</a>
                        <a href="<?= APP_URL ?>/pages/customer/wishlist.php"><i class="fas fa-heart"></i>My Wishlist</a>
                        <a href="<?= APP_URL ?>/pages/customer/orders.php"><i class="fas fa-box"></i>My Orders</a>
                        <a href="<?= APP_URL ?>/pages/customer/returns.php"><i class="fas fa-undo-alt"></i>My Returns</a>
                        <a href="<?= APP_URL ?>/pages/customer/cart.php"><i class="fas fa-shopping-cart"></i>My Cart</a>
                        <a href="<?= APP_URL ?>/pages/sessions/"><i class="fas fa-shield-alt"></i>Active Sessions</a>
                        <?php if ($isCustomer): ?>
                            <a href="<?= APP_URL ?>/pages/auth/logout.php" class="sf-logout"><i class="fas fa-sign-out-alt"></i>Sign Out</a>
                        <?php else: ?>
                            <a href="<?= APP_URL ?>/pages/store/" class="sf-logout"><i class="fas fa-tachometer-alt"></i>Staff Dashboard</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

<main>
    <!-- Bootstrap + shared storefront scripts load before any page inline script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= APP_URL ?>/assets/js/storefront.js"></script>