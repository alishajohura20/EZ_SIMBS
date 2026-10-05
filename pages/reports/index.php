<?php
$pageTitle = 'Reports';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>
    <h4 class="mb-4">Reports & Analytics</h4>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#salesTab">Sales</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#inventoryTab">Inventory</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#profitTab">Profit/Loss</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#purchaseTab">Purchases</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#customerTab">Customers</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#returnsTab">Returns</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#purchaseReturnsTab">Supplier Returns</a></li>
    </ul>

    <div class="card mb-4"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><label class="form-label">From</label><input type="date" class="form-control" id="dateFrom" value="<?= date('Y-m-01') ?>"></div>
            <div class="col-md-3"><label class="form-label">To</label><input type="date" class="form-control" id="dateTo" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-3"><label class="form-label">&nbsp;</label><button class="btn btn-primary w-100" onclick="loadReport()">Generate</button></div>
        </div>
    </div></div>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="salesTab">
            <div class="row mb-4">
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="sOrders">0</h3><p>Orders</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="sRevenue">$0</h3><p>Revenue</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="sAvg">$0</h3><p>Avg Order</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="sDisc">$0</h3><p>Discounts</p></div></div>
            </div>
            <div class="card"><div class="card-body"><canvas id="salesReportChart" height="250"></canvas></div></div>
            <button class="btn btn-outline-success mt-2" onclick="exportReport('sales')"><i class="fas fa-download"></i> CSV</button>
        </div>
        <div class="tab-pane fade" id="inventoryTab">
            <div class="row mb-4">
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="iProducts">0</h3><p>Products</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="iQty">0</h3><p>Total Qty</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="iValue">$0</h3><p>Value</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="iOut" class="text-danger">0</h3><p>Out of Stock</p></div></div>
            </div>
            <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Product</th><th>SKU</th><th>Category</th><th>Qty</th><th>Value</th><th>Branch</th></tr></thead><tbody id="invTable"></tbody></table></div>
            <button class="btn btn-outline-success mt-2" onclick="exportReport('inventory')"><i class="fas fa-download"></i> CSV</button>
        </div>
        <div class="tab-pane fade" id="profitTab">
            <div class="row mb-4">
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="plRev">$0</h3><p>Revenue</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="plCOGS">$0</h3><p>COGS</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="plProfit" class="text-success">$0</h3><p>Gross Profit</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="plMargin">0%</h3><p>Margin</p></div></div>
            </div>
        </div>
        <div class="tab-pane fade" id="purchaseTab">
            <div class="row mb-4">
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="pOrders">0</h3><p>Orders</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="pTotal">$0</h3><p>Total</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="pPaid">$0</h3><p>Paid</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="pDue" class="text-danger">$0</h3><p>Due</p></div></div>
            </div>
            <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Supplier</th><th>Orders</th><th>Total</th></tr></thead><tbody id="purTable"></tbody></table></div>
        </div>
        <div class="tab-pane fade" id="customerTab">
            <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Customer</th><th>Phone</th><th>Orders</th><th>Spent</th><th>Points</th><th>VIP</th></tr></thead><tbody id="custTable"></tbody></table></div>
            <button class="btn btn-outline-success mt-2" onclick="exportReport('customers')"><i class="fas fa-download"></i> CSV</button>
        </div>
        <div class="tab-pane fade" id="returnsTab">
            <div class="row mb-4">
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="rTotal">0</h3><p>Returns</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="rRefunded">$0</h3><p>Refunded</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="rRate">0%</h3><p>Return Rate</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="rOpen">0</h3><p>Open Requests</p></div></div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card"><div class="card-body"><h6 class="text-muted text-uppercase small">By Reason</h6>
                        <div class="table-responsive"><table class="table mb-0">
                            <thead><tr><th>Reason</th><th class="text-end">Count</th><th class="text-end">Refunded</th></tr></thead>
                            <tbody id="returnsByReason"></tbody>
                        </table></div>
                    </div></div>
                </div>
                <div class="col-md-6">
                    <div class="card"><div class="card-body"><h6 class="text-muted text-uppercase small">By Branch</h6>
                        <div class="table-responsive"><table class="table mb-0">
                            <thead><tr><th>Branch</th><th class="text-end">Count</th><th class="text-end">Refunded</th></tr></thead>
                            <tbody id="returnsByBranch"></tbody>
                        </table></div>
                    </div></div>
                </div>
            </div>
            <button class="btn btn-outline-success mt-2" onclick="exportReport('returns')"><i class="fas fa-download"></i> CSV</button>
        </div>
        <div class="tab-pane fade" id="purchaseReturnsTab">
            <div class="row mb-4">
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="prTotal">0</h3><p>Returns</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="prCredited">$0</h3><p>Credited</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="prOpen">0</h3><p>Open Returns</p></div></div>
                <div class="col-md-3"><div class="card card-stat text-center"><h3 id="prBalance">$0</h3><p>To Balance</p></div></div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card"><div class="card-body"><h6 class="text-muted text-uppercase small">By Reason</h6>
                        <div class="table-responsive"><table class="table mb-0">
                            <thead><tr><th>Reason</th><th class="text-end">Count</th><th class="text-end">Credited</th></tr></thead>
                            <tbody id="purchaseReturnsByReason"></tbody>
                        </table></div>
                    </div></div>
                </div>
                <div class="col-md-6">
                    <div class="card"><div class="card-body"><h6 class="text-muted text-uppercase small">By Supplier</h6>
                        <div class="table-responsive"><table class="table mb-0">
                            <thead><tr><th>Supplier</th><th class="text-end">Count</th><th class="text-end">Credited</th></tr></thead>
                            <tbody id="purchaseReturnsBySupplier"></tbody>
                        </table></div>
                    </div></div>
                </div>
            </div>
            <button class="btn btn-outline-success mt-2" onclick="exportReport('purchase-returns')"><i class="fas fa-download"></i> CSV</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let salesReportChart;
