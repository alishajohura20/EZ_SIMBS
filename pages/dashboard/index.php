<?php
$pageTitle = 'Dashboard';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>
    <h4 class="mb-4">Dashboard</h4>

    <div id="lowStockBanner" class="alert alert-warning d-none">
        <i class="fas fa-exclamation-triangle"></i>
        <span id="lowStockMessage"></span>
        <a href="../notifications/reorder-suggestions.php" class="alert-link">View Suggestions</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-stat">
                <div class="d-flex justify-content-between">
                    <div><h3 id="todaySales">$0</h3><p>Today's Sales</p></div>
                    <div class="text-primary"><i class="fas fa-dollar-sign fa-3x opacity-25"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-stat">
                <div class="d-flex justify-content-between">
                    <div><h3 id="monthlySales">$0</h3><p>Monthly Sales</p></div>
                    <div class="text-success"><i class="fas fa-chart-line fa-3x opacity-25"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-stat">
                <div class="d-flex justify-content-between">
                    <div><h3 id="monthlyProfit">$0</h3><p>Monthly Profit</p></div>
                    <div class="text-info"><i class="fas fa-coins fa-3x opacity-25"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-stat">
                <div class="d-flex justify-content-between">
                    <div><h3 id="todayCount">0</h3><p>Today's Orders</p></div>
                    <div class="text-warning"><i class="fas fa-receipt fa-3x opacity-25"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-stat">
                <div class="d-flex justify-content-between">
                    <div><h3 id="totalProducts">0</h3><p>Products</p></div>
                    <div class="text-secondary"><i class="fas fa-box fa-3x opacity-25"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-stat">
                <div class="d-flex justify-content-between">
                    <div><h3 id="totalCustomers">0</h3><p>Customers</p></div>
                    <div class="text-secondary"><i class="fas fa-users fa-3x opacity-25"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-stat">
                <div class="d-flex justify-content-between">
                    <div><h3 id="lowStock" class="text-danger">0</h3><p>Low Stock</p></div>
                    <div class="text-danger"><i class="fas fa-exclamation-triangle fa-3x opacity-25"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card card-stat">
                <div class="d-flex justify-content-between">
                    <div><h3 id="pendingPayments" class="text-warning">$0</h3><p>Pending Payments</p></div>
                    <div class="text-warning"><i class="fas fa-clock fa-3x opacity-25"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Sales Overview</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-primary active" onclick="loadChart('daily',this)">Daily</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="loadChart('weekly',this)">Weekly</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="loadChart('monthly',this)">Monthly</button>
                    </div>
                </div>
                <div class="card-body"><canvas id="salesChart" height="300"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h5>Top Selling Products</h5></div>
                <div class="card-body" id="topProducts" style="max-height:350px;overflow-y:auto;"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Recent Sales</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Method</th><th>Date</th></tr></thead>
                        <tbody id="recentSales"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Low Stock Alert</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Product</th><th>SKU</th><th>Stock</th><th>Reorder</th><th>Branch</th></tr></thead>
                        <tbody id="lowStockTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let salesChart;
async function loadDashboard() {
    const [statsRes, chartsRes] = await Promise.all([
        apiRequest('../../api/dashboard/stats.php'),
        apiRequest('../../api/dashboard/charts.php?period=monthly'),
    ]);
    if (statsRes.success) {
        const s = statsRes.data;
        document.getElementById('todaySales').textContent = '$' + parseFloat(s.today_sales).toFixed(0);
        document.getElementById('monthlySales').textContent = '$' + parseFloat(s.monthly_sales).toFixed(0);
        document.getElementById('monthlyProfit').textContent = '$' + parseFloat(s.monthly_profit).toFixed(0);
        document.getElementById('todayCount').textContent = s.today_count;
        document.getElementById('totalProducts').textContent = s.total_products;
        document.getElementById('totalCustomers').textContent = s.total_customers;
        document.getElementById('lowStock').textContent = s.low_stock;
        document.getElementById('pendingPayments').textContent = '$' + parseFloat(s.pending_payments).toFixed(0);

        if (parseInt(s.low_stock) > 0) {
            document.getElementById('lowStockBanner').classList.remove('d-none');
            document.getElementById('lowStockMessage').textContent =
                'You have ' + s.low_stock + ' products below reorder level. ';
        }
    }
    if (chartsRes.success) {
        renderSalesChart(chartsRes.data.sales_chart);
        renderTopProducts(chartsRes.data.top_products);
        renderRecentSales(chartsRes.data.recent_sales);
        renderLowStock(chartsRes.data.low_stock);
    }
}
function renderSalesChart(data) {
    const ctx = document.getElementById('salesChart').getContext('2d');
    if (salesChart) salesChart.destroy();
    salesChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: data.map(d => d.label), datasets: [{ label: 'Sales ($)', data: data.map(d => parseFloat(d.total)), backgroundColor: 'rgba(79,70,229,0.5)', borderColor: 'rgba(79,70,229,1)', borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
}
async function loadChart(period, btn) {
    document.querySelectorAll('[onclick^="loadChart"]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const res = await apiRequest(`../../api/dashboard/charts.php?period=${period}`);
    if (res.success) renderSalesChart(res.data.sales_chart);
}
function renderTopProducts(products) {
    document.getElementById('topProducts').innerHTML = products.map((p, i) =>
        `<div class="d-flex align-items-center mb-2"><span class="badge bg-primary me-2">${i+1}</span><div><small class="fw-bold">${p.name}</small><br><small class="text-muted">${p.total_sold} sold - $${parseFloat(p.revenue).toFixed(2)}</small></div></div>`
    ).join('');
}
function renderRecentSales(sales) {
    document.getElementById('recentSales').innerHTML = sales.map(s =>
        `<tr><td><code>${s.invoice_no}</code></td><td>${s.customer_name||'Walk-in'}</td><td>$${parseFloat(s.grand_total).toFixed(2)}</td><td><span class="badge bg-secondary">${s.payment_method}</span></td><td>${new Date(s.created_at).toLocaleDateString()}</td></tr>`
    ).join('');
}
function renderLowStock(items) {
    document.getElementById('lowStockTable').innerHTML = items.map(i =>
        `<tr><td>${i.name}</td><td><code>${i.sku}</code></td><td><span class="badge bg-danger">${i.qty}</span></td><td>${i.reorder_level}</td><td>${i.branch_name}</td></tr>`
    ).join('');
}
loadDashboard();
</script>

<?php require_once '../../includes/footer.php'; ?>
