<?php
$pageTitle = 'Manager Dashboard';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin', 'manager']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

$dash = new Dashboard();
$stats = $dash->getStats();
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h4 class="mb-0" style="font-weight:700"><i class="fas fa-user-tie me-2 text-primary"></i>Manager Dashboard</h4>
        <div class="d-flex gap-2">
            <a href="../reports/" class="btn btn-sm btn-outline-primary"><i class="fas fa-chart-bar me-1"></i> Full Reports</a>
            <a href="../store/roles.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-user-shield me-1"></i> Role Dashboards</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)$stats['today_sales'], 0) ?></h3><p>Today's Sales</p></div><div class="text-primary"><i class="fas fa-dollar-sign fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)$stats['monthly_sales'], 0) ?></h3><p>Monthly Sales</p></div><div class="text-success"><i class="fas fa-chart-line fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)$stats['monthly_profit'], 0) ?></h3><p>Monthly Profit</p></div><div class="text-info"><i class="fas fa-coins fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3><?= (int)$stats['total_customers'] ?></h3><p>Customers</p></div><div class="text-warning"><i class="fas fa-users fa-3x opacity-25"></i></div></div></div></div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5>Sales Overview (Monthly)</h5></div>
                <div class="card-body"><canvas id="mgrSalesChart" height="290"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-header"><h5>Operational Focus</h5></div>
                <div class="card-body d-grid gap-2">
                    <a href="../purchases/" class="btn btn-outline-primary"><i class="fas fa-shopping-cart me-2"></i> Purchases</a>
                    <a href="../suppliers/" class="btn btn-outline-primary"><i class="fas fa-truck me-2"></i> Suppliers</a>
                    <a href="../customers/" class="btn btn-outline-primary"><i class="fas fa-users me-2"></i> Customers</a>
                    <a href="../sales/" class="btn btn-outline-primary"><i class="fas fa-cash-register me-2"></i> Sales History</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
fetch('../api/dashboard/charts.php?period=monthly').then(r=>r.json()).then(res=>{
    const d = res.data.sales_chart || [];
    new Chart(document.getElementById('mgrSalesChart'), {
        type: 'line',
        data: { labels: d.map(x=>x.label), datasets: [{ label:'Sales ($)', data: d.map(x=>parseFloat(x.total)), borderColor:'#6366f1', backgroundColor:'rgba(99,102,241,.15)', fill:true, tension:.35, pointRadius:4 }] },
        options: { responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } } }
    });
});
</script>
<?php require_once '../../includes/footer.php'; ?>