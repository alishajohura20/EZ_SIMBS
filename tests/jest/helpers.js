const { BASE_URL } = require('./config');

async function apiCall(endpoint, method = 'GET', body = null, cookies = '') {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Cookie': cookies,
        },
    };
    if (body) options.body = JSON.stringify(body);
    const res = await fetch(`${BASE_URL}${endpoint}`, options);
    const setCookie = res.headers.get('set-cookie') || '';
    const data = await res.json();
    return { status: res.status, data, cookies: setCookie };
}

async function loginAs(email = 'admin@ezsimbs.local', password = 'admin123') {
    const res = await apiCall('/auth/login.php', 'POST', { email, password });
    return res;
}

module.exports = { apiCall, loginAs };
