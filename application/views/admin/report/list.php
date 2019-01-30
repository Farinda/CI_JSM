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
					<!-- <div class="card-header">
						<a href="<?php echo site_url('admin/report/add') ?>"><i class="fas fa-plus"></i> Add New</a>
					</div>
					<div class="card-body"> -->

						<div class="table-responsive">
							<table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
								<thead>
									<tr>
										<th>No. Inv. Barang</th>
										<th>Nama Barang</th>
										<th>Tipe</th>
										<th>Warna</th>
										<th>Status</th>
										<th>PIC</th>
										<th>Tanggal</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($report_data as $report): ?>
									<tr>
									
										<td>
											<?php echo $report->nomorinventaris ?>
										</td>
										<td>
											<?php echo $report->nama ?>
										</td>
										<td>
											<?php echo $report->tipe ?>
										</td>
										<td>
											<?php echo $report->warna ?>
										</td>
										<td>
											<?php echo $report->status ?>
										</td>
										<td>
											<?php echo $report->pic ?>
										</td>
										<td>
											<?php echo $report->tanggal ?>
										</td>
										
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

		$('#dataTable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    } );

		function deleteConfirm(url) {
			$('#btn-delete').attr('href', url);
			$('#deleteModal').modal();
		}
	</script>

</body>

</html>