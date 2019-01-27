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
								<label for="name">Id Ruang*</label>
								<input class="form-control <?php echo form_error('id_ruang') ? 'is-invalid':'' ?>"
								 type="text" name="id_ruang" placeholder="Id Ruang" value="<?php echo $detail->id_ruang ?>" />
								<div class="invalid-feedback">
									<?php echo form_error('id_ruang') ?>
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

							<div class="form-group">
								<label for="name">Tanggal Cek*</label>
								<input class="form-control <?php echo form_error('tanggalcek') ? 'is-invalid':'' ?>"
								 type="date" name="tanggalcek" value="<?php echo date('Y-m-d') ?>" class="form-control" style="width:250px" placeholder="Tanggal Cek" />
								<div class="invalid-feedback">
									<?php echo form_error('tanggalcek') ?>
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