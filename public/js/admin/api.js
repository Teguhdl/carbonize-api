const API = {
    baseUrl: '/api/v1',
    sanctumToken: localStorage.getItem('sanctum_token'),
    customToken: localStorage.getItem('custom_token'),

    headers() {
        const h = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
        if (this.sanctumToken) h['Authorization'] = `Bearer ${this.sanctumToken}`;
        if (this.customToken) h['X-Api-Token'] = this.customToken;
        return h;
    },

    async request(endpoint, method = 'GET', body = null) {
        const options = { method, headers: this.headers() };
        if (body) options.body = JSON.stringify(body);
        const res = await fetch(`${this.baseUrl}${endpoint}`, options);
        const data = await res.json();
        if (!res.ok) {
            if (res.status === 401) { App.logout(); return; }
            throw new Error(data.message || `HTTP ${res.status}`);
        }
        return data;
    },

    async upload(endpoint, formData) {
        const h = { 'Accept': 'application/json' };
        if (this.sanctumToken) h['Authorization'] = `Bearer ${this.sanctumToken}`;
        if (this.customToken) h['X-Api-Token'] = this.customToken;
        const res = await fetch(`${this.baseUrl}${endpoint}`, { method: 'POST', headers: h, body: formData });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || `HTTP ${res.status}`);
        return data;
    }
};

const UI = {
    toast(msg, type = 'success') {
        const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
        const colors = { success: 'var(--success)', error: 'var(--danger)', info: 'var(--primary)' };
        const c = document.getElementById('toast-container');
        const d = document.createElement('div');
        d.className = 'toast';
        d.innerHTML = `<i class="fa-solid ${icons[type]}" style="color:${colors[type]}"></i> ${msg}`;
        c.appendChild(d);
        setTimeout(() => { d.style.opacity = '0'; setTimeout(() => d.remove(), 300); }, 3500);
    },

    switchView(name) {
        document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
        const view = document.getElementById(`view-${name}`);
        const nav  = document.querySelector(`[data-view="${name}"]`);
        if (view) view.classList.remove('hidden');
        if (nav)  nav.classList.add('active');
        const loaders = {
            food: 'loadFood', vehicles: 'loadVehicles',
            fuels: 'loadFuels', transit: 'loadTransit',
            history: 'loadHistory'
        };
        if (loaders[name]) App[loaders[name]]();
    },

    showModal(id) {
        const m = document.getElementById(id);
        if (m) m.classList.remove('hidden');
    },

    hideModal(id) {
        const m = document.getElementById(id);
        if (!m) return;
        m.classList.add('hidden');
        const f = m.querySelector('form');
        if (f) f.reset();
    },

    emptyRow(cols, msg) {
        return `<tr><td colspan="${cols}" style="text-align:center;color:var(--muted);padding:2rem">${msg}</td></tr>`;
    }
};
