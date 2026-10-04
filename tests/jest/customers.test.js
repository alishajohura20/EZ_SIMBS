const { apiCall } = require('./helpers');

describe('Customer CRUD', () => {
    let customerId;

    test('Create customer', async () => {
        const res = await apiCall('/customers/index.php', 'POST', {
            name: 'Test Customer',
            phone: '555-0199',
            email: 'test@customer.com',
        });
        expect(res.data.success).toBe(true);
        customerId = res.data.data.id;
    });

    test('Get customer profile', async () => {
        const res = await apiCall(`/customers/index.php?id=${customerId}`);
        expect(res.data.data.customer.name).toBe('Test Customer');
        expect(res.data.data.stats).toBeDefined();
    });

    test('Customer lookup by phone', async () => {
        const res = await apiCall('/customers/lookup.php?phone=555-0199');
        expect(res.data.data.name).toBe('Test Customer');
    });

    test('Update customer to VIP', async () => {
        const res = await apiCall(`/customers/index.php?id=${customerId}`, 'PUT', { is_vip: 1 });
        expect(res.data.success).toBe(true);
    });

    test('List customers', async () => {
        const res = await apiCall('/customers/index.php');
        expect(res.status).toBe(200);
        expect(Array.isArray(res.data.data.customers)).toBe(true);
    });
});
