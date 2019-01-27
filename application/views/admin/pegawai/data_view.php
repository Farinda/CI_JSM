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
						<a href="<?php echo site_url('admin/pegawai/create') ?>"><i class="fas fa-plus"></i> Add New</a>
					</div>
					<div class="card-body">
						<h1 class="h1">Data Pegawai</h1>

				<h4>List Data</h4>
				
				<table id="example" class="table table-striped table-bordered" style="width:100%">
					<thead>
						<tr>
							<th>No</th>
							<th>Nama</th>
							<th>Alamat</th>
							<th>No HP</th>
							<th>Username</th>
							<th>Password</th>
							<th>Level</th>
							<th>Foto</th>
							<th>aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php $nomor = 1; ?>
						<?php foreach ($pegawai_data as $value): ?>
							<tr>
								<td><?php echo $nomor; ?></td>
								<td><?php echo $value->nama ?></td>
								<td><?php echo $value->alamat ?></td>
								<td><?php echo $value->nohp ?></td>
								<td><?php echo $value->username ?></td>
								<td>********</td>
								<td><?php echo $value->level ?></td>
								<td><img src="<?php echo base_url('assets/img/'.$value->foto) ?>" alt="" width="100px"></td>
								<td>
									<a href="<?php echo site_url('admin/pegawai/update/'.$value->id) ?>" class="btn btn-sm btn-success">Update</a>
									<a href="<?php echo site_url('admin/pegawai/delete/'.$value->id) ?>" class="btn btn-sm btn-danger">Delete</a>
								</td>
							</tr>
							<?php $nomor++; ?>
						<?php endforeach ?>
					</tbody>
				</table>
				</div>
			</div>
		</div>
		<?php $this->load->view("admin/_partials/footer.php") ?>
	</div>
</div>

	<?php $this->load->view("admin/_partials/scrolltop.php") ?>
	<?php $this->load->view("admin/_partials/modal.php") ?>

	<?php $this->load->view("admin/_partials/js.php") ?>
	<script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#example').DataTable();
		} );
	</script>
	</body>

</html>