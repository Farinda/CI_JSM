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

				<!-- DataTables -->
				<div class="card mb-3">
					<div class="card-header">
						<a href="<?php echo site_url('admin/detail/add') ?>"><i class="fas fa-plus"></i> Add New</a>
					</div>
					<div class="card-body">

						<div class="table-responsive">
							<table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
								<thead>
									<tr>
										<!-- <th>Kode Lokasi</th>
										<th>Kode Unit</th>
										<th>Kode Kelompok</th>
										<th>Kode Sub Kelompok</th>
										<th>Kode Sub-sub Kelompok</th>
										<th>Nomor Urut</th> -->
										<th>Nomor Inventaris</th>
										<th>Tipe</th>
										<th>Warna</th>
										<th>Status</th>
										<th>Tanggal</th>
										<th>PIC</th><!-- 
										<th>Action</th> -->
									</tr>
								</thead>
								<tbody>
									<?php foreach ($detail_data as $detail): ?>
									<tr>
										<!-- <td width="150">
											<?php echo $detail->kodelokasi ?>
										</td>
										<td>
											<?php echo $detail->kodeunit?>
										</td>
										<td>
											<?php echo $detail->kodekelompok?>
										</td>
										<td>
											<?php echo $detail->kode_subkelompok ?>
										</td>
										<td>
											<?php echo $detail->kode_subsubkelompok ?>
										</td>
										<td>
											<?php echo $detail->nomorurut?>
										</td> -->
										<td>
											<?php echo $detail->nomorinventaris ?>
										</td>
										<td>
											<?php echo $detail->tipe ?>
										</td>
										<td>
											<?php echo $detail->warna ?>
										</td>
										<td>
											<?php echo $detail->status?>
										</td>
										
										<td>
											<?php echo $detail->tanggal ?>
										</td>
										<td>
											<?php echo $detail->pic ?>
										</td>
										<!-- <td width="250">
											<a href="<?php echo site_url('admin/detail/edit/'.$detail->id) ?>"
											 class="btn btn-small"><i class="fas fa-edit"></i> Edit</a>
											<a onclick="deleteConfirm('<?php echo site_url('admin/detail/delete/'.$detail->id) ?>')"
											 href="#!" class="btn btn-small text-danger"><i class="fas fa-trash"></i> Hapus</a>
										</td> -->
									</tr>
									<?php endforeach; ?>

								</tbody>
							</table>
						</div>
					</div>
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

	<script>
		function deleteConfirm(url) {
			$('#btn-delete').attr('href', url);
			$('#deleteModal').modal();
		}
	</script>

</body>

</html>