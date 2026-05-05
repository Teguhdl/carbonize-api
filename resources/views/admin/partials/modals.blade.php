{{-- ═══════════════════════ FOOD MODAL ══════════════════════════ --}}
<div id="food-modal" class="modal-overlay hidden">
    <div class="glass modal-box">
        <div class="modal-header">
            <h3 id="food-modal-title">Food Item</h3>
            <button class="close-btn" onclick="UI.hideModal('food-modal')"><i class="fa-solid fa-times"></i></button>
        </div>
        <form id="food-form">
            <input type="hidden" id="food-id">
            <label>Nama Item</label>
            <input type="text" id="food-name" class="form-control" required>
            <label>Metode Kalkulasi</label>
            <select id="food-method" class="form-control" onchange="App.toggleFoodFields()">
                <option value="fixed">Fixed (Faktor Lokal)</option>
                <option value="climatiq">Climatiq API</option>
            </select>
            <div id="field-ef">
                <label>Emission Factor (kgCO2e/kg)</label>
                <input type="number" step="0.000001" id="food-ef" class="form-control" placeholder="Contoh: 0.85">
            </div>
            <div id="field-cid" class="hidden">
                <label>Climatiq Activity ID</label>
                <input type="text" id="food-cid" class="form-control" placeholder="Contoh: arable_farming-type_apples-...">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="UI.hideModal('food-modal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════ VEHICLE MODAL ═══════════════════════ --}}
