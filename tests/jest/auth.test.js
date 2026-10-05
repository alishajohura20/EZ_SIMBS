const { apiCall, loginAs } = require('./helpers');

describe('Authentication', () => {
    test('Login with valid credentials', async () => {
        const res = await loginAs();
        expect(res.status).toBe(200);
        expect(res.data.success).toBe(true);
        expect(res.data.data.role).toBeDefined();
    });

    test('Login with invalid credentials', async () => {
        const res = await apiCall('/auth/login.php', 'POST', { email: 'wrong@email.com', password: 'wrongpass' });
        expect(res.data.success).toBe(false);
    });

    test('Login with empty credentials', async () => {
        const res = await apiCall('/auth/login.php', 'POST', { email: '', password: '' });
        expect(res.data.success).toBe(false);
    });

    test('Admin role returned on login', async () => {
        const res = await loginAs();
        expect(res.data.data.role).toBe('admin');
    });

    test('Password hashing verification', async () => {
        const res = await apiCall('/auth/login.php', 'POST', { email: 'admin@ezsimbs.local', password: 'admin123' });
        expect(res.data.success).toBe(true);
    });
});
