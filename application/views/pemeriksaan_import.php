<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
</head>

<body>
   <?php $this->load->view("admin/_partials/navbar.php") ?>
  <div id="wrapper">

    <?php $this->load->view("admin/_partials/sidebar.php") ?>

    <div id="content-wrapper">

      <div class="container-fluid">

        <?php $this->load->view("admin/_partials/breadcrumb.php") ?>
        <div class="container">
          <br />
          <h3 align="center">Import Data Pemeriksaan</h3>
          <div class="card-header">
            <a href="<?php echo site_url('/ruang_import/') ?>"><i class="fas fa-arrow-left"></i>
            Ruang</a>
          </div>
          <div>
            <form method="post" id="import_form" enctype="multipart/form-data">
              <p><label>Select Excel File</label>
              <input type="file" name="file" id="file" required accept=".xls, .xlsx" /></p>
              <br />
              <input type="submit" name="import" value="Import" class="btn btn-info" />
            </form>
            <br />
            <div class="table-responsive" id="pemeriksaan_data">

            </div>
          </div>
          <?php $this->load->view("admin/_partials/footer.php") ?>
        </div>

  <?php $this->load->view("admin/_partials/scrolltop.php") ?>
  <?php $this->load->view("admin/_partials/modal.php") ?>

  <?php $this->load->view("admin/_partials/js.php") ?>
  <script>
  $(document).ready(function(){

   load_data();

   function load_data()
   {
    $.ajax({
     url:"<?php echo site_url(); ?>/pemeriksaan_import/fetch",
     method:"POST",
     success:function(data){
      $('#pemeriksaan_data').html(data);
     }
    })
   }

   $('#import_form').on('submit', function(event){
    event.preventDefault();
    $.ajax({
     url:"<?php echo site_url(); ?>/pemeriksaan_import/import",
     method:"POST",
     data:new FormData(this),
     contentType:false,
     cache:false,
     processData:false,
     success:function(data){
      alert(data);
      $('#file').val('');
      load_data();
      alert(data);
     }
    })
   });

  });
  </script>
</body>
</html>