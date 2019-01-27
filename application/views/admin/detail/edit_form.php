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

				<?php if ($this->session->flashdata('success')): ?>
				<div class="alert alert-success" role="alert">
					<?php echo $this->session->flashdata('success'); ?>
				</div>
				<?php endif; ?>

				<!-- Card  -->
				<div class="card mb-3">
					<div class="card-header">

						<a href="<?php echo site_url('admin/detail/') ?>"><i class="fas fa-arrow-left"></i>
							Back</a>
					</div>
					<div class="card-body">

						<form action="<?php base_url('admin/detail/edit') ?>" method="post" enctype="multipart/form-data">

							<input type="hidden" name="id" value="<?php echo $detail->id?>" />
							
							<div class="form-group">
								<label for="name">Kode Lokasi*</label>
								<input class="form-control <?php echo form_error('kodelokasi') ? 'is-invalid':'' ?>"
								 type="text" name="kodelokasi" placeholder="Kode Kelompok Barang" value="<?php echo $detail->kodelokasi ?>" />
								<div class="invalid-feedback">
									<?php echo form_error('kodelokasi') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">Kode Unit*</label>
								<input class="form-control <?php echo form_error('kodeunit') ? 'is-invalid':'' ?>"
								 type="text" name="kodeunit" placeholder="Kode Kelompok Barang" value="<?php echo $detail->kodeunit ?>" />
								<div class="invalid-feedback">
									<?php echo form_error('kodeunit') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">Kode Kelompok Barang*</label>
								<input class="form-control <?php echo form_error('kodesubkelompok') ? 'is-invalid':'' ?>"
								 type="text" name="kodekelompok" placeholder="Kode Kelompok Barang" value="<?php echo $detail->kodekelompok ?>" />
								<div class="invalid-feedback">
									<?php echo form_error('kodekelompok') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">Id Sub Kelompok Barang*</label>
								<input class="form-control <?php echo form_error('idsub') ? 'is-invalid':'' ?>"
								 type="text" name="idsub" placeholder="Id Sub Kelompok Barang" value="<?php echo $detail->idsub ?>" />
								<div class="invalid-feedback">
									<?php echo form_error('idsub') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">Id Sub-sub Kelompok Barang*</label>
								<input class="form-control <?php echo form_error('idsubsub') ? 'is-invalid':'' ?>"
								 type="text" name="idsubsub" placeholder="Id Sub-sub Kelompok Barang" value="<?php echo $detail->idsubsub ?>" />
								<div class="invalid-feedback">
									<?php echo form_error('idsubsub') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">Nomor Urut Barang*</label>
								<input class="form-control <?php echo form_error('nomorurut') ? 'is-invalid':'' ?>"
								 type="text" name="nomorurut" placeholder="Kode Kelompok Barang" value="<?php echo $detail->nomorurut ?>" />
								<div class="invalid-feedback">
									<?php echo form_error('nomorurut') ?>
								</div>
							</div>
							
							<div class="form-group">
								<label for="name">Nomor Inventaris Barang*</label>
								<input class="form-control <?php echo form_error('nomorinventaris') ? 'is-invalid':'' ?>"
								 type="text" name="nomorinventaris" placeholder="Nomor Inventaris Barang" value="<?php echo $detail->nomorinventaris?>" />
								<div class="invalid-feedback">
									<?php echo form_error('nomorinventaris') ?>nomorinventaris
								</div>
							</div>
							
							<div class="form-group">
								<label for="name">Tipe*</label>
								<input class="form-control <?php echo form_error('tipe') ? 'is-invalid':'' ?>"
								 type="text" name="tipe" placeholder="Tipe Barang" value="<?php echo $detail->tipe ?>" />
								<div class="invalid-feedback">
									<?php echo form_error('tipe') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">Warna*</label>
								<input class="form-control <?php echo form_error('warna') ? 'is-invalid':'' ?>"
								 type="text" name="warna" placeholder="Warna Barang" value="<?php echo $detail->warna ?>" />
								<div class="invalid-feedback">
									<?php echo form_error('warna') ?>
								</div>
							</div>
							
							<div class="form-group">
								<label for="name">Status*</label>
								<select class="form-control" id="status" name="status">
							      <option>Baik</option>
							      <option>Rusak</option>
							    </select>
							</div>

							

							<div class="form-group">
								<label for="name">Tanggal*</label>
								<input class="form-control <?php echo form_error('tanggal') ? 'is-invalid':'' ?>"
								 type="date" name="tanggal" value="<?php echo date('Y-m-d') ?>" class="form-control" style="width:250px" placeholder="Tanggal Masuk" />
								<div class="invalid-feedback">
									<?php echo form_error('tanggal') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">PIC*</label>
								<input class="form-control <?php echo form_error('pic') ? 'is-invalid':'' ?>"
								 type="text" name="pic" placeholder="PIC" value="<?php echo $detail->pic ?>" />
								<div class="invalid-feedback">
									<?php echo form_error('pic') ?>
								</div>
							</div>

							<input class="btn btn-success" type="submit" name="btn" value="Save" />
						</form>

					</div>

					<div class="card-footer small text-muted">
						* required fields
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

		<?php $this->load->view("admin/_partials/js.php") ?>

</body>

</html>