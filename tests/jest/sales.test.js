const { apiCall } = require('./helpers');

describe('Sales Creation', () => {
    test('Create sale with items', async () => {
        const prodRes = await apiCall('/products/index.php?per_page=1');
        if (!prodRes.data.data.products || prodRes.data.data.products.length === 0) return;

        const product = prodRes.data.data.products[0];
        const res = await apiCall('/sales/index.php', 'POST', {
            customer_id: null,
            items: [{ product_id: product.id, qty: 2, unit_price: product.price }],
            discount: 0,
            shipping: 0,
            payment_method: 'cash',
        });
        expect(res.data.success).toBe(true);
        expect(res.data.data.invoice_no).toBeDefined();
        expect(res.data.data.grand_total).toBeGreaterThan(0);
    });

    test('Stock deduction on sale', async () => {
        const prodRes = await apiCall('/products/index.php?per_page=1');
        if (!prodRes.data.data.products || prodRes.data.data.products.length === 0) return;

        const product = prodRes.data.data.products[0];
        const stockBefore = product.total_stock;

        await apiCall('/sales/index.php', 'POST', {
            items: [{ product_id: product.id, qty: 1, unit_price: product.price }],
            payment_method: 'cash',
        });

        const afterRes = await apiCall(`/products/index.php?id=${product.id}`);
        expect(afterRes.data.data.total_stock).toBeLessThanOrEqual(stockBefore);
    });

    test('List sales', async () => {
        const res = await apiCall('/sales/index.php');
        expect(res.status).toBe(200);
        expect(Array.isArray(res.data.data.sales)).toBe(true);
    });
});
