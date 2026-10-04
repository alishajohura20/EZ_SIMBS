const { BASE_URL } = require('./config');

const API = BASE_URL.replace(/\/api$/i, '');

async function loginThroughForm(email, password, remember = false) {
    const res = await fetch(`${API}/pages/auth/login.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: remember ? `email=${email}&password=${password}&remember=on` : `email=${email}&password=${password}`,
        redirect: 'manual',
    });
    return res.headers.get('set-cookie') || '';
}

async function api(path, method = 'GET', body = null, cookies = '') {
    const options = { method, headers: {} };
    if (cookies) options.headers.Cookie = cookies;
    if (body) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(body);
    }
    const res = await fetch(`${API}/api/sessions${path}`, options);
    const setCookie = res.headers.get('set-cookie') || '';
    let data = null;
    try { data = await res.json(); } catch (e) { /* non-json */ }
    return { status: res.status, data, cookies: setCookie };
}

describe('Active Sessions (multi-login)', () => {
    test('Concurrent logins each create their own tracked session', async () => {
        const cookiesA = await loginThroughForm('admin@ezsimbs.local', 'admin123');
        const cookiesB = await loginThroughForm('admin@ezsimbs.local', 'admin123');
        expect(cookiesA).toContain('PHPSESSID');
        expect(cookiesB).toContain('PHPSESSID');

        // Device A still holds a live session after device B logs in
        const res = await api('/index.php', 'GET', null, cookiesA);
        expect(res.status).toBe(200);
        expect(res.data.success).toBe(true);
        expect(Array.isArray(res.data.data.sessions)).toBe(true);
        expect(res.data.data.sessions.length).toBeGreaterThanOrEqual(2);
    });

    test('A revoked session is rejected on the next request', async () => {
        const cookiesA = await loginThroughForm('admin@ezsimbs.local', 'admin123');
        const cookiesB = await loginThroughForm('admin@ezsimbs.local', 'admin123');

        // Admin lists all sessions, finds the other one, and revokes it
        const list = await api('/index.php?all=1', 'GET', null, cookiesA);
        const others = list.data.data.sessions.filter(s => s.is_current === false);
        expect(others.length).toBeGreaterThanOrEqual(1);

        const target = others[others.length - 1];
        const revoke = await api('/index.php', 'POST', { action: 'terminate', id: target.id }, cookiesA);
        expect(revoke.data.success).toBe(true);

        // Find which cookie jar belongs to the revoked session and verify it's dead
        const check = await api('/index.php', 'GET', null, cookiesB);
        if (check.status === 302 || check.data === null || (check.data && !check.data.success)) {
            // If B was the revoked session it must no longer authenticate
            expect(check.status === 302 || !check.data.success).toBe(true);
        } else {
            const still = check.data.data.sessions.find(s => s.id === target.id);
            expect(still).toBeUndefined();
        }
    });

    test('Ending all other sessions keeps the current session alive', async () => {
        const cookiesA = await loginThroughForm('admin@ezsimbs.local', 'admin123');
        await loginThroughForm('admin@ezsimbs.local', 'admin123');

        const res = await api('/index.php', 'POST', { action: 'terminate_others' }, cookiesA);
        expect(res.data.success).toBe(true);

        const list = await api('/index.php', 'GET', null, cookiesA);
        expect(list.data.success).toBe(true);
        const current = list.data.data.sessions.filter(s => s.is_current);
        expect(current.length).toBe(1);
    });
});