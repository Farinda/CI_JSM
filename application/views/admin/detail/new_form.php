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
						<a href="<?php echo site_url('admin/detail/') ?>"><i class="fas fa-arrow-left"></i> Back</a>
					</div>
					<div class="card-body">

						<form action="<?php base_url('admin/detail/add') ?>" method="post" enctype="multipart/form-data" >
							
							<?php echo validation_errors() ?>
							<div class="form-group">
								<label for="name">Kode Lokasi*</label>
								<select class="form-control" id="lokasibarang" name="kodelokasi">
							      <option value='0'>--pilih--</option>
							      <?php 
									foreach ($this->db->get('tb_lokasibarang')->result_array() as $lokbar) {
										echo "<option value='".$lokbar['kode']."'>".$lokbar['kode']."-".$lokbar['nama']."</option>";
									}

									echo form_error('kodelokasi');
								  ?>

							    </select>
							</div>

							<div class="form-group">
								<label for="name">Kode Unit*</label>
								<select class="form-control" id="unitkerja" name="kodeunit">
							      <option value='0'>--pilih--</option>
							      <?php 
									foreach ($this->db->get('tb_unitkerja')->result_array() as $uk) {
										echo "<option value='".$uk['kode']."'>".$uk['kode']."-".$uk['nama']."</option>";
									}

									echo form_error('unitkerja');
								  ?>

							    </select>
							</div>

							<div class="form-group">
								<label for="name">Kode Kelompok*</label>
								<select class="form-control" id="kelompokbarang" name="kodekelompok">
							      <option value='0'>--pilih--</option>
							      <?php 
									foreach ($this->db->get('tb_kelompokbarang')->result_array() as $kk) {
										echo "<option value='".$kk['kode']."'>".$kk['kode']."-".$kk['nama']."</option>";
									}

									echo form_error('kodekelompok');
								  ?>

							    </select>
							</div>

							<div class="form-group">
								<label for="name">Kode Sub Kelompok*</label>
								<select class="form-control" id="subkelompok" name="idsub">
							      <option value='0'>--pilih--</option>
							      <?php 
									foreach ($this->db->get('tb_subkelompok')->result_array() as $ksk) {
										echo "<option value='".$ksk['id']."' data-kode='".$ksk['kode']."'>".$ksk['kode']."-".$ksk['nama']."</option>";
									}

									echo form_error('subkelompok');
								  ?>
							    </select>
							</div>

							<div class="form-group">
								<label for="name">Kode Sub-sub Kelompok*</label>
								<select class="form-control" id="subsubkelompok" name="idsubsub">
							      <option value='0'>--pilih--</option>
							      <?php 
									foreach ($this->db->get('tb_subsubkelompok')->result_array() as $kbk) {
										echo "<option value='".$kbk['id']."' data-kode='".$ksk['kode']."'>".$kbk['kode']."-".$kbk['nama']."</option>";
									}

									echo form_error('subsubkelompok');
								  ?>
							    </select>
							</div>
<!-- 
							<div class="form-group">
								<label for="name">Kode Unit*</label>
								<input class="form-control <?php echo form_error('kodeunit') ? 'is-invalid':'' ?>"
								 type="text" name="kodeunit" placeholder="Kode Kelompok Barang" />
								<div class="invalid-feedback">
									<?php echo form_error('kodeunit') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">Kode Kelompok Barang*</label>
								<input class="form-control <?php echo form_error('kodesubkelompok') ? 'is-invalid':'' ?>"
								 type="text" name="kodekelompok" placeholder="Kode Kelompok Barang" />
								<div class="invalid-feedback">
									<?php echo form_error('kodekelompok') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">Id Sub Kelompok Barang*</label>
								<input class="form-control <?php echo form_error('idsub') ? 'is-invalid':'' ?>"
								 type="text" name="idsub" placeholder="Id Sub Kelompok Barang"  />
								<div class="invalid-feedback">
									<?php echo form_error('idsub') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">Id Sub-sub Kelompok Barang*</label>
								<input class="form-control <?php echo form_error('idsubsub') ? 'is-invalid':'' ?>"
								 type="text" name="idsubsub" placeholder="Id Sub-sub Kelompok Barang" />
								<div class="invalid-feedback">
									<?php echo form_error('idsubsub') ?>
								</div>
							</div> -->

							<div class="form-group">
								<label for="name">Nomor Urut Barang*</label>
								<input class="form-control <?php echo form_error('nomorurut') ? 'is-invalid':'' ?>"
								 type="text" name="nomorurut" id="nomorurut" placeholder="Kode Kelompok Barang" />
								<div class="invalid-feedback">
									<?php echo form_error('nomorurut') ?>
								</div>
							</div>
							
							<div class="form-group">
								<label for="name">Nomor Inventaris Barang*</label>
								<input class="form-control <?php echo form_error('nomorinventaris') ? 'is-invalid':'' ?>"
								 type="text" name="nomorinventaris" id="nomorinventaris" placeholder="Nomor Inventaris Barang" />
								<div class="invalid-feedback">
									<?php echo form_error('nomorinventaris') ?>
								</div>
								<script type="text/javascript">
									$('#lokasibarang, #unitkerja, #kelompokbarang, #subkelompok, #subsubkelompok, #nomorurut').change(function(){
										var lokasibarang = $('#lokasibarang :selected').val();
										var unit = $('#unitkerja :selected').val();
										var kelompok = $('#kelompokbarang :selected').val();
										var subkelompok = $('#subkelompok :selected').data('kode');
										var sub2kelompok = $('#subsubkelompok :selected').data('kode');
										var nomorurut = $('#nomorurut').val();
										var noinv = lokasibarang+'.'+unit+'/'+kelompok+'.'+subkelompok+'.'+sub2kelompok+'/'+nomorurut;
										$('#nomorinventaris').val(noinv);
									});
								</script>
							</div>
							
							<div class="form-group">
								<label for="name">Tipe*</label>
								<input class="form-control <?php echo form_error('tipe') ? 'is-invalid':'' ?>"
								 type="text" name="tipe" placeholder="Tipe Barang" />
								<div class="invalid-feedback">
									<?php echo form_error('tipe') ?>
								</div>
							</div>

							<div class="form-group">
								<label for="name">Warna*</label>
								<input class="form-control <?php echo form_error('warna') ? 'is-invalid':'' ?>"
								 type="text" name="warna" placeholder="Warna Barang" />
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
								 type="text" name="pic" placeholder="PIC" />
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