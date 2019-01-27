<!-- Sidebar -->
<ul class="sidebar navbar-nav">
    <li class="nav-item <?php echo $this->uri->segment(2) == '' ? 'active': '' ?>">
        <a class="nav-link" href="<?php echo site_url('admin') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>DASHBOARD</span>
        </a>
    </li>

    <!-- New Sidebar -->
    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'Activity' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Activity</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/kelompokbarang') ?>">Kelompok Barang</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/subkelompok') ?>">Sub Kelompok Barang</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/subsubkelompok') ?>">Sub-sub Kelompok</a>
        </div>
    </li>

    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'masteringdata' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Mastering Data</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/lokasibarang') ?>">Lokasi Barang</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/unitkerja') ?>">Unit Kerja</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/ruang') ?>">Ruang</a>
        </div>
    </li>

    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'reportdata' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Report</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/report') ?>">Report</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/detail') ?>">Detail</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/detaillog') ?>">Data History</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/pemeriksaan') ?>">Pemeriksaan</a>
        </div>
    </li>

    <!-- End of NewSidebar -->

    <!-- Old Sidebar -->
    
    <!-- <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'lokasibarang' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Lokasi Barang</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/lokasibarang/add') ?>">New Lokasi Barang</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/lokasibarang') ?>">List Lokasi Barang</a>
        </div>
    </li>
    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'unitkerja' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Unit Kerja</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/unitkerja/add') ?>">New Unit Kerja</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/unitkerja') ?>">List Unit Kerja</a>
        </div>
    </li>
    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'kelompokbarang' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Kelompok Barang</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/kelompokbarang/add') ?>">New Kelompok Barang</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/kelompokbarang') ?>">List Kelompok Barang</a>
        </div>
    </li>
    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'subkelompok' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Sub Kelompok</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/subkelompok/add') ?>">New Sub Kelompok</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/subkelompok') ?>">List Sub Kelompok</a>
        </div>
    </li>
    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'subsubkelompok' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Sub-sub Kelompok</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/subsubkelompok/add') ?>">New Sub-sub Kelompok</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/subsubkelompok') ?>">List Sub-sub Kelompok</a>
        </div>
    </li>
    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'ruang' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Ruang</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/ruang/add') ?>">New Ruang</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/ruang') ?>">List Ruang</a>
        </div>
    </li>
    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'detail' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Detail</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/detail/add') ?>">New Detail</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/detail') ?>">List Detail</a>
        </div>
    </li>
    
    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'pemeriksaan' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Pemeriksaan</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/pemeriksaan/add') ?>">New pemeriksaan</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/pemeriksaan') ?>">List Pemeriksaan</a>
        </div>
    </li>

    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'report' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Report</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/report') ?>">Report Detail</a>
        </div>
    </li> -->

    <!-- End of Old Sidebar -->

     <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'pegawai' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Pegawai</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/pegawai/create') ?>">New Pegawai</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/pegawai') ?>">List Pegawai</a>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-cog"></i>
            <span>Settings</span></a>
    </li>
</ul>