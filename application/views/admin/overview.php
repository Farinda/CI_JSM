<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
</head>
<body id="page-top">

<?php $this->load->view("admin/_partials/navbar.php") ?>

<div id="wrapper">

  <?php $this->load->view("admin/_partials/sidebar.php") ?>

  <div id="content-wrapper">

    <div class="container-fluid">

        <!-- 
        karena ini halaman overview (home), kita matikan partial breadcrumb.
        Jika anda ingin mengampilkan breadcrumb di halaman overview,
        silahkan hilangkan komentar (//) di tag PHP di bawah.
        -->
    <?php //$this->load->view("admin/_partials/breadcrumb.php") ?>

    <!-- Icon Cards-->
    <div class="row">
      <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white bg-primary o-hidden h-100">
        <div class="card-body">
        <div class="card-body-icon">
          <i class="fas fa-fw fa-comments"></i>
        </div>
        <div class="mr-5"><?php echo $this->db->select('count(id) as jml')->get('tb_detail')->row(0)->jml ?> Jumlah Barang</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="<?php echo site_url('Admin_login/detail/all') ?>">
        <span class="float-left">View Details</span>
        <span class="float-right">
          <i class="fas fa-angle-right"></i>
        </span>
        </a>
      </div>
      </div>
      <div class="col-xl-3 col-sm-6 mb-3">

      <div class="card text-white bg-warning o-hidden h-100">
        <div class="card-body">
        <div class="card-body-icon">
          <i class="fas fa-fw fa-list"></i>
        </div>
        <div class="mr-5"><?php echo $this->db->select('count(id) as jml')->where('status',"Rusak")->get('tb_detail')->row(0)->jml ?> Barang Rusak</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="<?php echo site_url('Admin_login/detail/rusak') ?>">
        <span class="float-left">View Details</span>
        <span class="float-right">
          <i class="fas fa-angle-right"></i>
        </span>
        </a>
      </div>
    
      </div>
      <div class="col-xl-3 col-sm-6 mb-3">

      <div class="card text-white bg-success o-hidden h-100">
        <div class="card-body">
        <div class="card-body-icon">
          <i class="fas fa-fw fa-shopping-cart"></i>
        </div>
        <div class="mr-5"><?php echo $this->db->select('count(id) as jml')->where('status',"Baik")->get('tb_detail')->row(0)->jml ?> Barang Bagus</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="<?php echo site_url('Admin_login/detail/baik') ?>">
        <span class="float-left">View Details</span>
        <span class="float-right">
          <i class="fas fa-angle-right"></i>
        </span>
        </a>
      </div>
    
      </div>
      <!-- <div class="col-xl-3 col-sm-6 mb-3">
      <div class="card text-white bg-danger o-hidden h-100">
        <div class="card-body">
        <div class="card-body-icon">
          <i class="fas fa-fw fa-life-ring"></i>
        </div>
        <div class="mr-5">13 New Tickets!</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="#">
        <span class="float-left">View Details</span>
        <span class="float-right">
          <i class="fas fa-angle-right"></i>
        </span>
        </a>
      </div>
      </div> -->
    </div>

    <!-- Area Chart Example-->
    <div class="card mb-3">
      <div class="card-header">
      <i class="fas fa-chart-area"></i>
      Kondisi</div>
      <?php 
      $this->db->select('month(tanggal) as bulan,count(id) as jumlah');
      $this->db->from('tb_detail');
      $this->db->group_by('month(tanggal)');
      $this->db->where('status','Rusak');
      $this->db->where('year(tanggal)','2019');
      $resRusak = $this->db->get()->result();
      $retRusak = "[";
      $idx = 0;
      for ($i=0; $i < 12; $i++) {  
        if(count($resRusak) == 0){
          $retRusak .= '"0",';
        }else if ($resRusak[$idx]->bulan == ($i+1)) {
          $retRusak .= '"'.$resRusak[$idx]->jumlah.'",';
          $idx++;
        }else{
          $retRusak .= '"0",';
        }
      }
      $retRusak = substr($retRusak, 0,-1)."]";


      $this->db->select('month(tanggal) as bulan,count(id) as jumlah');
      $this->db->from('tb_detail');
      $this->db->group_by('month(tanggal)');
      $this->db->where('status','Baik');
      $this->db->where('year(tanggal)','2019');
      $resBaik = $this->db->get()->result();
         $retBaik = "[";
      $idx = 0;
      for ($i=0; $i < 12; $i++) {  
        if(count($resBaik) == 0 || !isset($resBaik[$idx])){
          $retBaik .= '"0",';
        }else if ($resBaik[$idx]->bulan == ($i+1)) {
          $retBaik .= '"'.$resBaik[$idx]->jumlah.'",';
          $idx++;
        }else{
          $retBaik .= '"0",';
        }
      }
      $retBaik = substr($retBaik, 0,-1)."]";

       ?>
      <div class="card-body">
      <canvas id="myBarChart" width="100%" height="30" data-rusak='<?php echo $retRusak ?>' data-baik='<?php echo $retBaik ?>'></canvas>
      </div>
      <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
    </div>

    </div>
    <!-- /.container-fluid -->

    <!-- Sticky Footer -->
    <?php $this->load->view("admin/_partials/footer.php") ?>

  </div>
  <!-- /.content-wrapper -->

</div>
<!-- /#wrapper -->


<?php $this->load->view("admin/_partials/scrolltop.php") ?>
<?php $this->load->view("admin/_partials/modal.php") ?>
<?php $this->load->view("admin/_partials/js.php") ?>
    
</body>
</html>