const { apiCall } = require('./helpers');

describe('Dashboard Stats', () => {
    test('Get dashboard stats', async () => {
        const res = await apiCall('/dashboard/stats.php');
        expect(res.status).toBe(200);
        expect(res.data.data).toHaveProperty('today_sales');
        expect(res.data.data).toHaveProperty('monthly_sales');
        expect(res.data.data).toHaveProperty('total_products');
        expect(res.data.data).toHaveProperty('low_stock');
    });

    test('Get chart data', async () => {
        const res = await apiCall('/dashboard/charts.php?period=monthly');
        expect(res.status).toBe(200);
        expect(res.data.data.sales_chart).toBeDefined();
        expect(res.data.data.top_products).toBeDefined();
    });

    test('Stats return numeric values', async () => {
        const res = await apiCall('/dashboard/stats.php');
        expect(typeof res.data.data.today_sales).toBe('number');
        expect(typeof res.data.data.total_products).toBe('number');
    });
});
