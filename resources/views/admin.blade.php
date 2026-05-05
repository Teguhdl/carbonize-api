<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbonize Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #0f172a;
            --surface-color: rgba(30, 41, 59, 0.7);
            --surface-border: rgba(255, 255, 255, 0.1);
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --success: #10b981;
            --danger: #ef4444;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 85% 30%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
            overflow-x: hidden;
        }

        /* Utility */
        .hidden { display: none !important; }
        .glass-panel {
            background: var(--surface-color);
            backdrop-filter: blur(12px);
            border: 1px solid var(--surface-border);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Button styles */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary { background: var(--primary); }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-danger { background: var(--danger); }
        
        /* Input styles */
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--surface-border);
            border-radius: 8px;
            color: white;
            margin-bottom: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-control:focus { border-color: var(--primary); }
        label { display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.875rem; }

        /* Login Layout */
        #login-view {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            text-align: center;
        }
        .login-box h1 { margin-bottom: 1.5rem; font-size: 1.8rem; background: linear-gradient(to right, #60a5fa, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        /* App Layout */
        #app-view { display: flex; min-height: 100vh; }
        
        .sidebar {
            width: 260px;
            border-right: 1px solid var(--surface-border);
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 100;
        }
        
        .brand {
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            border-bottom: 1px solid var(--surface-border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .brand i { color: var(--success); }

        .nav-menu { padding: 1rem 0; flex: 1; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        .nav-item:hover, .nav-item.active {
            color: white;
            background: rgba(255,255,255,0.05);
            border-left: 3px solid var(--primary);
        }

        .user-info {
            padding: 1.5rem;
            border-top: 1px solid var(--surface-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
            max-width: 1200px;
        }

        /* Page Headers */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        /* Tables */
        .table-container { width: 100%; overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 1rem;
            border-bottom: 1px solid var(--surface-border);
        }
        th { color: var(--text-muted); font-weight: 500; font-size: 0.875rem; }
        tr:hover td { background: rgba(255,255,255,0.02); }

        /* Grid */
        .grid { display: grid; gap: 1.5rem; }
        .grid-3 { grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); }

        /* Stat Cards */
        .stat-card { padding: 1.5rem; display: flex; align-items: center; gap: 1rem; }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
        .bg-blue { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .bg-green { background: rgba(16, 185, 129, 0.2); color: #34d399; }
        
        /* Notification/Toasts */
        #toast-container { position: fixed; top: 1rem; right: 1rem; z-index: 1000; }
        .toast {
            padding: 1rem; margin-bottom: 0.5rem;
            border-radius: 8px; background: var(--surface-color); backdrop-filter: blur(8px);
            border: 1px solid var(--surface-border); color: white; display: flex; align-items: center; gap: 0.5rem;
            animation: slideIn 0.3s ease forwards;
        }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        /* Modals */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center; z-index: 200;
        }
        .modal-content {
            width: 100%; max-width: 500px;
            padding: 1.5rem; max-height: 90vh; overflow-y: auto;
        }
        .modal-header { display: flex; justify-content: space-between; margin-bottom: 1rem; }
        .close-btn { background: none; border: none; color: white; cursor: pointer; font-size: 1.2rem; }
    </style>
</head>
<body>

    <div id="toast-container"></div>

    <!-- LOGIN VIEW -->
    <div id="login-view">
        <div class="glass-panel login-box">
            <h1><i class="fa-solid fa-leaf"></i> Carbonize Admin</h1>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Sign in to access the API control panel.</p>
            <form id="login-form">
                <div>
                    <label>Email Address</label>
                    <input type="email" id="login-email" class="form-control" placeholder="admin@example.com" required>
                </div>
                <div>
                    <label>Password</label>
                    <input type="password" id="login-password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1rem;">
                    Sign In
                </button>
            </form>
        </div>
    </div>

    <!-- APP VIEW -->
    <div id="app-view" class="hidden">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <i class="fa-solid fa-leaf"></i> Carbonize
            </div>
            <nav class="nav-menu">
                <a class="nav-item active" data-view="dashboard"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                <a class="nav-item" data-view="food"><i class="fa-solid fa-apple-whole"></i> Food & Packaging</a>
                <a class="nav-item" data-view="vehicles"><i class="fa-solid fa-car"></i> Kendaraan Pribadi</a>
                <a class="nav-item" data-view="fuels"><i class="fa-solid fa-gas-pump"></i> Bahan Bakar</a>
                <a class="nav-item" data-view="transit"><i class="fa-solid fa-bus"></i> Transportasi Umum</a>
                <a class="nav-item" data-view="history"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Konsumsi</a>
                <a class="nav-item" data-view="profile"><i class="fa-solid fa-user"></i> Profile</a>
            </nav>
            <div class="user-info">
                <div style="font-size: 0.875rem;">
                    <div id="user-name" style="font-weight: 500;">Admin User</div>
                    <div id="user-email" style="color: var(--text-muted); font-size: 0.75rem;">admin@test.com</div>
                </div>
                <button class="btn" style="padding: 0.5rem; background: rgba(255,255,255,0.1);" onclick="App.logout()">
                    <i class="fa-solid fa-sign-out-alt"></i>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            
            <!-- Dashboard View -->
            <div id="view-dashboard" class="view-section">
                <div class="page-header">
                    <h2>Overview</h2>
                </div>
                <div class="grid grid-3">
                    <div class="glass-panel stat-card">
                        <div class="stat-icon bg-green"><i class="fa-solid fa-apple-whole"></i></div>
                        <div>
                            <div style="color: var(--text-muted); font-size: 0.875rem;">Food Items</div>
                            <div id="stat-food" style="font-size: 1.5rem; font-weight: 600;">0</div>
                        </div>
                    </div>
                    <div class="glass-panel stat-card">
                        <div class="stat-icon bg-blue"><i class="fa-solid fa-car"></i></div>
                        <div>
                            <div style="color: var(--text-muted); font-size: 0.875rem;">Kendaraan Pribadi</div>
                            <div id="stat-veh" style="font-size: 1.5rem; font-weight: 600;">0</div>
                        </div>
                    </div>
                    <div class="glass-panel stat-card">
                        <div class="stat-icon bg-blue"><i class="fa-solid fa-bus"></i></div>
                        <div>
                            <div style="color: var(--text-muted); font-size: 0.875rem;">Kendaraan Umum</div>
                            <div id="stat-transit" style="font-size: 1.5rem; font-weight: 600;">0</div>
                        </div>
                    </div>
                </div>
                
                <h3 style="margin-top: 2rem; margin-bottom: 1rem;">System Information</h3>
                <div class="glass-panel" style="padding: 1.5rem;">
                    <p style="color: var(--text-muted); line-height: 1.6;">
                        Welcome to Carbonize API Dashboard. Use the sidebar to manage emission factors, categories, and view user consumption records. The dashboard communicates directly with your Laravel API endpoints via Sanctum and Custom Tokens.
                    </p>
                </div>
            </div>

            <!-- Food & Packaging View -->
            <div id="view-food" class="view-section hidden">
                <div class="page-header">
                    <h2>Food & Packaging Items</h2>
                    <div style="display:flex;gap:0.5rem">
                        <select id="food-filter" class="form-control" style="margin:0;width:auto" onchange="App.loadFood()">
                            <option value="">Semua</option>
                            <option value="fixed">Fixed</option>
                            <option value="climatiq">Climatiq</option>
                        </select>
                        <button class="btn btn-primary" onclick="UI.showModal('food-modal',{action:'create'})"><i class="fa-solid fa-plus"></i> Tambah</button>
                    </div>
                </div>
                <div class="glass-panel table-container">
                    <table><thead><tr><th>ID</th><th>Name</th><th>Method</th><th>Emission Factor</th><th>Climatiq ID</th><th>Aksi</th></tr></thead>
                    <tbody id="food-tbody"></tbody></table>
                </div>
            </div>

            <!-- Vehicles View -->
            <div id="view-vehicles" class="view-section hidden">
                <div class="page-header">
                    <h2>Kendaraan Pribadi</h2>
                    <button class="btn btn-primary" onclick="UI.showModal('vehicle-modal',{action:'create'})"><i class="fa-solid fa-plus"></i> Tambah</button>
                </div>
                <div class="glass-panel table-container">
                    <table><thead><tr><th>ID</th><th>Nama Kendaraan</th><th>Efisiensi Default (km/L)</th><th>Aksi</th></tr></thead>
                    <tbody id="vehicles-tbody"></tbody></table>
                </div>
            </div>

            <!-- Fuels View -->
            <div id="view-fuels" class="view-section hidden">
                <div class="page-header">
                    <h2>Jenis Bahan Bakar</h2>
                    <button class="btn btn-primary" onclick="UI.showModal('fuel-modal',{action:'create'})"><i class="fa-solid fa-plus"></i> Tambah</button>
                </div>
                <div class="glass-panel table-container">
                    <table><thead><tr><th>ID</th><th>Nama BBM</th><th>Emission Factor (kgCO2e/L)</th><th>Aksi</th></tr></thead>
                    <tbody id="fuels-tbody"></tbody></table>
                </div>
            </div>

            <!-- Transit View -->
            <div id="view-transit" class="view-section hidden">
                <div class="page-header">
                    <h2>Transportasi Umum</h2>
                    <button class="btn btn-primary" onclick="UI.showModal('transit-modal',{action:'create'})"><i class="fa-solid fa-plus"></i> Tambah</button>
                </div>
                <div class="glass-panel table-container">
                    <table><thead><tr><th>ID</th><th>Nama</th><th>Emission Factor</th><th>Avg Penumpang</th><th>Aksi</th></tr></thead>
                    <tbody id="transit-tbody"></tbody></table>
                </div>
            </div>

            <!-- History View -->
            <div id="view-history" class="view-section hidden">
                <div class="page-header">
                    <h2>Riwayat Konsumsi</h2>
                    <select id="history-filter" class="form-control" style="margin:0;width:auto" onchange="App.loadHistory()">
                        <option value="">Semua Tipe</option>
                        <option value="food">Food</option>
                        <option value="private_vehicle">Private Vehicle</option>
                        <option value="public_transit">Public Transit</option>
                    </select>
                </div>
                <div class="glass-panel table-container">
                    <table><thead><tr><th>Tanggal</th><th>Tipe</th><th>Detail</th><th>Qty</th><th>Emisi (kgCO2e)</th><th>Aksi</th></tr></thead>
                    <tbody id="history-tbody"></tbody></table>
                </div>
            </div>

            <!-- Profile View -->
            <div id="view-profile" class="view-section hidden">
                <div class="page-header"><h2>My Profile</h2></div>
                <div class="glass-panel" style="padding: 2rem; max-width: 600px;">
                    <form id="profile-form">
                        <label>Name</label>
                        <input type="text" id="profile-name" class="form-control" required>
                        <label>Email</label>
                        <input type="email" id="profile-email" class="form-control" disabled>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <!-- MODALS -->
    <!-- Food Modal -->
    <div id="food-modal" class="modal-overlay hidden">
        <div class="glass-panel modal-content">
            <div class="modal-header">
                <h3 id="food-modal-title">Food Item</h3>
                <button class="close-btn" onclick="UI.hideModal('food-modal')"><i class="fa-solid fa-times"></i></button>
            </div>
            <form id="food-form">
                <input type="hidden" id="food-id">
                <label>Nama Item</label><input type="text" id="food-name" class="form-control" required>
                <label>Metode Kalkulasi</label>
                <select id="food-method" class="form-control" onchange="App.toggleFoodFields()">
                    <option value="fixed">Fixed (lokal)</option>
                    <option value="climatiq">Climatiq API</option>
                </select>
                <div id="field-emission-factor"><label>Emission Factor (kgCO2e/kg)</label><input type="number" step="0.000001" id="food-ef" class="form-control"></div>
                <div id="field-climatiq-id" class="hidden"><label>Climatiq ID</label><input type="text" id="food-cid" class="form-control"></div>
                <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:1rem">
                    <button type="button" class="btn" style="background:rgba(255,255,255,0.1)" onclick="UI.hideModal('food-modal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Vehicle Modal -->
    <div id="vehicle-modal" class="modal-overlay hidden">
        <div class="glass-panel modal-content">
            <div class="modal-header">
                <h3 id="vehicle-modal-title">Kendaraan Pribadi</h3>
                <button class="close-btn" onclick="UI.hideModal('vehicle-modal')"><i class="fa-solid fa-times"></i></button>
            </div>
            <form id="vehicle-form">
                <input type="hidden" id="vehicle-id">
                <label>Nama Kendaraan</label><input type="text" id="vehicle-name" class="form-control" required>
                <label>Efisiensi Default (km/liter)</label><input type="number" step="0.1" id="vehicle-eff" class="form-control" required>
                <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:1rem">
                    <button type="button" class="btn" style="background:rgba(255,255,255,0.1)" onclick="UI.hideModal('vehicle-modal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Fuel Modal -->
    <div id="fuel-modal" class="modal-overlay hidden">
        <div class="glass-panel modal-content">
            <div class="modal-header">
                <h3 id="fuel-modal-title">Bahan Bakar</h3>
                <button class="close-btn" onclick="UI.hideModal('fuel-modal')"><i class="fa-solid fa-times"></i></button>
            </div>
            <form id="fuel-form">
                <input type="hidden" id="fuel-id">
                <label>Nama BBM</label><input type="text" id="fuel-name" class="form-control" required>
                <label>Emission Factor (kgCO2e/liter)</label><input type="number" step="0.0001" id="fuel-ef" class="form-control" required>
                <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:1rem">
                    <button type="button" class="btn" style="background:rgba(255,255,255,0.1)" onclick="UI.hideModal('fuel-modal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Transit Modal -->
    <div id="transit-modal" class="modal-overlay hidden">
        <div class="glass-panel modal-content">
            <div class="modal-header">
                <h3 id="transit-modal-title">Kendaraan Umum</h3>
                <button class="close-btn" onclick="UI.hideModal('transit-modal')"><i class="fa-solid fa-times"></i></button>
            </div>
            <form id="transit-form">
                <input type="hidden" id="transit-id">
                <label>Nama Kendaraan</label><input type="text" id="transit-name" class="form-control" required>
                <label>Emission Factor (kgCO2e/km per kendaraan)</label><input type="number" step="0.000001" id="transit-ef" class="form-control" required>
                <label>Rata-rata Penumpang</label><input type="number" step="1" id="transit-pax" class="form-control" required>
                <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:1rem">
                    <button type="button" class="btn" style="background:rgba(255,255,255,0.1)" onclick="UI.hideModal('transit-modal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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
                    if (res.status === 401) App.logout();
                    throw new Error(data.message || 'API Error');
                }
                return data;
            }
        };

        const UI = {
            toast(msg, type = 'success') {
                const c = document.getElementById('toast-container');
                const d = document.createElement('div'); d.className = 'toast';
                const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                d.innerHTML = `<i class="fa-solid ${icon}"></i> ${msg}`;
                c.appendChild(d);
                setTimeout(() => { d.style.opacity = '0'; setTimeout(() => d.remove(), 300); }, 3000);
            },
            switchView(name) {
                document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
                const view = document.getElementById(`view-${name}`);
                if (view) view.classList.remove('hidden');
                const nav = document.querySelector(`[data-view="${name}"]`);
                if (nav) nav.classList.add('active');
                const loaders = { food: 'loadFood', vehicles: 'loadVehicles', fuels: 'loadFuels', transit: 'loadTransit', history: 'loadHistory' };
                if (loaders[name]) App[loaders[name]]();
            },
            showModal(id) {
                const m = document.getElementById(id);
                if (m) m.classList.remove('hidden');
            },
            hideModal(id) {
                const m = document.getElementById(id);
                if (m) { m.classList.add('hidden'); const f = m.querySelector('form'); if (f) f.reset(); }
            }
        };

        const App = {
            user: null,
            init() {
                const token = localStorage.getItem('sanctum_token');
                const ctoken = localStorage.getItem('custom_token');
                const user = localStorage.getItem('user');
                if (token && ctoken && user) {
                    this.user = JSON.parse(user);
                    API.sanctumToken = token;
                    API.customToken = ctoken;
                    this.showApp();
                }
                this.bindEvents();
            },
            showApp() {
                document.getElementById('login-view').classList.add('hidden');
                document.getElementById('app-view').classList.remove('hidden');
                document.getElementById('user-name').innerText = this.user.name;
                document.getElementById('user-email').innerText = this.user.email;
                if (document.getElementById('profile-name')) document.getElementById('profile-name').value = this.user.name;
                if (document.getElementById('profile-email')) document.getElementById('profile-email').value = this.user.email;
                this.loadDashboard();
            },
            async logout() {
                try { await API.request('/auth/logout', 'POST'); } catch (e) {}
                localStorage.clear(); window.location.reload();
            },
            toggleFoodFields() {
                const m = document.getElementById('food-method').value;
                document.getElementById('field-emission-factor').classList.toggle('hidden', m === 'climatiq');
                document.getElementById('field-climatiq-id').classList.toggle('hidden', m === 'fixed');
            },
            async loadDashboard() {
                try {
                    const [f, v, t] = await Promise.all([
                        API.request('/food-packaging/items'),
                        API.request('/transport/private/vehicles'),
                        API.request('/transport/public/vehicles'),
                    ]);
                    document.getElementById('stat-food').innerText = (f.data || []).length;
                    document.getElementById('stat-veh').innerText = (v.data || []).length;
                    document.getElementById('stat-transit').innerText = (t.data || []).length;
                } catch (e) {}
            },
            async loadFood() {
                const method = document.getElementById('food-filter').value;
                const url = method ? `/food-packaging/items?method=${method}` : '/food-packaging/items';
                try {
                    const res = await API.request(url);
                    document.getElementById('food-tbody').innerHTML = (res.data || []).map(i => `
                        <tr>
                            <td>${i.id}</td><td>${i.name}</td>
                            <td><span style="padding:2px 8px;border-radius:4px;font-size:0.75rem;background:${i.calculation_method==='climatiq'?'rgba(59,130,246,0.2)':'rgba(16,185,129,0.2)'}">${i.calculation_method}</span></td>
                            <td>${i.emission_factor ?? '-'}</td>
                            <td style="font-size:0.75rem;color:var(--text-muted)">${i.climatiq_id ?? '-'}</td>
                            <td style="display:flex;gap:0.25rem">
                                <button class="btn btn-primary" onclick='App.editFood(${JSON.stringify(i)})' style="padding:0.25rem 0.5rem;font-size:0.8rem"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-danger" onclick="App.del('/food-packaging/items/${i.id}','loadFood')" style="padding:0.25rem 0.5rem;font-size:0.8rem"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>`).join('');
                } catch(e) {}
            },
            async loadVehicles() {
                try {
                    const res = await API.request('/transport/private/vehicles');
                    document.getElementById('vehicles-tbody').innerHTML = (res.data || []).map(v => `
                        <tr><td>${v.id}</td><td>${v.name}</td><td>${v.default_efficiency} km/L</td>
                        <td style="display:flex;gap:0.25rem">
                            <button class="btn btn-primary" onclick='App.editVehicle(${JSON.stringify(v)})' style="padding:0.25rem 0.5rem;font-size:0.8rem"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn btn-danger" onclick="App.del('/transport/private/vehicles/${v.id}','loadVehicles')" style="padding:0.25rem 0.5rem;font-size:0.8rem"><i class="fa-solid fa-trash"></i></button>
                        </td></tr>`).join('');
                } catch(e) {}
            },
            async loadFuels() {
                try {
                    const res = await API.request('/transport/private/fuels');
                    document.getElementById('fuels-tbody').innerHTML = (res.data || []).map(f => `
                        <tr><td>${f.id}</td><td>${f.name}</td><td>${f.emission_factor} kgCO2e/L</td>
                        <td style="display:flex;gap:0.25rem">
                            <button class="btn btn-primary" onclick='App.editFuel(${JSON.stringify(f)})' style="padding:0.25rem 0.5rem;font-size:0.8rem"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn btn-danger" onclick="App.del('/transport/private/fuels/${f.id}','loadFuels')" style="padding:0.25rem 0.5rem;font-size:0.8rem"><i class="fa-solid fa-trash"></i></button>
                        </td></tr>`).join('');
                } catch(e) {}
            },
            async loadTransit() {
                try {
                    const res = await API.request('/transport/public/vehicles');
                    document.getElementById('transit-tbody').innerHTML = (res.data || []).map(t => `
                        <tr><td>${t.id}</td><td>${t.name}</td><td>${t.emission_factor}</td><td>${t.avg_passengers}</td>
                        <td style="display:flex;gap:0.25rem">
                            <button class="btn btn-primary" onclick='App.editTransit(${JSON.stringify(t)})' style="padding:0.25rem 0.5rem;font-size:0.8rem"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn btn-danger" onclick="App.del('/transport/public/vehicles/${t.id}','loadTransit')" style="padding:0.25rem 0.5rem;font-size:0.8rem"><i class="fa-solid fa-trash"></i></button>
                        </td></tr>`).join('');
                } catch(e) {}
            },
            async loadHistory() {
                const type = document.getElementById('history-filter').value;
                const url = type ? `/entries?entry_type=${type}` : '/entries';
                try {
                    const res = await API.request(url);
                    const entries = res.data || [];
                    if (!entries.length) {
                        document.getElementById('history-tbody').innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted)">Belum ada riwayat</td></tr>';
                        return;
                    }
                    document.getElementById('history-tbody').innerHTML = entries.map(e => {
                        let detail = '-';
                        if (e.entry_type === 'food') detail = e.food_item?.name ?? '-';
                        else if (e.entry_type === 'private_vehicle') detail = `${e.vehicle_type?.name ?? '-'} + ${e.fuel_type?.name ?? '-'}`;
                        else if (e.entry_type === 'public_transit') detail = e.transit_vehicle?.name ?? '-';
                        const colors = { food: 'rgba(16,185,129,0.2)', private_vehicle: 'rgba(59,130,246,0.2)', public_transit: 'rgba(139,92,246,0.2)' };
                        return `<tr>
                            <td>${e.entry_date}</td>
                            <td><span style="padding:2px 8px;border-radius:4px;font-size:0.75rem;background:${colors[e.entry_type]}">${e.entry_type}</span></td>
                            <td>${detail}</td><td>${e.quantity}</td>
                            <td style="font-weight:bold;color:var(--success)">${parseFloat(e.emissions).toFixed(4)}</td>
                            <td><button class="btn btn-danger" onclick="App.del('/entries/${e.id}','loadHistory')" style="padding:0.25rem 0.5rem;font-size:0.8rem"><i class="fa-solid fa-trash"></i></button></td>
                        </tr>`;
                    }).join('');
                } catch(e) {}
            },
            async del(endpoint, reload) {
                if (!confirm('Hapus data ini?')) return;
                try { await API.request(endpoint, 'DELETE'); UI.toast('Dihapus'); this[reload](); } catch (e) {}
            },
            editFood(item) {
                document.getElementById('food-id').value = item.id;
                document.getElementById('food-name').value = item.name;
                document.getElementById('food-method').value = item.calculation_method;
                document.getElementById('food-ef').value = item.emission_factor ?? '';
                document.getElementById('food-cid').value = item.climatiq_id ?? '';
                document.getElementById('food-modal-title').innerText = 'Edit Food Item';
                this.toggleFoodFields();
                UI.showModal('food-modal');
            },
            editVehicle(v) {
                document.getElementById('vehicle-id').value = v.id;
                document.getElementById('vehicle-name').value = v.name;
                document.getElementById('vehicle-eff').value = v.default_efficiency;
                document.getElementById('vehicle-modal-title').innerText = 'Edit Kendaraan';
                UI.showModal('vehicle-modal');
            },
            editFuel(f) {
                document.getElementById('fuel-id').value = f.id;
                document.getElementById('fuel-name').value = f.name;
                document.getElementById('fuel-ef').value = f.emission_factor;
                document.getElementById('fuel-modal-title').innerText = 'Edit Bahan Bakar';
                UI.showModal('fuel-modal');
            },
            editTransit(t) {
                document.getElementById('transit-id').value = t.id;
                document.getElementById('transit-name').value = t.name;
                document.getElementById('transit-ef').value = t.emission_factor;
                document.getElementById('transit-pax').value = t.avg_passengers;
                document.getElementById('transit-modal-title').innerText = 'Edit Kendaraan Umum';
                UI.showModal('transit-modal');
            },
            bindEvents() {
                document.getElementById('login-form').addEventListener('submit', async e => {
                    e.preventDefault();
                    const btn = e.target.querySelector('button[type="submit"]');
                    btn.innerText = 'Signing in...';
                    try {
                        const data = await API.request('/auth/login', 'POST', {
                            email: document.getElementById('login-email').value,
                            password: document.getElementById('login-password').value
                        });
                        localStorage.setItem('sanctum_token', data.data.sanctum_token);
                        localStorage.setItem('custom_token', data.data.custom_token);
                        localStorage.setItem('user', JSON.stringify(data.data.user));
                        API.sanctumToken = data.data.sanctum_token;
                        API.customToken = data.data.custom_token;
                        this.user = data.data.user;
                        this.showApp();
                        UI.toast('Login berhasil!');
                    } catch (err) {
                        UI.toast(err.message || 'Login gagal. Cek email/password.', 'error');
                    } finally {
                        btn.innerText = 'Sign In';
                    }
                });

                document.querySelectorAll('.nav-item').forEach(el =>
                    el.addEventListener('click', e => UI.switchView(e.currentTarget.dataset.view)));

                document.getElementById('food-form').addEventListener('submit', async e => {
                    e.preventDefault();
                    const id = document.getElementById('food-id').value;
                    const body = { name: document.getElementById('food-name').value, calculation_method: document.getElementById('food-method').value, emission_factor: document.getElementById('food-ef').value || null, climatiq_id: document.getElementById('food-cid').value || null };
                    try {
                        if (id) await API.request(`/food-packaging/items/${id}`, 'PUT', body);
                        else await API.request('/food-packaging/items', 'POST', body);
                        UI.toast('Disimpan'); UI.hideModal('food-modal'); this.loadFood();
                    } catch (e) {}
                });

                document.getElementById('vehicle-form').addEventListener('submit', async e => {
                    e.preventDefault();
                    const id = document.getElementById('vehicle-id').value;
                    const body = { name: document.getElementById('vehicle-name').value, default_efficiency: document.getElementById('vehicle-eff').value };
                    try {
                        if (id) await API.request(`/transport/private/vehicles/${id}`, 'PUT', body);
                        else await API.request('/transport/private/vehicles', 'POST', body);
                        UI.toast('Disimpan'); UI.hideModal('vehicle-modal'); this.loadVehicles();
                    } catch (e) {}
                });

                document.getElementById('fuel-form').addEventListener('submit', async e => {
                    e.preventDefault();
                    const id = document.getElementById('fuel-id').value;
                    const body = { name: document.getElementById('fuel-name').value, emission_factor: document.getElementById('fuel-ef').value };
                    try {
                        if (id) await API.request(`/transport/private/fuels/${id}`, 'PUT', body);
                        else await API.request('/transport/private/fuels', 'POST', body);
                        UI.toast('Disimpan'); UI.hideModal('fuel-modal'); this.loadFuels();
                    } catch (e) {}
                });

                document.getElementById('transit-form').addEventListener('submit', async e => {
                    e.preventDefault();
                    const id = document.getElementById('transit-id').value;
                    const body = { name: document.getElementById('transit-name').value, emission_factor: document.getElementById('transit-ef').value, avg_passengers: document.getElementById('transit-pax').value };
                    try {
                        if (id) await API.request(`/transport/public/vehicles/${id}`, 'PUT', body);
                        else await API.request('/transport/public/vehicles', 'POST', body);
                        UI.toast('Disimpan'); UI.hideModal('transit-modal'); this.loadTransit();
                    } catch (e) {}
                });

                document.getElementById('profile-form').addEventListener('submit', async e => {
                    e.preventDefault();
                    const name = document.getElementById('profile-name').value;
                    try {
                        await API.request('/user/profile', 'PUT', { name });
                        UI.toast('Profil diperbarui');
                        this.user.name = name;
                        localStorage.setItem('user', JSON.stringify(this.user));
                        document.getElementById('user-name').innerText = name;
                    } catch (e) {}
                });
            }
        };

        window.addEventListener('DOMContentLoaded', () => App.init());
    </script>
</body>
</html>
