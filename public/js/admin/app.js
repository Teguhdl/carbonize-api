const App = {
    user: null,

    init() {
        const token  = localStorage.getItem('sanctum_token');
        const ctoken = localStorage.getItem('custom_token');
        const user   = localStorage.getItem('user');
        if (token && ctoken && user) {
            API.sanctumToken = token;
            API.customToken  = ctoken;
            this.user = JSON.parse(user);
            this.showApp();
        }
        this.bindEvents();
    },

    showApp() {
        document.getElementById('login-view').classList.add('hidden');
        document.getElementById('app-view').classList.remove('hidden');
        document.getElementById('user-name').innerText  = this.user.name;
        document.getElementById('user-email').innerText = this.user.email;
        const pName = document.getElementById('profile-name');
        const pEmail = document.getElementById('profile-email');
        if (pName) pName.value  = this.user.name;
        if (pEmail) pEmail.value = this.user.email;
        this.loadDashboard();
    },

    async logout() {
        try { await API.request('/auth/logout', 'POST'); } catch (e) {}
        localStorage.clear();
        window.location.reload();
    },

    /* ── DASHBOARD ─────────────────────────────── */
    async loadDashboard() {
        try {
            const [f, v, t, h] = await Promise.all([
                API.request('/food-packaging/items'),
                API.request('/transport/private/vehicles'),
                API.request('/transport/public/vehicles'),
                API.request('/entries'),
            ]);
            document.getElementById('stat-food').innerText    = (f.data || []).length;
            document.getElementById('stat-veh').innerText     = (v.data || []).length;
            document.getElementById('stat-transit').innerText = (t.data || []).length;
            document.getElementById('stat-entries').innerText = (h.data || []).length;
        } catch (e) {}
    },

    /* ── FOOD & PACKAGING ─────────────────────── */
    async loadFood() {
        const method = document.getElementById('food-filter')?.value;
        const url = method ? `/food-packaging/items?method=${method}` : '/food-packaging/items';
        try {
            const res = await API.request(url);
            const rows = (res.data || []).map(i => `
                <tr>
                    <td>${i.id}</td>
                    <td>${i.name}</td>
                    <td><span class="badge ${i.calculation_method==='climatiq'?'badge-blue':'badge-green'}">${i.calculation_method}</span></td>
                    <td>${i.emission_factor ?? '-'}</td>
                    <td style="font-size:0.75rem;color:var(--muted);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${i.climatiq_id ?? '-'}</td>
                    <td style="display:flex;gap:0.25rem">
                        <button class="btn btn-primary btn-sm" onclick='App.editFood(${JSON.stringify(i).replace(/'/g,"\\'")})'><i class="fa-solid fa-pen"></i></button>
                        <button class="btn btn-danger btn-sm" onclick="App.del('/food-packaging/items/${i.id}','loadFood')"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>`).join('') || UI.emptyRow(6, 'Belum ada data food item');
            document.getElementById('food-tbody').innerHTML = rows;
        } catch (e) {}
    },

    /* ── VEHICLES ─────────────────────────────── */
    async loadVehicles() {
        try {
            const res = await API.request('/transport/private/vehicles');
            document.getElementById('vehicles-tbody').innerHTML = (res.data || []).map(v => `
                <tr>
                    <td>${v.id}</td><td>${v.name}</td>
                    <td><span class="badge badge-orange">${v.default_efficiency} km/L</span></td>
                    <td style="display:flex;gap:0.25rem">
                        <button class="btn btn-primary btn-sm" onclick='App.editVehicle(${JSON.stringify(v).replace(/'/g,"\\'")})'><i class="fa-solid fa-pen"></i></button>
                        <button class="btn btn-danger btn-sm" onclick="App.del('/transport/private/vehicles/${v.id}','loadVehicles')"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>`).join('') || UI.emptyRow(4, 'Belum ada data kendaraan');
        } catch (e) {}
    },

    /* ── FUELS ────────────────────────────────── */
    async loadFuels() {
        try {
            const res = await API.request('/transport/private/fuels');
            document.getElementById('fuels-tbody').innerHTML = (res.data || []).map(f => `
                <tr>
                    <td>${f.id}</td><td>${f.name}</td>
                    <td><span class="badge badge-blue">${f.emission_factor} kgCO2e/L</span></td>
                    <td style="display:flex;gap:0.25rem">
                        <button class="btn btn-primary btn-sm" onclick='App.editFuel(${JSON.stringify(f).replace(/'/g,"\\'")})'><i class="fa-solid fa-pen"></i></button>
                        <button class="btn btn-danger btn-sm" onclick="App.del('/transport/private/fuels/${f.id}','loadFuels')"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>`).join('') || UI.emptyRow(4, 'Belum ada data BBM');
        } catch (e) {}
    },

    /* ── TRANSIT ──────────────────────────────── */
    async loadTransit() {
        try {
            const res = await API.request('/transport/public/vehicles');
            document.getElementById('transit-tbody').innerHTML = (res.data || []).map(t => `
                <tr>
                    <td>${t.id}</td><td>${t.name}</td>
                    <td>${t.emission_factor}</td>
                    <td><span class="badge badge-purple">${t.avg_passengers} org</span></td>
                    <td style="display:flex;gap:0.25rem">
                        <button class="btn btn-primary btn-sm" onclick='App.editTransit(${JSON.stringify(t).replace(/'/g,"\\'")})'><i class="fa-solid fa-pen"></i></button>
                        <button class="btn btn-danger btn-sm" onclick="App.del('/transport/public/vehicles/${t.id}','loadTransit')"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>`).join('') || UI.emptyRow(5, 'Belum ada data transit');
        } catch (e) {}
    },

    /* ── HISTORY ──────────────────────────────── */
    async loadHistory() {
        const type = document.getElementById('history-filter')?.value;
        const url  = type ? `/entries?entry_type=${type}` : '/entries';
        try {
            const res = await API.request(url);
            const entries = res.data || [];
            if (!entries.length) {
                document.getElementById('history-tbody').innerHTML = UI.emptyRow(6, 'Belum ada riwayat konsumsi');
                return;
            }
            document.getElementById('history-tbody').innerHTML = entries.map(e => {
                let detail = '-';
                const badgeClass = { food:'badge-green', private_vehicle:'badge-blue', public_transit:'badge-purple' };
                if (e.entry_type === 'food')             detail = e.food_item?.name ?? '-';
                if (e.entry_type === 'private_vehicle')  detail = `${e.vehicle_type?.name ?? '-'} + ${e.fuel_type?.name ?? '-'}`;
                if (e.entry_type === 'public_transit')   detail = e.transit_vehicle?.name ?? '-';
                return `<tr>
                    <td>${e.entry_date}</td>
                    <td><span class="badge ${badgeClass[e.entry_type]}">${e.entry_type}</span></td>
                    <td>${detail}</td>
                    <td>${e.quantity}</td>
                    <td style="font-weight:600;color:var(--success)">${parseFloat(e.emissions).toFixed(4)} kgCO2e</td>
                    <td><button class="btn btn-danger btn-sm" onclick="App.del('/entries/${e.id}','loadHistory')"><i class="fa-solid fa-trash"></i></button></td>
                </tr>`;
            }).join('');
        } catch (e) {}
    },

    /* ── CONSUMPTION INPUT ────────────────────── */
    async openConsumptionModal() {
        // Pre-load all dropdown data
        try {
            const [foods, vehicles, fuels, transits] = await Promise.all([
                API.request('/food-packaging/items'),
                API.request('/transport/private/vehicles'),
                API.request('/transport/private/fuels'),
                API.request('/transport/public/vehicles'),
            ]);

            const sel = (id, data, label, val) =>
                `<option value="">${label}</option>` +
                data.map(d => `<option value="${d.id}">${d[val]}</option>`).join('');

            document.getElementById('cons-food-item').innerHTML   = sel('', foods.data||[], '-- Pilih Item --', 'name');
            document.getElementById('cons-vehicle').innerHTML     = sel('', vehicles.data||[], '-- Pilih Kendaraan --', 'name');
            document.getElementById('cons-fuel').innerHTML        = sel('', fuels.data||[], '-- Pilih BBM --', 'name');
            document.getElementById('cons-transit').innerHTML     = sel('', transits.data||[], '-- Pilih Kendaraan Umum --', 'name');
        } catch (e) { UI.toast('Gagal memuat data dropdown', 'error'); return; }

        this.consStep(1);
        UI.showModal('consumption-modal');
    },

    consStep(step) {
        ['step1','step2-food','step2-private','step2-public'].forEach(id => {
            const el = document.getElementById(`cons-${id}`);
            if (el) el.classList.add('hidden');
        });
        document.getElementById(`cons-step${step === 1 ? '1' : '2-' + this._consMode}`).classList.remove('hidden');

        document.querySelectorAll('#consumption-modal .step').forEach((el, i) => {
            el.classList.remove('active','done');
            if (i + 1 < step) el.classList.add('done');
            if (i + 1 === step) el.classList.add('active');
        });
    },

    consSelectMode(mode) {
        this._consMode = mode;
        this.consStep(2);
    },

    async submitConsumption(e) {
        e.preventDefault();
        const btns = document.querySelectorAll('#cons-submit-btn');
        btns.forEach(b => { b.disabled = true; b.innerText = 'Menyimpan...'; });

        try {
            let endpoint, body;
            const dateMap = { food: 'cons-date', private: 'cons-date-private', public: 'cons-date-public' };
            const date = document.getElementById(dateMap[this._consMode])?.value;

            if (this._consMode === 'food') {
                endpoint = '/food-packaging/entries';
                body = {
                    food_item_id: document.getElementById('cons-food-item').value,
                    quantity:     document.getElementById('cons-food-qty').value,
                    entry_date:   date,
                };
            } else if (this._consMode === 'private') {
                endpoint = '/transport/entries';
                body = {
                    mode:             'private',
                    vehicle_type_id:  document.getElementById('cons-vehicle').value,
                    fuel_type_id:     document.getElementById('cons-fuel').value,
                    quantity:         document.getElementById('cons-km').value,
                    custom_efficiency:document.getElementById('cons-custom-eff').value || null,
                    entry_date:       date,
                };
            } else {
                endpoint = '/transport/entries';
                body = {
                    mode:               'public',
                    transit_vehicle_id: document.getElementById('cons-transit').value,
                    quantity:           document.getElementById('cons-transit-km').value,
                    entry_date:         date,
                };
            }

            const res = await API.request(endpoint, 'POST', body);
            UI.toast(`✅ Emisi: ${parseFloat(res.data.emissions).toFixed(4)} kgCO2e`, 'success');
            UI.hideModal('consumption-modal');
            this.loadDashboard();
        } catch (err) {
            UI.toast(err.message || 'Gagal menyimpan', 'error');
        } finally {
            btns.forEach(b => { b.disabled = false; b.innerText = 'Hitung & Simpan'; });
        }
    },



    /* ── DELETE ───────────────────────────────── */
    async del(endpoint, reload) {
        if (!confirm('Yakin hapus data ini?')) return;
        try {
            await API.request(endpoint, 'DELETE');
            UI.toast('Data berhasil dihapus');
            this[reload]();
        } catch (e) {}
    },

    /* ── EDIT HELPERS ─────────────────────────── */
    editFood(item) {
        document.getElementById('food-id').value     = item.id;
        document.getElementById('food-name').value   = item.name;
        document.getElementById('food-method').value = item.calculation_method;
        document.getElementById('food-ef').value     = item.emission_factor ?? '';
        document.getElementById('food-cid').value    = item.climatiq_id ?? '';
        document.getElementById('food-modal-title').innerText = 'Edit Food Item';
        this.toggleFoodFields();
        UI.showModal('food-modal');
    },
    editVehicle(v) {
        document.getElementById('vehicle-id').value   = v.id;
        document.getElementById('vehicle-name').value = v.name;
        document.getElementById('vehicle-eff').value  = v.default_efficiency;
        document.getElementById('vehicle-modal-title').innerText = 'Edit Kendaraan';
        UI.showModal('vehicle-modal');
    },
    editFuel(f) {
        document.getElementById('fuel-id').value   = f.id;
        document.getElementById('fuel-name').value = f.name;
        document.getElementById('fuel-ef').value   = f.emission_factor;
        document.getElementById('fuel-modal-title').innerText = 'Edit Bahan Bakar';
        UI.showModal('fuel-modal');
    },
    editTransit(t) {
        document.getElementById('transit-id').value   = t.id;
        document.getElementById('transit-name').value = t.name;
        document.getElementById('transit-ef').value   = t.emission_factor;
        document.getElementById('transit-pax').value  = t.avg_passengers;
        document.getElementById('transit-modal-title').innerText = 'Edit Kendaraan Umum';
        UI.showModal('transit-modal');
    },
    toggleFoodFields() {
        const m = document.getElementById('food-method').value;
        document.getElementById('field-ef').classList.toggle('hidden',  m === 'climatiq');
        document.getElementById('field-cid').classList.toggle('hidden', m === 'fixed');
    },

    /* ── BIND EVENTS ──────────────────────────── */
    bindEvents() {
        // Login
        document.getElementById('login-form').addEventListener('submit', async e => {
            e.preventDefault();
            const btn = e.target.querySelector('button[type="submit"]');
            btn.innerText = 'Masuk...';
            try {
                const res = await API.request('/auth/login', 'POST', {
                    email:    document.getElementById('login-email').value,
                    password: document.getElementById('login-password').value,
                });
                localStorage.setItem('sanctum_token', res.data.sanctum_token);
                localStorage.setItem('custom_token',  res.data.custom_token);
                localStorage.setItem('user', JSON.stringify(res.data.user));
                API.sanctumToken = res.data.sanctum_token;
                API.customToken  = res.data.custom_token;
                this.user = res.data.user;
                this.showApp();
                UI.toast('Login berhasil!');
            } catch (err) {
                UI.toast(err.message || 'Email atau password salah', 'error');
            } finally { btn.innerText = 'Sign In'; }
        });

        // Nav
        document.querySelectorAll('.nav-item').forEach(el =>
            el.addEventListener('click', e => UI.switchView(e.currentTarget.dataset.view)));

        // Food form
        document.getElementById('food-form').addEventListener('submit', async e => {
            e.preventDefault();
            const id   = document.getElementById('food-id').value;
            const body = {
                name:               document.getElementById('food-name').value,
                calculation_method: document.getElementById('food-method').value,
                emission_factor:    document.getElementById('food-ef').value || null,
                climatiq_id:        document.getElementById('food-cid').value || null,
            };
            try {
                if (id) await API.request(`/food-packaging/items/${id}`, 'PUT', body);
                else    await API.request('/food-packaging/items', 'POST', body);
                UI.toast('Food item disimpan'); UI.hideModal('food-modal'); this.loadFood();
            } catch (e) {}
        });

        // Vehicle form
        document.getElementById('vehicle-form').addEventListener('submit', async e => {
            e.preventDefault();
            const id   = document.getElementById('vehicle-id').value;
            const body = { name: document.getElementById('vehicle-name').value, default_efficiency: document.getElementById('vehicle-eff').value };
            try {
                if (id) await API.request(`/transport/private/vehicles/${id}`, 'PUT', body);
                else    await API.request('/transport/private/vehicles', 'POST', body);
                UI.toast('Kendaraan disimpan'); UI.hideModal('vehicle-modal'); this.loadVehicles();
            } catch (e) {}
        });

        // Fuel form
        document.getElementById('fuel-form').addEventListener('submit', async e => {
            e.preventDefault();
            const id   = document.getElementById('fuel-id').value;
            const body = { name: document.getElementById('fuel-name').value, emission_factor: document.getElementById('fuel-ef').value };
            try {
                if (id) await API.request(`/transport/private/fuels/${id}`, 'PUT', body);
                else    await API.request('/transport/private/fuels', 'POST', body);
                UI.toast('BBM disimpan'); UI.hideModal('fuel-modal'); this.loadFuels();
            } catch (e) {}
        });

        // Transit form
        document.getElementById('transit-form').addEventListener('submit', async e => {
            e.preventDefault();
            const id   = document.getElementById('transit-id').value;
            const body = { name: document.getElementById('transit-name').value, emission_factor: document.getElementById('transit-ef').value, avg_passengers: document.getElementById('transit-pax').value };
            try {
                if (id) await API.request(`/transport/public/vehicles/${id}`, 'PUT', body);
                else    await API.request('/transport/public/vehicles', 'POST', body);
                UI.toast('Kendaraan umum disimpan'); UI.hideModal('transit-modal'); this.loadTransit();
            } catch (e) {}
        });

        // Profile form
        document.getElementById('profile-form').addEventListener('submit', async e => {
            e.preventDefault();
            try {
                await API.request('/user/profile', 'PUT', { name: document.getElementById('profile-name').value });
                UI.toast('Profil diperbarui');
                this.user.name = document.getElementById('profile-name').value;
                localStorage.setItem('user', JSON.stringify(this.user));
                document.getElementById('user-name').innerText = this.user.name;
            } catch (e) {}
        });

        // Consumption form
        document.getElementById('consumption-form').addEventListener('submit', e => this.submitConsumption(e));
    }
};

window.addEventListener('DOMContentLoaded', () => App.init());
