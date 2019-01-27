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
					<div class="card mb-3">
						<div class="card-header">
							<a href="<?php echo site_url('admin/pegawai') ?>"><i class="fas fa-arrow-left"></i> Back</a>
						</div>
					<div class="card-body">
						<h1 class="h1">Data Pegawai</h1>

						<h4>Tambah Data</h4>
						<?php if (isset($error)) {
							echo $error;
						} ?>
						<?php echo validation_errors(); ?>
						<?php echo form_open_multipart(''); ?>
						<div class="form-group">
							<label for="">Nama</label>
							<input type="text" name="nama" class="form-control" style="width:250px;">
						</div>
						<div class="form-group">
							<label for="">Alamat</label>
							<input type="text" name="alamat" class="form-control" style="width:250px;">
						</div>
						<div class="form-group">
							<label for="">No HP</label>
							<input type="text" name="nohp" class="form-control" style="width:250px;">
						</div>
						<div class="form-group">
							<label for="">Username</label>
							<input type="text" name="username" class="form-control" style="width:250px;">
						</div>
						<div class="form-group">
							<label for="">Password</label>
							<input type="password" name="password" class="form-control" style="width:250px;">
						</div>
						<div class="form-group">
							<label for="">level</label>
							<select name="level" class="form-control">
								<option value="admin">Admin</option>
								<option value="pegawai">Pegawai</option>
							</select>
						</div>
						<div class="form-group">
							<label for="">Foto</label>
							<input type="file" name="foto" class="form-control">
						</div>
						<input type="submit" class="btn btn-primary" value="Tambah">
						<?php echo form_close(); ?>

					</div>
				</div>
			</div>
			<?php $this->load->view('admin/_partials/footer') ?>
		</div>
	</div>

	<?php $this->load->view("admin/_partials/scrolltop.php") ?>
	<?php $this->load->view("admin/_partials/modal.php") ?>

	<?php $this->load->view("admin/_partials/js.php") ?>

	</body>

</html>