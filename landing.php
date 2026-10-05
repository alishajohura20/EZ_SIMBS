<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$STORE = APP_URL;

$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$onLanding = preg_match('#/?$#', $currentPath);
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= $_SESSION['theme'] ?? 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> – Inventory, Billing &amp; POS for Online + Offline Stores</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= $STORE ?>/assets/css/style.css" rel="stylesheet">
    <style>
        :root{
            --l-nav-bg:rgba(255,255,255,.78);
            --l-nav-border:rgba(99,102,241,.12);
            --l-body-bg:#fafbfe;
            --l-soft:#eef1ff;
            --l-card:#ffffff;
            --l-text:#0f172a;
            --l-muted:#64748b;
            --l-shadow:0 18px 44px -18px rgba(49,46,129,.22);
        }
        [data-theme="dark"]{
            --l-nav-bg:rgba(15,23,42,.78);
            --l-nav-border:rgba(148,163,184,.14);
            --l-body-bg:#0f172a;
            --l-soft:#1e293b;
            --l-card:#1e293b;
            --l-text:#e2e8f0;
            --l-muted:#94a3b8;
            --l-shadow:0 18px 44px -18px rgba(0,0,0,.5);
        }
        body{font-family:'Plus Jakarta Sans',system-ui,-apple-system,sans-serif;background:var(--l-body-bg);color:var(--l-text);overflow-x:hidden;display:block}
        h1,h2,h3,h4,h5,.brand,.p-name,.price{font-family:'Plus Jakarta Sans',system-ui,sans-serif;letter-spacing:-.01em}
        section{scroll-margin-top:90px}

        /* ── Nav ─────────────────────────────────────────────── */
        .store-nav{position:sticky;top:0;z-index:1000;background:var(--l-nav-bg);backdrop-filter:blur(16px) saturate(160%);-webkit-backdrop-filter:blur(16px) saturate(160%);border-bottom:1px solid var(--l-nav-border);padding:13px 0}
        .store-nav .brand{font-weight:800;font-size:1.32rem;color:var(--primary);text-decoration:none;display:flex;align-items:center;gap:10px}
        .store-nav .brand .brand-chip{width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#8b5cf6 60%,#d946ef);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.05rem;box-shadow:0 8px 20px -8px rgba(99,102,241,.7)}
        .store-nav .brand small{display:block;font-size:.62rem;letter-spacing:.14em;text-transform:uppercase;color:var(--l-muted);font-weight:600;line-height:1}
        .store-nav .search-box{background:var(--l-soft);border:1.5px solid var(--l-nav-border);border-radius:50px;padding:7px 16px;display:flex;align-items:center;gap:10px;transition:border-color .2s, box-shadow .2s}
        .store-nav .search-box:focus-within{border-color:var(--primary);box-shadow:0 0 0 4px rgba(99,102,241,.14)}
        .store-nav .search-box input{border:none;background:transparent;outline:none;width:100%;font-size:.92rem;color:var(--l-text)}
        .store-nav .search-box input::placeholder{color:var(--l-muted)}
        .store-nav .nav-links{display:flex;gap:8px;align-items:center}
        .store-nav .nav-links a,.store-nav .nav-links button{font-size:.88rem;font-weight:700;padding:9px 18px;border-radius:50px;text-decoration:none;transition:all .18s;border:none;cursor:pointer}
        .btn-grad{background:linear-gradient(135deg,#6366f1,#8b5cf6 60%,#d946ef);color:#fff!important;box-shadow:0 10px 22px -10px rgba(99,102,241,.75)}
        .btn-grad:hover{transform:translateY(-1px);box-shadow:0 14px 28px -10px rgba(99,102,241,.85);filter:brightness(1.05)}
        .btn-ghost{background:var(--l-soft);color:var(--l-text);border:1.5px solid var(--l-nav-border)}
        .btn-ghost:hover{border-color:var(--primary);color:var(--primary);background:rgba(99,102,241,.06)}
        .cart-badge{position:relative}
        .cart-badge .notif-dot{position:absolute;top:-6px;right:-8px;min-width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,#ef4444,#f97316);color:#fff;font-size:.62rem;display:flex;align-items:center;justify-content:center;font-weight:800;padding:0 5px;box-shadow:0 4px 10px -4px rgba(239,68,68,.8)}

        /* ── Hero ─────────────────────────────────────────────── */
        .hero-slider{position:relative;overflow:hidden;display:flex;height:clamp(540px,calc(100svh - 68px),800px);background:linear-gradient(135deg,#312e81,#4f46e5 60%,#6d28d9)}
        .hero-slide{position:absolute;inset:0;display:flex;align-items:center;opacity:0;visibility:hidden;transition:opacity .65s ease,visibility .65s}
        .hero-slide.active{opacity:1;visibility:visible;position:relative;height:100%;flex:1 0 100%}
        .hero-slide::before{content:'';position:absolute;inset:0;background:linear-gradient(100deg,rgba(15,23,42,.72) 0%,rgba(15,23,42,.35) 55%,rgba(15,23,42,.08) 100%);z-index:1}
        .hero-slide .container{position:relative;z-index:2;flex:1 1 100%;display:flex;align-items:center;padding-top:56px;padding-bottom:96px}
        .hero-slide .row{flex:1 1 100%;width:100%}
        .hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.22);color:#fff;font-size:.78rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:8px 16px;border-radius:50px;backdrop-filter:blur(6px);margin-bottom:22px}
        .hero-badge i{color:#fbbf24}
        .hero-slide h1{font-size:clamp(2.1rem,4.4vw,3.6rem);font-weight:800;color:#fff;line-height:1.12;margin-bottom:18px;text-shadow:0 4px 30px rgba(0,0,0,.25)}
        .hero-slide h1 .grad-line{background:linear-gradient(90deg,#fde68a,#fca5a5,#f0abfc);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
        .hero-slide .hero-sub{font-size:1.1rem;color:rgba(255,255,255,.88);max-width:520px;margin-bottom:30px;line-height:1.75;font-family:'Inter',sans-serif}
        .hero-cta{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:38px}
        .hero-cta .btn{display:inline-flex;align-items:center;gap:9px;padding:13px 28px;border-radius:50px;font-weight:700;font-size:.95rem;text-decoration:none;transition:all .2s}
        .hero-cta .btn-cta-main{background:#fff;color:#4338ca;box-shadow:0 14px 30px -10px rgba(0,0,0,.4)}
        .hero-cta .btn-cta-main:hover{transform:translateY(-2px);box-shadow:0 18px 36px -10px rgba(0,0,0,.45)}
        .hero-cta .btn-cta-ghost{background:rgba(255,255,255,.12);color:#fff;border:1.5px solid rgba(255,255,255,.3);backdrop-filter:blur(6px)}
        .hero-cta .btn-cta-ghost:hover{background:rgba(255,255,255,.2)}
        .hero-stats{display:flex;flex-wrap:wrap;gap:0;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.16);border-radius:18px;padding:6px 0;backdrop-filter:blur(8px);max-width:520px}
        .hero-stats .stat{flex:1;min-width:130px;padding:16px 22px;text-align:center;border-right:1px solid rgba(255,255,255,.12)}
        .hero-stats .stat:last-child{border-right:none}
        .hero-stats .stat strong{display:block;font-size:1.5rem;font-weight:800;color:#fff}
        .hero-stats .stat span{font-size:.76rem;color:rgba(255,255,255,.75);letter-spacing:.04em;text-transform:uppercase;font-weight:600}
        .hero-art{position:absolute;right:4%;top:50%;transform:translateY(-50%);width:clamp(280px,26vw,360px);height:clamp(280px,26vw,360px);z-index:2;display:flex;align-items:center;justify-content:center}
        .hero-art .halo{position:absolute;inset:0;background:conic-gradient(from 0deg,rgba(255,255,255,.28),rgba(255,255,255,0),rgba(255,255,255,.16),rgba(255,255,255,0));border-radius:50%;filter:blur(2px);animation:spin 22s linear infinite}
        .hero-art .card-float{position:relative;width:min(240px,72%);background:rgba(255,255,255,.95);border-radius:24px;padding:16px;box-shadow:0 34px 70px -24px rgba(0,0,0,.5);backdrop-filter:blur(10px);transform:rotate(3deg);animation:float 6s ease-in-out infinite}
        .hero-art .card-float img{width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:16px;background:#eef1ff}
        .hero-art .card-float .float-placeholder{width:100%;aspect-ratio:16/10;border-radius:16px;background:linear-gradient(135deg,#e0e7ff,#ede9fe);display:flex;align-items:center;justify-content:center;font-size:3.4rem;color:var(--primary)}
        .hero-art .card-float .f-name{font-weight:700;font-size:.92rem;color:#0f172a;margin-top:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .hero-art .card-float .f-price{display:flex;align-items:center;justify-content:space-between;margin-top:6px}
        .hero-art .card-float .f-price strong{font-size:1.15rem;color:#4338ca}
        .hero-art .card-float .f-price span{font-size:.7rem;font-weight:800;color:#16a34a;background:#dcfce7;padding:5px 10px;border-radius:50px}
        .hero-art .chip-a{position:absolute;top:-18px;left:-42px;width:74px;height:74px;border-radius:22px;background:linear-gradient(135deg,#f97316,#fbbf24);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.5rem;box-shadow:0 18px 36px -12px rgba(249,115,22,.7);animation:float 7s ease-in-out infinite}
        .hero-art .chip-b{position:absolute;bottom:-22px;right:-28px;width:66px;height:66px;border-radius:20px;background:linear-gradient(135deg,#22c55e,#4ade80);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.3rem;box-shadow:0 18px 36px -12px rgba(34,197,94,.7);animation:float 5.5s ease-in-out infinite}
        .hero-dots{position:absolute;bottom:24px;left:50%;transform:translateX(-50%);z-index:5;display:flex;gap:8px}
        .hero-dots button{width:9px;height:9px;border-radius:50%;border:none;background:rgba(255,255,255,.4);cursor:pointer;transition:all .25s}
        .hero-dots button.active{background:#fff;width:30px;border-radius:8px}
        @keyframes spin{to{transform:rotate(360deg)}}
        @keyframes float{0%,100%{transform:translateY(0) rotate(3deg)}50%{transform:translateY(-14px) rotate(3deg)}}

        /* ── Trust bar ────────────────────────────────────────── */
        .trust-bar{border-radius:18px;background:var(--l-card);box-shadow:var(--l-shadow);margin-top:-34px;position:relative;z-index:6;border:1px solid var(--l-nav-border)}
        .trust-item{display:flex;align-items:center;gap:14px;padding:18px 22px}
        .trust-item+.trust-item{border-left:1px solid var(--l-nav-border)}
        .trust-item .t-icon{width:46px;height:46px;border-radius:14px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.15rem;color:#fff}
        .t-i-1{background:linear-gradient(135deg,#6366f1,#8b5cf6)}
        .t-i-2{background:linear-gradient(135deg,#f97316,#fbbf24)}
        .t-i-3{background:linear-gradient(135deg,#22c55e,#4ade80)}
        .t-i-4{background:linear-gradient(135deg,#0ea5e9,#06b6d4)}
        .trust-item b{display:block;font-size:.92rem;font-weight:700}
        .trust-item span{font-size:.78rem;color:var(--l-muted)}

        /* ── Section headers ──────────────────────────────────── */
        .section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:26px}
        .section-head .eyebrow{display:inline-flex;align-items:center;gap:8px;color:var(--primary);font-size:.74rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;margin-bottom:8px}
        .section-head h2{font-weight:800;font-size:clamp(1.4rem,2.4vw,1.8rem);margin:0}
        .section-head p{margin:6px 0 0;color:var(--l-muted);font-size:.92rem}
        .link-arrow{display:inline-flex;align-items:center;gap:8px;color:var(--primary);text-decoration:none;font-weight:700;font-size:.9rem;white-space:nowrap}
        .link-arrow:hover{gap:12px;color:var(--primary-hover)}

        /* ── Category cards ───────────────────────────────────── */
        .cat-card{background:var(--l-card);border:1px solid var(--l-nav-border);border-radius:20px;padding:22px;text-decoration:none;display:flex;gap:16px;align-items:center;height:100%;transition:all .22s;position:relative;overflow:hidden}
        .cat-card:hover{transform:translateY(-4px);box-shadow:var(--l-shadow);border-color:var(--primary)}
        .cat-card .cat-icon{width:56px;height:56px;border-radius:16px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#fff}
        .cg-0{background:linear-gradient(135deg,#6366f1,#a855f7)}
        .cg-1{background:linear-gradient(135deg,#f97316,#f43f5e)}
        .cg-2{background:linear-gradient(135deg,#0ea5e9,#6366f1)}
        .cg-3{background:linear-gradient(135deg,#22c55e,#14b8a6)}
        .cg-4{background:linear-gradient(135deg,#eab308,#f97316)}
        .cat-card .c-name{font-weight:700;font-size:1rem;color:var(--l-text);margin:0}
        .cat-card .c-count{font-size:.78rem;color:var(--l-muted);font-weight:500}
        .cat-card .c-arrow{position:absolute;right:18px;top:50%;transform:translateY(-50%);color:var(--l-muted);font-size:.85rem;opacity:0;transition:all .2s}
        .cat-card:hover .c-arrow{opacity:1;right:12px;color:var(--primary)}

        /* ── Product cards ────────────────────────────────────── */
        .product-card{background:var(--l-card);border:1px solid var(--l-nav-border);border-radius:20px;overflow:hidden;transition:all .22s;height:100%;display:flex;flex-direction:column}
        .product-card:hover{transform:translateY(-5px);box-shadow:var(--l-shadow);border-color:rgba(99,102,241,.35)}
        .product-card .img-wrap{aspect-ratio:4/3;overflow:hidden;background:var(--l-soft);position:relative}
        .product-card .img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
        .product-card:hover .img-wrap img{transform:scale(1.06)}
        .product-card .img-placeholder{display:flex;align-items:center;justify-content:center;width:100%;height:100%;background:linear-gradient(135deg,#e0e7ff,#ede9fe);color:var(--primary);font-size:3rem;opacity:.5}
        .badge-stock{position:absolute;top:12px;right:12px;padding:4px 11px;border-radius:50px;font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.03em;box-shadow:0 6px 16px -6px rgba(0,0,0,.25)}
        .badge-stock.in-stock{background:rgba(34,197,94,.92);color:#fff}
        .badge-stock.out-of-stock{background:rgba(239,68,68,.92);color:#fff}
        .product-card .cat-chip{position:absolute;top:12px;left:12px;padding:4px 11px;border-radius:50px;font-size:.66rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;background:rgba(255,255,255,.88);color:#4338ca;backdrop-filter:blur(6px);box-shadow:0 4px 12px -4px rgba(0,0,0,.2)}
        .wishlist-btn{position:absolute;bottom:12px;right:12px;width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.92);border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--l-muted);transition:all .15s;font-size:.85rem;box-shadow:0 4px 12px -4px rgba(0,0,0,.25);backdrop-filter:blur(6px)}
        .wishlist-btn:hover,.wishlist-btn.active{color:#ef4444}
        .product-card .card-body{padding:14px 16px 10px;flex:1;display:flex;flex-direction:column}
        .p-name{font-weight:700;font-size:.96rem;color:var(--l-text);margin:0 0 6px;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;line-height:1.35}
        .p-rating{font-size:.76rem;color:#f59e0b;display:flex;align-items:center;gap:4px}
        .p-rating .rating-count{color:var(--l-muted);font-size:.72rem}
        .p-price{font-size:1.18rem;font-weight:800;color:var(--primary);margin-top:8px}
        .add-cart-btn{margin:12px 16px 16px;padding:9px;border-radius:12px;border:none;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-size:.86rem;font-weight:700;cursor:pointer;transition:all .18s}
        .add-cart-btn:hover{filter:brightness(1.08);box-shadow:0 8px 20px -8px rgba(99,102,241,.7)}
        .add-cart-btn:disabled{opacity:.45;cursor:not-allowed;filter:none;box-shadow:none}

        /* ── Why us ───────────────────────────────────────────── */
        .why-wrap{background:var(--l-card);border:1px solid var(--l-nav-border);border-radius:26px;overflow:hidden;box-shadow:var(--l-shadow)}
        .why-head{padding:clamp(32px,5vh,56px);background:linear-gradient(135deg,rgba(99,102,241,.1),rgba(217,70,239,.07));display:flex;flex-direction:column;justify-content:center}
        .why-head .eyebrow{color:var(--primary);font-size:.74rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
        .why-head h2{font-weight:800;font-size:clamp(1.6rem,2.6vw,2.1rem);margin:12px 0}
        .why-head p{color:var(--l-muted);line-height:1.75;margin-bottom:26px;font-size:.95rem}
        .why-grid{flex:1;display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--l-nav-border)}
        .why-card{background:var(--l-card);padding:30px;display:flex;gap:16px}
        .why-card .w-icon{width:50px;height:50px;border-radius:14px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff;background:linear-gradient(135deg,#6366f1,#8b5cf6);box-shadow:0 10px 24px -10px rgba(99,102,241,.6)}
        .why-card b{display:block;font-size:.98rem;margin-bottom:6px;font-weight:700}
        .why-card span{font-size:.82rem;color:var(--l-muted);line-height:1.6}

        /* ── Promo CTA ────────────────────────────────────────── */
        .promo-banner{border-radius:26px;overflow:hidden;position:relative;background:linear-gradient(120deg,#312e81,#4f46e5 55%,#9333ea);color:#fff}
        .promo-banner::before{content:'';position:absolute;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.16),transparent 65%);top:-160px;right:-120px}
        .promo-banner::after{content:'';position:absolute;width:360px;height:360px;border-radius:50%;background:radial-gradient(circle,rgba(217,70,239,.25),transparent 65%);bottom:-140px;left:-100px}
        .promo-banner .container{position:relative;z-index:2;padding:clamp(36px,5.5vh,64px) clamp(22px,4vw,56px)}
        .promo-banner h2{font-size:clamp(1.5rem,3vw,2.3rem);font-weight:800;margin-bottom:10px}
        .promo-banner p{color:rgba(255,255,255,.85);max-width:520px;margin-bottom:26px}
        .promo-banner .btn{background:#fff;color:#4338ca;font-weight:700;padding:13px 30px;border-radius:50px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .2s;box-shadow:0 14px 30px -10px rgba(0,0,0,.4)}
        .promo-banner .btn:hover{transform:translateY(-2px)}
        .promo-banner .pills{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:26px}
        .promo-banner .pills span{background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.22);padding:7px 14px;border-radius:50px;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:7px}

        /* ── Help & Support ─────────────────────────────────── */
        .help-card{background:var(--l-card);border:1px solid var(--l-nav-border);border-radius:20px;padding:28px 26px;height:100%;transition:all .22s}
        .help-card:hover{transform:translateY(-4px);box-shadow:var(--l-shadow);border-color:rgba(99,102,241,.35)}
        .help-card .help-icon{width:54px;height:54px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.35rem;color:#fff;margin-bottom:16px;box-shadow:0 10px 24px -10px rgba(0,0,0,.35)}
        .help-card h3{font-weight:800;font-size:1.12rem;margin-bottom:10px}
        .help-tag{display:inline-block;font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--primary);margin-bottom:6px}
        .help-card p{font-size:.88rem;color:var(--l-muted);line-height:1.7;margin-bottom:14px}
        .help-card ul{list-style:none;margin:0;padding:0}
        .help-card ul li{display:flex;gap:10px;align-items:flex-start;font-size:.88rem;color:var(--l-text);padding:7px 0;line-height:1.55}
        .help-card ul li i{color:#10b981;margin-top:3px;font-size:.72rem}
        .help-note{margin-top:16px;padding:12px 14px;border-radius:12px;background:var(--l-soft);font-size:.82rem;color:var(--l-muted);display:flex;gap:10px;align-items:flex-start;line-height:1.6}
        .help-note i{color:var(--primary);margin-top:2px}
        .faq-accordion .accordion-item{background:var(--l-card);border:1px solid var(--l-nav-border);border-radius:14px!important;overflow:hidden;margin-bottom:12px;box-shadow:none}
        .faq-accordion .accordion-button{background:var(--l-card);color:var(--l-text);font-weight:700;font-size:.94rem;padding:16px 18px;box-shadow:none}
        .faq-accordion .accordion-button:not(.collapsed){color:var(--primary);background:linear-gradient(135deg,rgba(99,102,241,.06),rgba(217,70,239,.04))}
        .faq-accordion .accordion-button::after{background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236366f1'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e")}
        .faq-accordion .accordion-body{color:var(--l-muted);font-size:.9rem;line-height:1.75;padding:6px 18px 18px}
        .faq-accordion .accordion-body strong{color:var(--l-text)}

        /* ── Footer ───────────────────────────────────────────── */
        .store-footer{background:linear-gradient(180deg,#1e1b4b,#111827);color:#c7d2fe;padding:70px 0 0;margin-top:72px}
        .store-footer h5{color:#fff;font-weight:700;margin-bottom:22px;font-size:1rem}
        .store-footer a{color:#c7d2fe;text-decoration:none;font-size:.9rem;display:block;padding:6px 0;transition:color .15s,padding-left .15s}
        .store-footer a:hover{color:#fff;padding-left:4px}
        .store-footer .brand-blurb{opacity:.8;font-size:.9rem;line-height:1.8;margin-top:18px}
        .store-footer .socials{display:flex;gap:10px;margin-top:22px}
        .store-footer .socials a{width:38px;height:38px;border-radius:12px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;padding:0;transition:all .18s}
        .store-footer .socials a:hover{background:var(--primary);padding:0;transform:translateY(-3px)}
        .store-footer .copyright{border-top:1px solid rgba(255,255,255,.1);margin-top:50px;padding:24px 0;font-size:.82rem;opacity:.7}

        /* ── Fluid vertical pacing ────────────────────────────── */
        .section-space{padding-block:clamp(44px,7vh,88px)}
        .section-space-sm{padding-block:clamp(24px,4.5vh,52px)}

        /* ── Search overlay / modal / toast (unchanged) ───────── */
        .search-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);backdrop-filter:blur(4px);z-index:1100;display:none;align-items:flex-start;justify-content:center;padding:12vh 20px 20px}
        .search-overlay.open{display:flex}
        .search-overlay .search-panel{background:var(--l-card);border-radius:20px;width:100%;max-width:680px;max-height:80vh;overflow:hidden;box-shadow:0 30px 90px rgba(0,0,0,.3);display:flex;flex-direction:column;animation:dropIn .25s ease}
        @keyframes dropIn{from{opacity:0;transform:translateY(-12px)}to{opacity:1;transform:none}}
        .search-overlay .search-input{border-bottom:1px solid var(--l-nav-border);padding:16px 22px;display:flex;align-items:center;gap:12px}
        .search-overlay .search-input input{border:none;outline:none;background:transparent;font-size:1.1rem;flex:1;color:var(--l-text)}
        .search-overlay .search-results{overflow-y:auto;padding:8px 22px 16px}
        .result-item{display:flex;gap:12px;padding:12px;border-bottom:1px solid var(--l-nav-border);text-decoration:none;color:var(--l-text);border-radius:12px}
        .result-item:hover{background:var(--l-soft);color:var(--l-text)}
        .result-item img{width:52px;height:52px;border-radius:10px;object-fit:cover;flex-shrink:0;background:var(--l-soft)}
        .result-img-placeholder{width:52px;height:52px;border-radius:10px;background:linear-gradient(135deg,#e0e7ff,#ede9fe);flex-shrink:0;display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:1.1rem}

        .reveal{opacity:0;transform:translateY(26px);transition:opacity .6s cubic-bezier(.2,.7,.3,1),transform .6s cubic-bezier(.2,.7,.3,1)}
        .reveal.visible{opacity:1;transform:none}

        @media(max-height:760px){
            .hero-slider{height:clamp(420px,calc(100svh - 58px),640px)}
            .hero-slide h1{font-size:clamp(1.8rem,3.6vw,2.7rem);line-height:1.15}
            .hero-slide .hero-sub{font-size:1rem;line-height:1.6;margin-bottom:20px}
            .hero-cta{margin-bottom:20px}
            .hero-cta .btn{padding:11px 22px;font-size:.88rem}
            .hero-badge{margin-bottom:14px;padding:7px 14px}
            .hero-stats .stat{padding:10px 18px}
            .hero-stats .stat strong{font-size:1.2rem}
            .hero-slide .container{padding-top:36px;padding-bottom:70px}
            .hero-art{transform:translateY(-50%) scale(.82);width:clamp(220px,22vw,300px);height:clamp(220px,22vw,300px)}
        }
        @media(max-height:560px){
            .hero-stats{display:none}
            .hero-slide .hero-sub{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        }
        @media(max-width:991.98px){
            .hero-art{display:none}
            .hero-slider{height:auto;min-height:clamp(440px,calc(100svh - 120px),680px)}
        }
        @media(max-width:767.98px){
            .hero-slider{height:auto;min-height:clamp(440px,calc(100svh - 130px),680px)}
            .hero-slide .container{padding:44px 20px 84px}
            .hero-cta .btn{width:100%;justify-content:center}
            .hero-stats{flex-direction:column}
            .hero-stats .stat{border-right:none;border-bottom:1px solid rgba(255,255,255,.12)}
            .hero-stats .stat:last-child{border-bottom:none}
            .trust-item+.trust-item{border-left:none;border-top:1px solid var(--l-nav-border)}
            .why-head{padding:40px 28px}
            .why-grid{grid-template-columns:1fr}
            .section-head{flex-direction:column;align-items:flex-start}
        }
    </style>
</head>
<body>
<?php if (!isset($_SESSION['user_id'])): ?>
<nav class="store-nav">
    <div class="container d-flex align-items-center justify-content-between gap-2 flex-wrap">
        <a href="/" class="brand"><span class="brand-chip"><i class="fas fa-store"></i></span><span><?= APP_NAME ?><small>Inventory · Billing · POS</small></span></a>
        <div class="d-flex align-items-center flex-grow-1 mx-3" style="max-width:460px;min-width:200px">
            <div class="search-box w-100">
                <i class="fas fa-search" style="opacity:.45;font-size:.85rem"></i>
                <input type="text" placeholder="Search products, brands, categories..." id="navSearchInput" onfocus="openSearch()">
            </div>
        </div>
        <div class="nav-links">
            <a href="<?= $STORE ?>/pages/store/" class="btn-ghost" title="Staff access for Admin, Manager, Branch Manager &amp; Cashier"><i class="fas fa-user-shield me-1"></i> Staff Login</a>
            <a href="<?= $STORE ?>/pages/auth/login.php" class="btn-grad"><i class="fas fa-sign-in-alt me-1"></i> Sign In</a>
        </div>
    </div>
</nav>
<?php else: ?>
<?php
    $currentUser = currentUser();
    $cRole = $_SESSION['user_role'] ?? '';
    $cDash = in_array($cRole, ['admin','manager','branch_manager','cashier']) ? '/pages/dashboard/' : '/pages/customer/dashboard.php';
?>
<nav class="store-nav">
    <div class="container d-flex align-items-center justify-content-between gap-2 flex-wrap">
        <a href="<?= $STORE ?>" class="brand"><span class="brand-chip"><i class="fas fa-store"></i></span><span><?= APP_NAME ?><small>Inventory · Billing · POS</small></span></a>
        <div class="d-flex align-items-center flex-grow-1 mx-3" style="max-width:460px;min-width:200px">
            <div class="search-box w-100">
                <i class="fas fa-search" style="opacity:.45;font-size:.85rem"></i>
                <input type="text" placeholder="Search products, brands, categories..." id="navSearchInput" onfocus="openSearch()">
            </div>
        </div>
        <div class="nav-links">
            <a href="<?= $STORE . $cDash ?>" class="btn-ghost"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
            <a href="<?= $STORE ?>/pages/customer/cart.php" class="btn-ghost cart-badge" id="navCartLink">
                <i class="fas fa-shopping-cart me-1"></i> Cart
                <span class="notif-dot d-none" id="navCartCount"></span>
            </a>
            <a href="<?= $STORE ?>/pages/auth/logout.php" class="btn-ghost" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</nav>
<?php endif; ?>

<!-- Hero -->
<div class="hero-slider" id="heroSlider"></div>

<!-- Trust bar -->
<div class="container" style="position:relative;z-index:6">
    <div class="trust-bar row g-0" style="margin-top:-34px">
        <div class="col-6 col-md-3 trust-item"><div class="t-icon t-i-1"><i class="fas fa-boxes-stacked"></i></div><div><b>Real-time Inventory</b><span>Live stock, sales &amp; alerts</span></div></div>
        <div class="col-6 col-md-3 trust-item"><div class="t-icon t-i-2"><i class="fas fa-cash-register"></i></div><div><b>POS &amp; Billing</b><span>Instant invoices &amp; receipts</span></div></div>
        <div class="col-6 col-md-3 trust-item"><div class="t-icon t-i-3"><i class="fas fa-store"></i></div><div><b>Online + Offline</b><span>One system for both stores</span></div></div>
        <div class="col-6 col-md-3 trust-item"><div class="t-icon t-i-4"><i class="fas fa-headset"></i></div><div><b>24/7 Support</b><span>We're always here to help</span></div></div>
    </div>
</div>

<!-- Why us -->
<section class="container section-space-sm" id="why">
    <div class="why-wrap reveal">
        <div class="row g-0">
            <div class="col-lg-5 why-head">
                <span class="eyebrow"><i class="fas fa-award"></i> Why <?= APP_NAME ?></span>
                <h2>One system for your online &amp; offline store.</h2>
                <p>Manage inventory, billing, point-of-sale, purchases, suppliers and customers from a single dashboard — whether you sell from a counter, an online shop, or both.</p>
                <div>
                    <a href="<?= isset($_SESSION['user_id']) ? $STORE . $cDash : $STORE . '/pages/auth/register-store.php' ?>" class="btn btn-grad px-4 py-2"><i class="fas fa-arrow-right me-1"></i> Get started free</a>
                </div>
            </div>
            <div class="col-lg-7 why-grid">
                <div class="why-card"><div class="w-icon"><i class="fas fa-cash-register"></i></div><div><b>POS &amp; billing</b><span>Ring up sales at the counter with instant invoices, discounts and payment tracking.</span></div></div>
                <div class="why-card"><div class="w-icon" style="background:linear-gradient(135deg,#f97316,#f43f5e)"><i class="fas fa-boxes-stacked"></i></div><div><b>Live inventory</b><span>Every stock-in, sale and return updates everywhere instantly — with low-stock alerts and auto-reorder.</span></div></div>
                <div class="why-card"><div class="w-icon" style="background:linear-gradient(135deg,#0ea5e9,#06b6d4)"><i class="fas fa-store"></i></div><div><b>Online + offline sync</b><span>Your web storefront reads the same stock, prices and orders as your physical counters.</span></div></div>
                <div class="why-card"><div class="w-icon" style="background:linear-gradient(135deg,#22c55e,#4ade80)"><i class="fas fa-chart-line"></i></div><div><b>Reports &amp; alerts</b><span>Sales, purchases and profit analytics — plus smart notifications — all in one clear dashboard.</span></div></div>
            </div>
        </div>
    </div>
</section>

<!-- Promo CTA -->
<section class="container section-space-sm">
    <div class="promo-banner reveal">
        <div class="container">
            <h2>Ready to run your store on one system?</h2>
            <p>Create a free account and manage inventory, billing, point-of-sale and your web storefront — online and offline.</p>
            <div class="pills">
                <span><i class="fas fa-check"></i> Inventory &amp; billing</span>
                <span><i class="fas fa-check"></i> Point-of-sale</span>
                <span><i class="fas fa-check"></i> Web storefront</span>
                <span><i class="fas fa-check"></i> Reports &amp; alerts</span>
            </div>
        </div>
    </div>
</section>

<!-- Help & Support -->
<section class="container section-space" id="help">
    <div class="section-head reveal">
        <div>
            <span class="eyebrow"><i class="fas fa-life-ring"></i> Help Center</span>
            <h2>Shipping, Returns &amp; FAQs</h2>
            <p>Everything you need to know about getting your order — and getting help when you need it.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 reveal" id="shipping">
            <div class="help-card">
                <span class="help-icon" style="background:linear-gradient(135deg,#0ea5e9,#06b6d4)"><i class="fas fa-truck-fast"></i></span>
                <span class="help-tag">Shipping Info</span>
                <h3>Fast, trackable delivery</h3>
                <p>Orders are packed and dispatched within 24 hours on business days, then delivered through our trusted courier partners with live tracking on every step.</p>
                <ul>
                    <li><i class="fas fa-check"></i><span>Free delivery on orders over $50</span></li>
                    <li><i class="fas fa-check"></i><span>Standard delivery in 2–4 business days</span></li>
                    <li><i class="fas fa-check"></i><span>Express delivery in 24–48 hours (surcharge)</span></li>
                    <li><i class="fas fa-check"></i><span>Nationwide coverage with real-time tracking</span></li>
                </ul>
                <div class="help-note"><i class="fas fa-info-circle"></i><span>Track your order anytime from <strong>Order History</strong> in your customer account.</span></div>
            </div>
        </div>
        <div class="col-md-6 reveal" id="returns">
            <div class="help-card">
                <span class="help-icon" style="background:linear-gradient(135deg,#10b981,#14b8a6)"><i class="fas fa-rotate-left"></i></span>
                <span class="help-tag">Return Policy</span>
                <h3>Simple 14-day returns</h3>
                <p>Changed your mind? Eligible items can be returned within 14 days of delivery for a full refund or quick store credit.</p>
                <ul>
                    <li><i class="fas fa-check"></i><span>14-day window from the delivery date</span></li>
                    <li><i class="fas fa-check"></i><span>Items must be unused and in original packaging</span></li>
                    <li><i class="fas fa-check"></i><span>Full refund via original payment method (3–5 business days)</span></li>
                    <li><i class="fas fa-check"></i><span>Instant store credit as an alternative</span></li>
                </ul>
                <div class="help-note"><i class="fas fa-info-circle"></i><span>Clearance or final-sale items are not eligible for returns.</span></div>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="row justify-content-center reveal" id="faq">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <span class="help-tag"><i class="fas fa-circle-question me-1"></i> Frequently Asked Questions</span>
                <h3 style="font-weight:800;font-size:1.35rem">Quick answers to common questions</h3>
            </div>
            <div class="accordion faq-accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq1">How do I create a store account?</button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Click <strong>Create Free Store</strong> on the homepage. It sets up your store, its first branch, and your Admin (owner) account in one step, then takes you straight into the Store Home.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq2">How do I sign in as staff or as a customer?</button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Customers sign in through the <strong>Sign In</strong> button at the top of the storefront. Staff (Admin, Manager, Branch Manager, Cashier) sign in via <strong>Staff Login</strong>, where they pick their role on the Store Home.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq3">How do I track my order?</button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Open <strong>Order History</strong> in your customer account. Every order shows live status badges and a full invoice you can view or print at any time.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq4">How quickly will my order arrive?</button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Standard delivery takes 2–4 business days and is free on orders over $50. Need it faster? Express delivery arrives in 24–48 hours.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq5">Can I return something I bought online?</button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Yes — eligible items can be returned within 14 days of delivery as long as they're unused and in their original packaging. Refunds go back to your original payment method within 3–5 business days.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq6">Does my online store stay in sync with my counters?</button>
                    </h2>
                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">Yes. Your web storefront reads the same catalog used at your counters — every price, stock level and order stays in sync automatically.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="store-footer">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <a href="/" class="brand" style="font-size:1.3rem;font-weight:800;display:flex;align-items:center;gap:10px;color:#fff;text-decoration:none">
                    <span class="brand-chip" style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#8b5cf6 60%,#d946ef);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#fff"><i class="fas fa-store"></i></span>
                    <?= APP_NAME ?>
                </a>
                <p class="brand-blurb">Smart Inventory Management &amp; Billing System for online and offline stores. Manage products, track stock, process POS sales, bill customers and grow your business — all from one powerful dashboard.</p>
                <div class="socials">
                    <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <h5>Quick Links</h5>
                <a href="<?= $STORE ?>/pages/store/">Staff Login</a>
                <a href="<?= $STORE ?>/pages/auth/login.php">Customer Sign In</a>
                <a href="<?= $STORE ?>/pages/auth/register-store.php">Create Free Store</a>
                <a href="#featured">Online Store</a>
                <a href="#why">Features</a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <h5>Help</h5>
                <a href="<?= $STORE ?>/pages/auth/forgot-password.php">Reset Password</a>
                <a href="#shipping">Shipping Info</a>
                <a href="#returns">Returns Policy</a>
                <a href="#faq">FAQs</a>
            </div>
            <div class="col-md-4 col-lg-3">
                <h5>Contact</h5>
                <a href="mailto:info@ezsimbs.local"><i class="fas fa-envelope me-2"></i>info@ezsimbs.local</a>
                <a href="tel:+1234567890"><i class="fas fa-phone me-2"></i>+1 (234) 567-890</a>
                <a href="#"><i class="fas fa-map-marker-alt me-2"></i>123 Business St, City</a>
                <a href="#"><i class="far fa-clock me-2"></i>Mon–Sat, 9:00 – 18:00</a>
            </div>
        </div>
        <div class="copyright text-center">&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved. &nbsp;·&nbsp; Crafted with <i class="fas fa-heart" style="color:#f472b6"></i> for smart shopping.</div>
    </div>
</footer>

<!-- Product Detail Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:22px;overflow:hidden;border:none;box-shadow:0 30px 90px rgba(0,0,0,.25)">
            <div class="modal-body p-0" id="productModalBody">Loading...</div>
        </div>
    </div>
</div>

<!-- Search Overlay -->
<div class="search-overlay" id="searchOverlay">
    <div class="search-panel">
        <div class="search-input">
            <i class="fas fa-search" style="opacity:.5"></i>
            <input type="text" id="overlaySearchInput" placeholder="Search products..." autofocus>
            <button class="btn btn-sm" onclick="closeSearch()" style="border-radius:50px;background:var(--l-soft);color:var(--l-text)">✕</button>
        </div>
        <div class="search-results" id="searchResults"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const API = '<?= $STORE ?>/api/storefront';
let isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
let cartCount = 0;
let featuredAll = [];
let activeCat = null;
const CAT_ICONS = ['fa-shirt','fa-laptop','fa-chair','fa-mobile-alt','fa-book','fa-dumbbell','fa-baby','fa-camera','fa-shoe-prints','fa-paw','fa-tools','fa-gift','fa-box-open','fa-utensils','fa-briefcase','fa-tshirt'];

/* ── Hero slider ──────────────────────────────────────────────── */
async function loadHeroSlider(firstProduct) {
    const res = await fetch(API + '/index.php');
    const json = await res.json();
    if (!json.success) return;
    const banners = json.data.banners || [];
    const defaultArt = cardArt(firstProduct);
    const el = document.getElementById('heroSlider');
    if (banners.length === 0) {
        el.innerHTML = heroSlide({
            badge: 'All-in-one for your store',
            title: 'Run your store — <span class="grad-line">online &amp; offline</span>',
            sub: 'Inventory management, billing, point-of-sale and a web storefront — all synced in one powerful dashboard.',
            cta: isLoggedIn ? '' : '<a href="<?= $STORE ?>/pages/auth/register-store.php" class="btn btn-cta-main"><i class="fas fa-user-plus me-1"></i> Create free account</a>',
            cta2: '<a href="#why" class="btn btn-cta-ghost"><i class="fas fa-play me-1"></i> See it in action</a>',
            art: defaultArt,
            active: true
        });
        return;
    }
    const colors = [
        'linear-gradient(135deg,#312e81,#4f46e5 60%,#6d28d9)',
        'linear-gradient(135deg,#701a75,#a21caf 60%,#c026d3)',
        'linear-gradient(135deg,#0c4a6e,#0284c7 60%,#38bdf8)',
        'linear-gradient(135deg,#14532d,#16a34a 60%,#4ade80)',
        'linear-gradient(135deg,#7c2d12,#ea580c 60%,#fbbf24)',
    ];
    el.innerHTML = banners.map((b, i) => heroSlide({
        badge: '✓ All-in-one system',
        title: `<span class="grad-line">${b.title || 'Run your store, online &amp; offline'}</span>`,
        sub: b.subtitle || 'Inventory, billing and point-of-sale — synced with your web storefront.',
        cta: b.link
            ? `<a href="${b.link}" class="btn btn-cta-main"><i class="fas fa-arrow-right me-1"></i> Get started</a>`
            : isLoggedIn
                ? `<a href="#featured" class="btn btn-cta-main"><i class="fas fa-fire me-1"></i> Browse the store</a>`
                : `<a href="<?= $STORE ?>/pages/auth/register-store.php" class="btn btn-cta-main"><i class="fas fa-user-plus me-1"></i> Create free account</a>`,
        cta2: '<a href="#why" class="btn btn-cta-ghost"><i class="fas fa-play me-1"></i> See it in action</a>',
        bg: b.image ? `url('${b.image}') center/cover no-repeat` : colors[i % colors.length],
        art: defaultArt,
        active: i === 0
    })).join('') + '<div class="hero-dots">' + banners.map((_, i) => `<button data-slide="${i}"${i === 0 ? ' class="active"' : ''}></button>`).join('') + '</div>';
    setupSlider();
}

function cardArt(p) {
    const img = p && p.image && p.image !== ''
        ? `<img src="<?= $STORE ?>/${p.image}" alt="${p.name}">`
        : `<div class="float-placeholder"><i class="fas fa-box"></i></div>`;
    const name = p ? p.name : 'Featured product';
    const price = p ? '$' + parseFloat(p.price).toFixed(2) : 'Best price';
    const inStock = p ? (p.total_stock || 0) > 0 : true;
    const pill = inStock
        ? '<span>IN STOCK</span>'
        : '<span style="background:#fee2e2;color:#dc2626">SOLD OUT</span>';
    return `<div class="hero-art">
        <div class="halo"></div>
        <div class="chip-a"><i class="fas fa-tag"></i></div>
        <div class="chip-b"><i class="fas fa-truck-fast"></i></div>
        <div class="card-float">${img}<div class="f-name">${name}</div>
            <div class="f-price"><strong>${price}</strong>${pill}</div>
        </div>
    </div>`;
}

function heroSlide(o) {
    return `<div class="hero-slide${o.active ? ' active' : ''}" style="background:${o.bg}">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-copy">
                    <span class="hero-badge"><i class="fas fa-star"></i> ${o.badge}</span>
                    <h1>${o.title}</h1>
                    <p class="hero-sub">${o.sub}</p>
                    <div class="hero-cta">${o.cta}${o.cta2}</div>
                    <div class="hero-stats">
                        <div class="stat"><strong>20k+</strong><span>Stores powered</span></div>
                        <div class="stat"><strong>500k+</strong><span>Items tracked</span></div>
                        <div class="stat"><strong>2-in-1</strong><span>Online + offline</span></div>
                    </div>
                </div>
            </div>
        </div>
        ${o.art || ''}
    </div>`;
}

function setupSlider() {
    let current = 0;
    const slides = document.querySelectorAll('#heroSlider .hero-slide');
    const dots = document.querySelectorAll('#heroSlider .hero-dots button');
    if (slides.length === 0) return;

    function go(idx) {
        slides[current]?.classList.remove('active');
        dots[current]?.classList.remove('active');
        current = idx;
        slides[current]?.classList.add('active');
        dots[current]?.classList.add('active');
    }

    dots.forEach(d => d.addEventListener('click', () => go(+d.dataset.slide)));
    setInterval(() => go((current + 1) % slides.length), 5500);
}

/* ── Product detail modal ────────────────────────────────────── */
async function viewProduct(id, e) {
    if (e) e.stopPropagation();
    const el = document.getElementById('productModalBody');
    el.innerHTML = '<div class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-2x" style="opacity:.3"></i></div>';
    new bootstrap.Modal(document.getElementById('productModal')).show();
    const res = await fetch(API + '/product.php?id=' + id);
    const json = await res.json();
    if (!json.success) { el.innerHTML = '<div class="p-4 text-center text-danger">Product not found</div>'; return; }
    const p = json.data.product;
    const inWishlist = json.data.in_wishlist;
    const inStock = p.total_stock > 0;
    const rs = p.rating_summary || { total: 0, average: 0, distribution: { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 } };
    const img = p.image && p.image !== ''
        ? `<img src="<?= $STORE ?>/${p.image}" style="width:100%;height:320px;object-fit:cover;border-radius:16px">`
        : `<div style="width:100%;height:320px;border-radius:16px;background:linear-gradient(135deg,#e0e7ff,#ede9fe);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:4rem"><i class="fas fa-box"></i></div>`;
    const starsHtml = Array.from({ length: 5 }, (_, i) => `<i class="fas fa-star" style="color:${i < Math.round(rs.average) ? '#f59e0b' : '#d1d5db'}"></i>`).join('');
    const maxDist = Math.max(...Object.values(rs.distribution), 1);
    const distRows = [5, 4, 3, 2, 1].map(s => {
        const c = rs.distribution[s] || 0;
        const pct = Math.round((c / maxDist) * 100);
        return `<div class="d-flex align-items-center gap-2 mb-1"><span style="width:18px;text-align:right;font-size:.82rem;color:#94a3b8">${s}</span><i class="fas fa-star" style="color:#f59e0b;font-size:.72rem"></i><div style="flex:1;height:6px;border-radius:3px;background:var(--l-border,#e2e8f0);overflow:hidden"><div style="width:${pct}%;height:100%;background:var(--primary);border-radius:3px"></div></div><span style="width:20px;font-size:.78rem;color:#94a3b8">${c}</span></div>`;
    }).join('');

    el.innerHTML = `<div class="modal-header border-0 pb-0">
        <h5 style="font-weight:800">${p.name}</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="row g-4">
            <div class="col-md-6">${img}</div>
            <div class="col-md-6">
                <p class="text-muted mb-1">${p.category_name || ''} ${p.brand_name ? ' · ' + p.brand_name : ''}</p>
                <div class="d-flex align-items-center gap-2 mb-2">${starsHtml} <span style="font-size:.85rem;color:#94a3b8">${rs.average > 0 ? Number(rs.average).toFixed(1) : 'No rating'} (${rs.total} ${rs.total === 1 ? 'review' : 'reviews'})</span></div>
                <h2 style="font-weight:800;color:var(--primary)">$${parseFloat(p.price).toFixed(2)}</h2>
                <p class="mt-3" style="color:var(--l-text);line-height:1.7">${p.description || 'No description available.'}</p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge" style="background:var(--l-soft);color:var(--l-text);font-size:.82rem;padding:6px 12px"><i class="fas fa-cube me-1"></i>${p.sku}</span>
                    ${p.unit_name ? `<span class="badge" style="background:var(--l-soft);color:var(--l-text);font-size:.82rem;padding:6px 12px"><i class="fas fa-ruler me-1"></i>${p.unit_name}</span>` : ''}
                    <span class="badge ${inStock ? 'bg-success' : 'bg-danger'}" style="font-size:.82rem;padding:6px 12px"><i class="fas fa-${inStock ? 'check' : 'times'}-circle me-1"></i>${inStock ? p.total_stock + ' available' : 'Out of stock'}</span>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1" onclick="addToCart(${p.id})" ${!inStock ? 'disabled' : ''}><i class="fas fa-cart-plus me-1"></i> Add to Cart</button>
                    <button class="btn btn-outline-danger" onclick="toggleWishlist(${p.id})" id="modalWishBtn"><i class="fa${inWishlist ? 's' : 'r'} fa-heart"></i></button>
                </div>
            </div>
        </div>
        ${rs.total > 0 ? `<div class="mt-4 p-3 rounded" style="background:var(--l-soft)"><h6 style="font-weight:700;margin-bottom:12px">Customer Reviews</h6>${distRows}<p style="font-size:.85rem;color:var(--l-muted);margin-top:8px">${rs.total} total reviews</p></div>` : ''}
        ${(p.reviews || []).length > 0 ? `<div class="mt-3">${(p.reviews || []).map(r => `<div class="p-3 mb-2" style="background:var(--l-soft);border-radius:12px"><div class="d-flex justify-content-between"><strong style="font-size:.9rem">${r.user_name}</strong><small class="text-muted">${new Date(r.created_at).toLocaleDateString()}</small></div><div class="mt-1">${Array.from({ length: 5 }, (_, i) => `<i class="fas fa-star" style="color:${i < r.rating ? '#f59e0b' : '#d1d5db'};font-size:.72rem"></i>`).join('')}</div>${r.title ? '<div class="mt-1 fw-bold" style="font-size:.9rem">' + r.title + '</div>' : ''}${r.comment ? '<div class="mt-1" style="font-size:.88rem;color:#64748b">' + r.comment + '</div>' : ''}</div>`).join('')}</div>` : ''}
    </div>`;
}

/* ── Cart & wishlist ─────────────────────────────────────────── */
async function addToCart(productId, e) {
    if (e) e.stopPropagation();
    if (!isLoggedIn) { window.location.href = '<?= $STORE ?>/pages/auth/login.php'; return; }
    const res = await fetch(API + '/cart.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'add', product_id: productId, qty: 1 })
    });
    const json = await res.json();
    if (json.success) { updateCartBadge(json.data.cart.count); showToast('Added to cart!'); }
    else { showToast(json.message || 'Could not add to cart', 'danger'); }
}

async function toggleWishlist(productId, e) {
    if (e) e.stopPropagation();
    if (!isLoggedIn) { window.location.href = '<?= $STORE ?>/pages/auth/login.php'; return; }
    const res = await fetch(API + '/wishlist.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    });
    const json = await res.json();
    if (json.success) {
        const added = json.data.added;
        document.querySelectorAll('.wishlist-btn[data-product="' + productId + '"]').forEach(btn => {
            btn.classList.toggle('active', added);
            btn.querySelector('i').className = added ? 'fas fa-heart' : 'far fa-heart';
        });
        const modalBtn = document.getElementById('modalWishBtn');
        if (modalBtn) modalBtn.innerHTML = '<i class="fa' + (added ? 's' : 'r') + ' fa-heart"></i>';
        showToast(added ? 'Added to wishlist' : 'Removed from wishlist');
    }
}

async function loadCartCount() {
    if (!isLoggedIn) return;
    const res = await fetch(API + '/cart.php');
    const json = await res.json();
    if (json.success) updateCartBadge(json.data.count);
}

function updateCartBadge(count) {
    const dot = document.getElementById('navCartCount');
    if (!dot) return;
    if (count > 0) { dot.textContent = count; dot.classList.remove('d-none'); }
    else { dot.classList.add('d-none'); }
}

/* ── Search overlay ──────────────────────────────────────────── */
let searchTimeout;
function openSearch() {
    document.getElementById('searchOverlay').classList.add('open');
    setTimeout(() => document.getElementById('overlaySearchInput').focus(), 120);
}
function closeSearch() {
    document.getElementById('searchOverlay').classList.remove('open');
    document.getElementById('searchResults').innerHTML = '';
    document.getElementById('overlaySearchInput').value = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSearch(); });
document.addEventListener('DOMContentLoaded', () => {
    const oi = document.getElementById('overlaySearchInput');
    if (oi) oi.addEventListener('input', e => {
        clearTimeout(searchTimeout);
        const q = e.target.value.trim();
        if (q.length < 2) { document.getElementById('searchResults').innerHTML = ''; return; }
        searchTimeout = setTimeout(async () => {
            const res = await fetch(API + '/catalog.php?search=' + encodeURIComponent(q));
            const json = await res.json();
            const products = json.success ? (json.data.products || []) : [];
            document.getElementById('searchResults').innerHTML = products.length
                ? products.map(p => {
                    const img = p.image ? `<img src="<?= $STORE ?>/${p.image}">` : `<div class="result-img-placeholder"><i class="fas fa-box"></i></div>`;
                    return `<a class="result-item" href="#" onclick="viewProduct(${p.id}, event);closeSearch()">
                        ${img}
                        <div><div class="fw-bold">${p.name}</div><small class="text-muted">${p.category_name || ''} · $${parseFloat(p.price).toFixed(2)}</small></div>
                    </a>`;
                }).join('')
                : '<p class="text-center py-4 text-muted">No results found</p>';
        }, 300);
    });
});

/* ── Toast ───────────────────────────────────────────────────── */
function showToast(msg, type = 'success') {
    let t = document.createElement('div');
    t.className = 'position-fixed';
    t.style.cssText = 'bottom:26px;right:26px;z-index:2200;padding:13px 22px;border-radius:14px;color:#fff;font-size:.9rem;font-weight:700;box-shadow:0 12px 30px rgba(0,0,0,.2);animation:dropIn .25s ease;display:flex;align-items:center;gap:9px';
    t.style.background = type === 'danger' ? 'linear-gradient(135deg,#ef4444,#f97316)' : 'linear-gradient(135deg,#6366f1,#8b5cf6)';
    t.innerHTML = `<i class="fas ${type === 'danger' ? 'fa-circle-exclamation' : 'fa-circle-check'}"></i>` + msg;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300); }, 2400);
}

/* ── Scroll reveal ───────────────────────────────────────────── */
let revealObserver;
function observeReveals() {
    if (!revealObserver) {
        revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('visible'); revealObserver.unobserve(en.target); } });
        }, { threshold: 0.12 });
    }
    document.querySelectorAll('.reveal:not(.visible)').forEach(el => revealObserver.observe(el));
}

/* ── Init ────────────────────────────────────────────────────── */
(async function () {
    await loadHeroSlider();
    loadCartCount();
    observeReveals();
})();
</script>
</body>
</html>