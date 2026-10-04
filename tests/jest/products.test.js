const { apiCall } = require('./helpers');
let authToken = '';

beforeAll(async () => {
    const res = await apiCall('/auth/login.php', 'POST', { email: 'admin@ezsimbs.local', password: 'admin123' });
    authToken = res.cookies;
});

describe('Product CRUD', () => {
    let productId;

    test('Create product', async () => {
        const res = await apiCall('/products/index.php', 'POST', {
            name: 'Test Product',
            price: 29.99,
            cost: 15.00,
            category_id: 1,
        }, authToken);
        expect(res.status).toBe(200);
        expect(res.data.success).toBe(true);
        productId = res.data.data.id;
    });

    test('Get product by ID', async () => {
        const res = await apiCall(`/products/index.php?id=${productId}`, 'GET', null, authToken);
        expect(res.status).toBe(200);
        expect(res.data.data.name).toBe('Test Product');
        expect(res.data.data.sku).toBeDefined();
    });

    test('List products with search', async () => {
        const res = await apiCall('/products/index.php?search=Test', 'GET', null, authToken);
        expect(res.status).toBe(200);
        expect(res.data.data.products.length).toBeGreaterThan(0);
    });

    test('Update product', async () => {
        const res = await apiCall(`/products/index.php?id=${productId}`, 'PUT', { name: 'Updated Product', price: 39.99 }, authToken);
        expect(res.status).toBe(200);
        expect(res.data.success).toBe(true);
    });

    test('Delete product', async () => {
        const res = await apiCall(`/products/index.php?id=${productId}`, 'DELETE', null, authToken);
        expect(res.status).toBe(200);
        expect(res.data.success).toBe(true);
    });

    test('SKU format validation', async () => {
        const res = await apiCall('/products/index.php?search=', 'GET', null, authToken);
        expect(res.status).toBe(200);
        res.data.data.products.forEach(p => {
            expect(p.sku).toMatch(/^SKU-/);
        });
    });
});
