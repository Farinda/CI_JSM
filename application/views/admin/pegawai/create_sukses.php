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
				<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
					<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
						<h1 class="h1">Data Pegawai</h1>

					</div>
					<?php 
					echo '<h1>Sukses Simpan Data</h1><br>';
					echo anchor('admin/Pegawai/create', 'Masukan Data Lagi').'<br>';
					echo anchor('admin/Pegawai/', 'List data');
					?>

				</main>
				<?php $this->load->view("admin/_partials/scrolltop.php") ?>
				<?php $this->load->view("admin/_partials/modal.php") ?>

				<?php $this->load->view("admin/_partials/js.php") ?>

</body>

</html>