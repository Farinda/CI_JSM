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

				<?php $this->load->view("admin/_partials/breadcrumb.php") ?>
				<div class="card-header">
							<a href="<?php echo site_url('admin/pegawai') ?>"><i class="fas fa-arrow-left"></i> List</a>
				</div>
				<div class="card-body">
						<h1 class="h1">Data Pegawai</h1>

					
					<?php 
					echo '<h1>Sukses Update Data</h1><br>';
					// echo anchor('Pegawai/', 'List data');
					?>
				</div>
			</div>
		</div>
				<?php $this->load->view("admin/_partials/footer.php") ?>
				<?php $this->load->view("admin/_partials/scrolltop.php") ?>
				<?php $this->load->view("admin/_partials/modal.php") ?>

				<?php $this->load->view("admin/_partials/js.php") ?>

</body>

</html>