async function loadReport() {
    const p = `date_from=${document.getElementById('dateFrom').value}&date_to=${document.getElementById('dateTo').value}`;
    const [sR,iR,plR,pR,cR,rR,prR] = await Promise.all([
        apiRequest(`../../api/reports/sales.php?${p}`), apiRequest(`../../api/reports/inventory.php?${p}`),
        apiRequest(`../../api/reports/profit-loss.php?${p}`), apiRequest(`../../api/reports/purchases.php?${p}`),
        apiRequest(`../../api/reports/customers.php`), apiRequest(`../../api/reports/returns.php?${p}`),
        apiRequest(`../../api/reports/purchase-returns.php?${p}`)
    ]);
    if (sR.success) { const s=sR.data.summary; document.getElementById('sOrders').textContent=s.total_orders; document.getElementById('sRevenue').textContent='$'+parseFloat(s.grand_total).toFixed(0); document.getElementById('sAvg').textContent='$'+parseFloat(s.avg_order).toFixed(2); document.getElementById('sDisc').textContent='$'+parseFloat(s.discounts).toFixed(2); renderSalesChart(sR.data.daily); }
    if (iR.success) { const i=iR.data.summary; document.getElementById('iProducts').textContent=i.total_products; document.getElementById('iQty').textContent=i.total_qty; document.getElementById('iValue').textContent='$'+parseFloat(i.total_value).toFixed(0); document.getElementById('iOut').textContent=i.out_of_stock; document.getElementById('invTable').innerHTML=iR.data.details.map(d=>`<tr><td>${d.name}</td><td><code>${d.sku}</code></td><td>${d.category||'-'}</td><td>${d.qty}</td><td>$${parseFloat(d.value).toFixed(2)}</td><td>${d.branch}</td></tr>`).join(''); }
    if (plR.success) { const p=plR.data; document.getElementById('plRev').textContent='$'+parseFloat(p.revenue).toFixed(0); document.getElementById('plCOGS').textContent='$'+parseFloat(p.cost_of_goods).toFixed(0); document.getElementById('plProfit').textContent='$'+parseFloat(p.gross_profit).toFixed(0); document.getElementById('plMargin').textContent=parseFloat(p.profit_margin).toFixed(1)+'%'; }
    if (pR.success) { const s=pR.data.summary; document.getElementById('pOrders').textContent=s.total_orders; document.getElementById('pTotal').textContent='$'+parseFloat(s.total_amount).toFixed(0); document.getElementById('pPaid').textContent='$'+parseFloat(s.total_paid).toFixed(0); document.getElementById('pDue').textContent='$'+parseFloat(s.total_due).toFixed(0); document.getElementById('purTable').innerHTML=pR.data.by_supplier.map(s=>`<tr><td>${s.company_name}</td><td>${s.orders}</td><td>$${parseFloat(s.total).toFixed(2)}</td></tr>`).join(''); }
    if (cR.success) document.getElementById('custTable').innerHTML=cR.data.map(c=>`<tr><td>${c.name}</td><td>${c.phone||'-'}</td><td>${c.orders}</td><td>$${parseFloat(c.total_spent).toFixed(2)}</td><td>${c.loyalty_points}</td><td>${c.is_vip?'<i class="fas fa-crown text-warning"></i>':'-'}</td></tr>`).join('');
    if (rR.success) { const r=rR.data; document.getElementById('rTotal').textContent=r.total_returns; document.getElementById('rRefunded').textContent='$'+parseFloat(r.refunded_total).toFixed(2); document.getElementById('rRate').textContent=r.return_rate+'%'; document.getElementById('rOpen').textContent=r.open_requests;
        document.getElementById('returnsByReason').innerHTML=(r.by_reason||[]).map(x=>`<tr><td class="text-capitalize">${(x.reason_code||'-').replace(/_/g,' ')}</td><td class="text-end">${x.count}</td><td class="text-end">$${Number(x.total).toFixed(2)}</td></tr>`).join('')||'<tr><td colspan="3" class="text-center text-muted">No returns in this period</td></tr>';
        document.getElementById('returnsByBranch').innerHTML=(r.by_branch||[]).map(x=>`<tr><td>${x.name||'-'}</td><td class="text-end">${x.count}</td><td class="text-end">$${Number(x.total).toFixed(2)}</td></tr>`).join('')||'<tr><td colspan="3" class="text-center text-muted">No returns in this period</td></tr>';
    }
    if (prR.success) { const pr=prR.data; document.getElementById('prTotal').textContent=pr.total_returns; document.getElementById('prCredited').textContent='$'+parseFloat(pr.credited_total).toFixed(2); document.getElementById('prOpen').textContent=pr.open_returns; document.getElementById('prBalance').textContent='$'+parseFloat(pr.to_balance).toFixed(2);
        document.getElementById('purchaseReturnsByReason').innerHTML=(pr.by_reason||[]).map(x=>`<tr><td class="text-capitalize">${(x.reason_code||'-').replace(/_/g,' ')}</td><td class="text-end">${x.count}</td><td class="text-end">$${Number(x.total).toFixed(2)}</td></tr>`).join('')||'<tr><td colspan="3" class="text-center text-muted">No supplier returns in this period</td></tr>';
        document.getElementById('purchaseReturnsBySupplier').innerHTML=(pr.by_supplier||[]).map(x=>`<tr><td>${x.company_name||'-'}</td><td class="text-end">${x.count}</td><td class="text-end">$${Number(x.total).toFixed(2)}</td></tr>`).join('')||'<tr><td colspan="3" class="text-center text-muted">No supplier returns in this period</td></tr>';
    }
}
function renderSalesChart(data) {
    const ctx = document.getElementById('salesReportChart').getContext('2d');
    if (salesReportChart) salesReportChart.destroy();
    salesReportChart = new Chart(ctx, { type: 'line', data: { labels: data.map(d=>d.date), datasets: [{ label: 'Sales', data: data.map(d=>parseFloat(d.total)), borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,0.1)', fill: true, tension: 0.3 }] }, options: { responsive: true, maintainAspectRatio: false } });
}
function exportReport(type) { window.open(`../../api/reports/${type}.php?export=csv&date_from=${document.getElementById('dateFrom').value}&date_to=${document.getElementById('dateTo').value}`); }
loadReport();
</script>

<?php require_once '../../includes/footer.php'; ?>
