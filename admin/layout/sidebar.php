<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo" style="justify-content: center; height: 140px; padding: 15px 0;">
        <a href="<?= base_url('admin/dashboard.php') ?>" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" height="85">
            </span>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/dashboard.php') ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- Master Data -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Master Data</span></li>
        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'guru.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/guru.php') ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-voice"></i>
                <div data-i18n="Guru">Guru Penguji</div>
            </a>
        </li>
        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'mapel.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/mapel.php') ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Mapel">Mata Pelajaran</div>
            </a>
        </li>
        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'siswa.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/siswa.php') ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div data-i18n="Siswa">Data Siswa</div>
            </a>
        </li>

        <!-- Penilaian -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Manajemen</span></li>
        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'ploting.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/ploting.php') ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-git-repo-forked"></i>
                <div data-i18n="Ploting">Ploting Penguji</div>
            </a>
        </li>
        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'jadwal.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/jadwal.php') ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Jadwal">Jadwal Praktik</div>
            </a>
        </li>
        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'laporan.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/laporan.php') ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Laporan">Laporan & Borang</div>
            </a>
        </li>

        <!-- Account -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Sistem</span></li>
        <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'admin_manage.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/admin_manage.php') ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-shield-quarter"></i>
                <div data-i18n="Admin">Manajemen Admin</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?= base_url('logout.php') ?>" class="menu-link text-danger">
                <i class="menu-icon tf-icons bx bx-power-off"></i>
                <div data-i18n="Logout">Logout</div>
            </a>
        </li>
    </ul>
</aside>
