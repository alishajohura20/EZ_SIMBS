document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('show');
});

function highlightActiveNav() {
    const path = window.location.pathname;
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        const base = link.getAttribute('href') || '';
        const basePath = base.replace(/\/+$/, '');
        if (basePath && path.startsWith(basePath)) {
            link.classList.add('active');
        }
    });
}

function refreshNotifBadge() {
    const badge = document.getElementById('notifBadge');
    if (!badge) return;
    const appBase = (document.querySelector('.sidebar .nav-link')?.getAttribute('href') || '/').split('/pages/')[0];
    fetch(appBase + '/api/notifications/index.php?limit=1')
        .then(res => res.json())
        .then(json => {
            if (!json.success) return;
            const count = parseInt(json.data?.unread_count || 0);
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        })
        .catch(() => {});
}

highlightActiveNav();
refreshNotifBadge();
setInterval(refreshNotifBadge, 30000);

setTimeout(() => {
    document.querySelectorAll('.alert-dismissible').forEach(el => {
        el.classList.add('fade');
        setTimeout(() => el.remove(), 500);
    });
}, 5000);

function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    fetch('api/settings/index.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'set_theme', theme: next })
    });
}

async function confirmDelete(url) {
    if (confirm('Are you sure you want to delete this?')) {
        const res = await fetch(url, { method: 'DELETE' });
        const json = await res.json();
        if (json.success) {
            window.location.reload();
        } else {
            alert(json.message || 'Delete failed');
        }
    }
}

async function apiRequest(url, method = 'GET', data = null) {
    const options = {
        method,
        headers: { 'Content-Type': 'application/json' },
    };
    if (data) options.body = JSON.stringify(data);
    const res = await fetch(url, options);
    return res.json();
}

function renderPagination(p, loader) {
    const el = document.getElementById('pagination');
    if (!el || !p) return;
    const fn = typeof loader === 'function' ? loader.name : String(loader);
    const current = parseInt(p.current_page) || 1;
    const totalPages = parseInt(p.total_pages) || 1;
    const total = parseInt(p.total) || 0;
    const perPage = parseInt(p.per_page) || 20;

    let html = '<nav><ul class="pagination mb-0">';
    html += `<li class="page-item ${current<=1?'disabled':''}"><a class="page-link" href="#" onclick="${fn}(${current-1});return false;">&laquo;</a></li>`;
    for (let i = 1; i <= totalPages; i++) {
        html += `<li class="page-item ${i===current?'active':''}"><a class="page-link" href="#" onclick="${fn}(${i});return false;">${i}</a></li>`;
    }
    html += `<li class="page-item ${current>=totalPages?'disabled':''}"><a class="page-link" href="#" onclick="${fn}(${current+1});return false;">&raquo;</a></li>`;
    html += '</ul></nav>';
    if (total > 0) {
        const from = (current - 1) * perPage + 1;
        const to = Math.min(current * perPage, total);
        html += `<span class="ms-2 text-muted align-self-center small">Showing ${from}–${to} of ${total}</span>`;
    }
    el.innerHTML = html;
}

function printSection(elementId) {
    const content = document.getElementById(elementId).innerHTML;
    const win = window.open('', '_blank');
    win.document.write(`<html><head><title>Print</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>${content}</body></html>`);
    win.document.close();
    win.print();
}
