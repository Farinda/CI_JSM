<!DOCTYPE html>
<html>
<head>
	<title>JASAMARGA Surabaya Mojokerto</title>

    <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet">
</head>
<body>
	<div class="container-fluid">
		<div class="row">
<div class="col-md-12 mt-3">
	<div class="card">
	<div class="card-body">
		JASAMARGA Surabaya Mojokerto
	</div>
</div>
<table class="table table-striped table-bordered mt-3">
	<thead>
		<tr>
		<th width="50%" colspan="3">Ruang : <?php echo $pemeriksaan->id_ruang ?></th>
		<th width="50%" colspan="3" class="text-right">Tanggal : <?php echo $pemeriksaan->tanggalcek ?></th>
	</tr>
	</thead>
	<tbody>
		<tr>
										<th>Id Pemeriksaan</th>
										<th>Nomor Inventaris</th>
										<th>Nama Barang</th>
										<th>Jumlah</th>
										<th>Status</th>
										<th>Foto</th>
									</tr>
		<?php foreach ($detailpemeriksaan as $key => $dp): ?>
			<tr>
										<td width="150">
											<?php echo $dp->id_pemeriksaan ?>
										</td>
										<td>
											<?php echo $dp->nomorinventaris ?>
										</td>
										<td>
											<?php echo $dp->nama_barang ?>
										</td>
										<td>
											<?php echo $dp->jumlah?>
										</td>
										<td>
											<?php echo $dp->status?>
										</td>
										<td>
											<img src="<?php echo base_url('upload/pemeriksaan/'.$dp->foto) ?>" width="100px">
										</td>
									
									</tr>
		<?php endforeach ?>
	</tbody>
</table>

</div>

	</div>
	</div>
	<script type="text/javascript">
		 setTimeout(function () { window.print(); }, 500);
        window.onfocus = function () { setTimeout(function () { window.close(); }, 500); }

</script>
</body>
</html>