<div id="vehicle-modal" class="modal-overlay hidden">
    <div class="glass modal-box">
        <div class="modal-header">
            <h3 id="vehicle-modal-title">Kendaraan Pribadi</h3>
            <button class="close-btn" onclick="UI.hideModal('vehicle-modal')"><i class="fa-solid fa-times"></i></button>
        </div>
        <form id="vehicle-form">
            <input type="hidden" id="vehicle-id">
            <label>Nama Kendaraan</label>
            <input type="text" id="vehicle-name" class="form-control" required placeholder="Contoh: Motorcycle">
            <label>Efisiensi Default (km/liter)</label>
            <input type="number" step="0.1" id="vehicle-eff" class="form-control" required placeholder="Contoh: 40">
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="UI.hideModal('vehicle-modal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════ FUEL MODAL ══════════════════════════ --}}
<div id="fuel-modal" class="modal-overlay hidden">
    <div class="glass modal-box">
        <div class="modal-header">
            <h3 id="fuel-modal-title">Jenis Bahan Bakar</h3>
            <button class="close-btn" onclick="UI.hideModal('fuel-modal')"><i class="fa-solid fa-times"></i></button>
        </div>
        <form id="fuel-form">
            <input type="hidden" id="fuel-id">
            <label>Nama BBM</label>
            <input type="text" id="fuel-name" class="form-control" required placeholder="Contoh: Pertalite">
            <label>Emission Factor (kgCO2e/liter)</label>
            <input type="number" step="0.0001" id="fuel-ef" class="form-control" required placeholder="Contoh: 2.31">
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="UI.hideModal('fuel-modal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════ TRANSIT MODAL ═══════════════════════ --}}
<div id="transit-modal" class="modal-overlay hidden">
    <div class="glass modal-box">
        <div class="modal-header">
            <h3 id="transit-modal-title">Kendaraan Umum</h3>
            <button class="close-btn" onclick="UI.hideModal('transit-modal')"><i class="fa-solid fa-times"></i></button>
        </div>
        <form id="transit-form">
            <input type="hidden" id="transit-id">
            <label>Nama Kendaraan</label>
            <input type="text" id="transit-name" class="form-control" required placeholder="Contoh: City Bus">
            <label>Emission Factor (kgCO2e/km per kendaraan)</label>
            <input type="number" step="0.000001" id="transit-ef" class="form-control" required placeholder="Contoh: 1.085">
            <label>Rata-rata Penumpang</label>
            <input type="number" step="1" id="transit-pax" class="form-control" required placeholder="Contoh: 20">
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="UI.hideModal('transit-modal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════ CONSUMPTION MODAL ═══════════════════ --}}
<div id="consumption-modal" class="modal-overlay hidden">
    <div class="glass modal-box" style="max-width:560px">
        <div class="modal-header">
            <h3><i class="fa-solid fa-leaf" style="color:var(--success)"></i> Input Konsumsi</h3>
            <button class="close-btn" onclick="UI.hideModal('consumption-modal')"><i class="fa-solid fa-times"></i></button>
        </div>

        {{-- Step indicator --}}
        <div class="steps">
            <div class="step active">1 · Pilih Jenis</div>
            <div class="step">2 · Isi Data</div>
        </div>

        <form id="consumption-form">

            {{-- STEP 1: Pilih tipe --}}
            <div id="cons-step1">
                <p style="color:var(--muted);margin-bottom:1rem;font-size:0.9rem">Pilih jenis konsumsi yang ingin dicatat:</p>
                <div style="display:grid;gap:0.75rem">
                    <button type="button" class="btn btn-ghost" style="justify-content:flex-start;padding:1rem;border:1px solid var(--border);border-radius:12px" onclick="App.consSelectMode('food')">
                        <i class="fa-solid fa-apple-whole" style="color:var(--success);font-size:1.2rem"></i>
                        <div style="text-align:left">
                            <div style="font-weight:600">Food & Packaging</div>
                            <div style="font-size:0.8rem;color:var(--muted)">Makanan, minuman, dan packaging</div>
                        </div>
                    </button>
                    <button type="button" class="btn btn-ghost" style="justify-content:flex-start;padding:1rem;border:1px solid var(--border);border-radius:12px" onclick="App.consSelectMode('private')">
                        <i class="fa-solid fa-car" style="color:#60a5fa;font-size:1.2rem"></i>
                        <div style="text-align:left">
                            <div style="font-weight:600">Kendaraan Pribadi</div>
                            <div style="font-size:0.8rem;color:var(--muted)">Motor, mobil, kendaraan BBM pribadi</div>
                        </div>
                    </button>
                    <button type="button" class="btn btn-ghost" style="justify-content:flex-start;padding:1rem;border:1px solid var(--border);border-radius:12px" onclick="App.consSelectMode('public')">
                        <i class="fa-solid fa-bus" style="color:#a78bfa;font-size:1.2rem"></i>
                        <div style="text-align:left">
                            <div style="font-weight:600">Transportasi Umum</div>
                            <div style="font-size:0.8rem;color:var(--muted)">Bus, MRT, angkot, ojek online</div>
                        </div>
                    </button>
                </div>
            </div>

            {{-- STEP 2A: Food --}}
            <div id="cons-step2-food" class="hidden">
                <button type="button" class="btn btn-ghost btn-sm" style="margin-bottom:1rem" onclick="App.consStep(1)">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </button>
                <label>Item Makanan / Packaging</label>
                <select id="cons-food-item" class="form-control" required></select>
                <label>Quantity (kg)</label>
                <input type="number" id="cons-food-qty" step="0.01" min="0.01" class="form-control" required placeholder="Contoh: 0.5">
                <label>Tanggal</label>
                <input type="date" id="cons-date" class="form-control" required value="{{ date('Y-m-d') }}">
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="UI.hideModal('consumption-modal')">Batal</button>
                    <button type="submit" id="cons-submit-btn" class="btn btn-success">
                        <i class="fa-solid fa-calculator"></i> Hitung & Simpan
                    </button>
                </div>
            </div>

            {{-- STEP 2B: Private Vehicle --}}
            <div id="cons-step2-private" class="hidden">
                <button type="button" class="btn btn-ghost btn-sm" style="margin-bottom:1rem" onclick="App.consStep(1)">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </button>
                <label>Tipe Kendaraan</label>
                <select id="cons-vehicle" class="form-control" required></select>
                <label>Jenis Bahan Bakar</label>
                <select id="cons-fuel" class="form-control" required></select>
                <label>Jarak Tempuh (km)</label>
                <input type="number" id="cons-km" step="0.1" min="0.1" class="form-control" required placeholder="Contoh: 15.5">
                <label>Efisiensi Custom (km/L) <span style="color:var(--muted);font-weight:400">— opsional, kosongkan = pakai default</span></label>
                <input type="number" id="cons-custom-eff" step="0.1" class="form-control" placeholder="Contoh: 35">
                <label>Tanggal</label>
                <input type="date" id="cons-date-private" class="form-control" required value="{{ date('Y-m-d') }}">
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="UI.hideModal('consumption-modal')">Batal</button>
                    <button type="submit" id="cons-submit-btn" class="btn btn-success">
                        <i class="fa-solid fa-calculator"></i> Hitung & Simpan
                    </button>
                </div>
            </div>

            {{-- STEP 2C: Public Transit --}}
            <div id="cons-step2-public" class="hidden">
                <button type="button" class="btn btn-ghost btn-sm" style="margin-bottom:1rem" onclick="App.consStep(1)">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </button>
                <label>Kendaraan Umum</label>
                <select id="cons-transit" class="form-control" required></select>
                <label>Jarak Tempuh (km)</label>
                <input type="number" id="cons-transit-km" step="0.1" min="0.1" class="form-control" required placeholder="Contoh: 12">
                <label>Tanggal</label>
                <input type="date" id="cons-date-public" class="form-control" required value="{{ date('Y-m-d') }}">
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="UI.hideModal('consumption-modal')">Batal</button>
                    <button type="submit" id="cons-submit-btn" class="btn btn-success">
                        <i class="fa-solid fa-calculator"></i> Hitung & Simpan
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
