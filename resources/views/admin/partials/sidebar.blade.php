<aside class="sidebar">
    <div class="brand">
        <i class="fa-solid fa-leaf"></i> Carbonize
    </div>
    <nav class="nav-menu">
        <a class="nav-item active" data-view="dashboard">
            <i class="fa-solid fa-chart-pie"></i> Dashboard
        </a>
        <a class="nav-item" data-view="food">
            <i class="fa-solid fa-apple-whole"></i> Food & Packaging
        </a>
        <a class="nav-item" data-view="vehicles">
            <i class="fa-solid fa-car"></i> Kendaraan Pribadi
        </a>
        <a class="nav-item" data-view="fuels">
            <i class="fa-solid fa-gas-pump"></i> Bahan Bakar
        </a>
        <a class="nav-item" data-view="transit">
            <i class="fa-solid fa-bus"></i> Transportasi Umum
        </a>
        <a class="nav-item" data-view="history">
            <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Konsumsi
        </a>
        <a class="nav-item" data-view="profile">
            <i class="fa-solid fa-user"></i> Profil Saya
        </a>
    </nav>
    <div class="user-info">
        <div style="font-size:0.875rem">
            <div id="user-name" style="font-weight:500">—</div>
            <div id="user-email" style="color:var(--muted);font-size:0.75rem">—</div>
        </div>
        <button class="btn btn-ghost" style="padding:0.5rem" onclick="App.logout()">
            <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </div>
</aside>
