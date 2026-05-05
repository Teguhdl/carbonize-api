{{-- ╔═══════════════════════════════════╗ --}}
{{-- ║  DASHBOARD VIEW                   ║ --}}
{{-- ╚═══════════════════════════════════╝ --}}
<div id="view-dashboard" class="view-section">
    <div class="page-header">
        <h2>Dashboard</h2>
        <button class="btn btn-success" onclick="App.openConsumptionModal()">
            <i class="fa-solid fa-plus"></i> Input Konsumsi
        </button>
    </div>
    <div class="stats-grid">
        <div class="glass stat-card">
            <div class="stat-icon ic-green"><i class="fa-solid fa-apple-whole"></i></div>
            <div><div class="stat-label">Food Items</div><div id="stat-food" class="stat-value">—</div></div>
        </div>
        <div class="glass stat-card">
            <div class="stat-icon ic-blue"><i class="fa-solid fa-car"></i></div>
            <div><div class="stat-label">Kendaraan Pribadi</div><div id="stat-veh" class="stat-value">—</div></div>
        </div>
        <div class="glass stat-card">
            <div class="stat-icon ic-purple"><i class="fa-solid fa-bus"></i></div>
            <div><div class="stat-label">Kendaraan Umum</div><div id="stat-transit" class="stat-value">—</div></div>
        </div>
        <div class="glass stat-card">
            <div class="stat-icon ic-orange"><i class="fa-solid fa-list-check"></i></div>
            <div><div class="stat-label">Total Entri Konsumsi</div><div id="stat-entries" class="stat-value">—</div></div>
        </div>
    </div>
    <div class="glass" style="padding:1.5rem">
        <p style="color:var(--muted);line-height:1.8">
            Selamat datang di <strong style="color:white">Carbonize Admin Panel</strong>.<br>
            Gunakan tombol <strong style="color:var(--success)">Input Konsumsi</strong> untuk mencatat emisi baru,
            atau navigasi ke menu di samping untuk mengelola master data.
        </p>
    </div>
</div>

{{-- ╔═══════════════════════════════════╗ --}}
{{-- ║  FOOD & PACKAGING VIEW            ║ --}}
{{-- ╚═══════════════════════════════════╝ --}}
<div id="view-food" class="view-section hidden">
    <div class="page-header">
        <h2>Food & Packaging Items</h2>
        <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <select id="food-filter" class="form-control" style="margin:0;width:auto" onchange="App.loadFood()">
                <option value="">Semua Metode</option>
                <option value="fixed">Fixed</option>
                <option value="climatiq">Climatiq</option>
            </select>
            <button class="btn btn-primary" onclick="document.getElementById('food-id').value='';document.getElementById('food-modal-title').innerText='Tambah Food Item';App.toggleFoodFields();UI.showModal('food-modal')">
                <i class="fa-solid fa-plus"></i> Tambah
            </button>
        </div>
    </div>
    <div class="glass table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Nama</th><th>Metode</th><th>Emission Factor</th><th>Climatiq ID</th><th>Aksi</th></tr></thead>
            <tbody id="food-tbody"><tr><td colspan="6" style="text-align:center;color:var(--muted);padding:2rem">Memuat data...</td></tr></tbody>
        </table>
    </div>
</div>

{{-- ╔═══════════════════════════════════╗ --}}
{{-- ║  VEHICLES VIEW                    ║ --}}
{{-- ╚═══════════════════════════════════╝ --}}
<div id="view-vehicles" class="view-section hidden">
    <div class="page-header">
        <h2>Kendaraan Pribadi</h2>
        <button class="btn btn-primary" onclick="document.getElementById('vehicle-id').value='';document.getElementById('vehicle-modal-title').innerText='Tambah Kendaraan';UI.showModal('vehicle-modal')">
            <i class="fa-solid fa-plus"></i> Tambah
        </button>
    </div>
    <div class="glass table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Nama Kendaraan</th><th>Efisiensi Default</th><th>Aksi</th></tr></thead>
            <tbody id="vehicles-tbody"></tbody>
        </table>
    </div>
</div>

{{-- ╔═══════════════════════════════════╗ --}}
{{-- ║  FUELS VIEW                       ║ --}}
{{-- ╚═══════════════════════════════════╝ --}}
<div id="view-fuels" class="view-section hidden">
    <div class="page-header">
        <h2>Jenis Bahan Bakar</h2>
        <button class="btn btn-primary" onclick="document.getElementById('fuel-id').value='';document.getElementById('fuel-modal-title').innerText='Tambah BBM';UI.showModal('fuel-modal')">
            <i class="fa-solid fa-plus"></i> Tambah
        </button>
    </div>
    <div class="glass table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Nama BBM</th><th>Emission Factor (kgCO2e/L)</th><th>Aksi</th></tr></thead>
            <tbody id="fuels-tbody"></tbody>
        </table>
    </div>
</div>

{{-- ╔═══════════════════════════════════╗ --}}
{{-- ║  TRANSIT VIEW                     ║ --}}
{{-- ╚═══════════════════════════════════╝ --}}
<div id="view-transit" class="view-section hidden">
    <div class="page-header">
        <h2>Transportasi Umum</h2>
        <button class="btn btn-primary" onclick="document.getElementById('transit-id').value='';document.getElementById('transit-modal-title').innerText='Tambah Kendaraan Umum';UI.showModal('transit-modal')">
            <i class="fa-solid fa-plus"></i> Tambah
        </button>
    </div>
    <div class="glass table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Nama</th><th>Emission Factor</th><th>Avg Penumpang</th><th>Aksi</th></tr></thead>
            <tbody id="transit-tbody"></tbody>
        </table>
    </div>
</div>

{{-- ╔═══════════════════════════════════╗ --}}
{{-- ║  HISTORY VIEW                     ║ --}}
{{-- ╚═══════════════════════════════════╝ --}}
<div id="view-history" class="view-section hidden">
    <div class="page-header">
        <h2>Riwayat Konsumsi</h2>
        <div style="display:flex;gap:0.5rem;align-items:center">
            <select id="history-filter" class="form-control" style="margin:0;width:auto" onchange="App.loadHistory()">
                <option value="">Semua Tipe</option>
                <option value="food">Food & Packaging</option>
                <option value="private_vehicle">Private Vehicle</option>
                <option value="public_transit">Public Transit</option>
            </select>
            <button class="btn btn-success" onclick="App.openConsumptionModal()">
                <i class="fa-solid fa-plus"></i> Input Baru
            </button>
        </div>
    </div>
    <div class="glass table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Tipe</th><th>Detail</th><th>Quantity</th><th>Emisi (kgCO2e)</th><th>Aksi</th></tr></thead>
            <tbody id="history-tbody"></tbody>
        </table>
    </div>
</div>

{{-- ╔═══════════════════════════════════╗ --}}
{{-- ║  PROFILE VIEW                     ║ --}}
{{-- ╚═══════════════════════════════════╝ --}}
<div id="view-profile" class="view-section hidden">
    <div class="page-header"><h2>Profil Saya</h2></div>
    <div class="glass" style="padding:2rem;max-width:520px">
        <form id="profile-form">
            <label>Nama</label>
            <input type="text" id="profile-name" class="form-control" required>
            <label>Email</label>
            <input type="email" id="profile-email" class="form-control" disabled>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>
