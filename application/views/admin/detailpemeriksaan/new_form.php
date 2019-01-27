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

				<div class="card mb-3">
					<div class="card-header">
						<a href="<?php echo site_url('admin/detailpemeriksaan/') ?>"><i class="fas fa-arrow-left"></i> Back</a>
					</div>
					<div class="card-body">

						<form action="<?php base_url('admin/detailpemeriksaan/add') ?>" method="post" enctype="multipart/form-data" >
							
							<?php echo validation_errors() ?>
							<div class="form-group">
								<label for="name">Id Pemeriksaan*</label>
								<input class="form-control <?php echo form_error('id_pemeriksaan') ? 'is-invalid':'' ?>"
								 type="text" name="id_pemeriksaan" placeholder="Id Pemeriksaan" value="<?php echo $pemeriksaan->id ?>" readonly/>
								<div class="invalid-feedback">
									<?php echo form_error('id_pemeriksaan') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">Nomor Inventaris*</label>
								<select class="form-control" id="detail" name="id_detail">
							      <option value='0'>--pilih--</option>
							      <?php 
									foreach ($this->db->query('SELECT tb_detail.id as id_detail ,tb_detail.kodelokasi, tb_detail.kodeunit, tb_detail.kodekelompok, tb_subkelompok.kode AS tb_subkelompokkode, tb_subsubkelompok.kode AS tb_subsubkelompokkode, tb_detail.nomorurut, concat(tb_lokasibarang.kode,"." , tb_unitkerja.kode,"/" , tb_kelompokbarang.kode,"." , tb_subkelompok.kode,"." , tb_subsubkelompok.kode,"/" , tb_detail.nomorurut) AS nomorinventaris, tb_subsubkelompok.nama AS nama, tb_detail.tipe, tb_detail.warna, tb_detail.status, tb_detail.pic, tb_detail.tanggal FROM tb_unitkerja INNER JOIN (tb_lokasibarang INNER JOIN (tb_kelompokbarang INNER JOIN (tb_subsubkelompok INNER JOIN (tb_subkelompok INNER JOIN tb_detail ON tb_subkelompok.id = tb_detail.idsub) ON tb_subsubkelompok.id = tb_detail.idsubsub) ON tb_kelompokbarang.kode = tb_detail.kodekelompok) ON tb_lokasibarang.kode = tb_detail.kodelokasi) ON tb_unitkerja.kode = tb_detail.kodeunit')->result_array() as $lokbar) {
										echo "<option value='".$lokbar['id_detail']."'>".$lokbar['nomorinventaris']."-".$lokbar['nama']."</option>";
									}

									echo form_error('detail');
								  ?>

							    </select>
							</div>
							

							<div class="form-group">
								<label for="name">Jumlah*</label>
								<input class="form-control <?php echo form_error('jumlah') ? 'is-invalid':'' ?>"
								 type="text" name="jumlah" placeholder="Jumlah" />
								<div class="invalid-feedback">
									<?php echo form_error('jumlah') ?>
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
								<label for="name">Foto*</label>
								<input class="form-control <?php echo form_error('foto') ? 'is-invalid':'' ?>"
								 type="file" name="foto" placeholder="Foto" />
								<div class="invalid-feedback">
									<?php echo form_error('foto') ?>
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