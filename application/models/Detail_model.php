<?php 

	defined('BASEPATH') OR exit('No direct script access');

	/**
	 * 
	 */
	class Detail_model extends CI_Model
	{
		private $_table = "tb_detail";

		public $id;
		public $kodelokasi;
		public $kodeunit;
		public $kodekelompok;
		public $idsub;
		public $idsubsub;
		public $nomorurut;
		public $nomorinventaris;
		public $tipe;
		public $warna;
		public $status;
		public $tanggal;
		public $pic;
		
		public function rules()
		{
			return [

				['field' => 'kodelokasi',
				'label' => 'kodelokasi',
				'rules' => 'required'],

				['field' => 'kodeunit',
				'label' => 'kodeunit',
				'rules' => 'required'],

				['field' => 'kodekelompok',
				'label' => 'kodekelompok',
				'rules' => 'required'],

				['field' => 'idsub',
				'label' => 'idsub',
				'rules' => 'required'],

				['field' => 'idsubsub',
				'label' => 'idsubsub',
				'rules' => 'required'],

				['field' => 'nomorurut',
				'label' => 'nomorurut',
				'rules' => 'required'],

				['field' => 'nomorinventaris',
				'label' => 'nomorinventaris',
				'rules' => 'required'],

				['field' => 'tipe',
				'label' => 'tipe',
				'rules' => 'required'],

				['field' => 'warna',
				'label' => 'warna',
				'rules' => 'required'],

				['field' => 'status',
				'label' => 'status',
				'rules' => 'required'],

				['field'=> 'tanggal',
				'label' => 'tanggal',
				'rules' => 'date'],

				['field'=>  'pic',
				'label' => 'pic',
				'rules' => 'required']
			];
		}

		public function getAll()
		{

			$this->db->select('
				tb_detail.*,
				(select kode from tb_subkelompok where id=tb_detail.idsub) as kode_subkelompok,
				(select kode from tb_subsubkelompok where id=tb_detail.idsubsub) as kode_subsubkelompok,
				');
			return $this->db->get($this->_table)->result();
		}

		public function lokasibarang()
		{
			$this->db->order_by('kode', 'ASC');
			$tb_lokasibarang = $this->db->get('tb_lokasibarang');

			return $tb_lokasibarang->result_array();
		}

		public function unitkerja()
		{
			$this->db->order_by('kode', 'ASC');
			$tb_unitkerja = $this->db->get('tb_unitkerja');

			return $tb_unitkerja->result_array();
		}

		public function kelompokbarang()
		{
			$this->db->order_by('kode', 'ASC');
			$tb_kelompokbarang = $this->db->get('tb_kelompokbarang');

			return $tb_kelompokbarang->result_array();
		}

		public function subkelompok()
		{
			$this->db->order_by('kode', 'ASC');
			$tb_subkelompok = $this->db->get('tb_subkelompok');

			return $tb_subkelompok->result_array();
		}

		public function subsubkelompok()
		{
			$this->db->order_by('kode', 'ASC');
			$tb_subsubkelompok = $this->db->get('tb_subsubkelompok');

			return $tb_subsubkelompok->result_array();
		}

		public function nomorinventaris()
		{

			$this->db->select('tb_detail.*, CONCAT(tb_lokasibarang.kode, ".", tb_unitkerja.kode, "/", tb_kelompokbarang.kode, ".", tb_subkelompok.kode, ".", tb_subsubkelompok.kode, "/", tb_detail.nomorurut) as nomorinventaris', FALSE);
			$this->db->from('tb_detail', '');
			$this->db->join('tb_unitkerja', 'tb_unitkerja.kode = tb_detail.kodeunit', 'inner');
			$this->db->join('tb_lokasibarang', 'tb_lokasibarang.kode = tb_detail.kodelokasi', 'inner');
			$this->db->join('tb_kelompokbarang', 'tb_kelompokbarang.kode = tb_detail.kodekelompok', 'inner');
			$this->db->join('tb_subkelompok', 'tb_subkelompok.id = tb_detail.idsub', 'inner');
			$this->db->join('Table', 'tb_subsubkelompok.id = tb_detail.idsubsub', 'inner');
			$this->db->where('deleted', 0);
			// GASIDO NGGAWE IKI SOALE MUMET BROOOOOOOOOOOOOOOOOOOOOOOOOO SELECT tb_detail.kodelokasi, tb_detail.kodeunit, tb_detail.kodekelompok, tb_subkelompok.kode AS tb_subkelompokkode, tb_subsubkelompok.kode AS tb_subsubkelompokkode, tb_detail.nomorurut, tb_lokasibarang.kode+"." & tb_unitkerja.kode+"/" & tb_kelompokbarang.kode+"." & tb_subkelompok.kode+"." & tb_subsubkelompok.kode+"/" & tb_detail.nomorurut AS nomorinventaris, tb_subsubkelompok.nama AS nama, tb_detail.tipe, tb_detail.warna, tb_detail.status, tb_detail.pic, tb_detail.tanggal 

			// FROM tb_unitkerja INNER JOIN (tb_lokasibarang INNER JOIN (tb_kelompokbarang INNER JOIN (tb_subsubkelompok INNER JOIN (tb_subkelompok INNER JOIN tb_detail ON tb_subkelompok.id = tb_detail.idsub) ON tb_subsubkelompok.id = tb_detail.idsubsub) ON tb_kelompokbarang.kode = tb_detail.kodekelompok) ON tb_lokasibarang.kode = tb_detail.kodelokasi) ON tb_unitkerja.kode = tb_detail.kodeunit
		}
		
		public function getById($id)
		{
			return $this->db->get_where($this->_table, ["id" => $id])->row();
		}

		public function save()
		{
			$post = $this->input->post();
			$this->id = uniqid();
			$this->kodelokasi = $post["kodelokasi"];
			$this->kodeunit = $post["kodeunit"];
			$this->kodekelompok = $post["kodekelompok"];
			$this->idsub = $post["idsub"];
			$this->idsubsub = $post["idsubsub"];
			$this->nomorurut = $post["nomorurut"];
			$this->nomorinventaris = $post["nomorinventaris"];
			$this->tipe = $post["tipe"];
			$this->warna = $post["warna"];
			$this->status = $post["status"];
			$this->tanggal=$post["tanggal"];
			$this->pic=$post["pic"];
			unset($post['btn']);
			unset($post['nama']);
			$this->db->insert($this->_table, $post);
		}

		public function update()
		{
			$post = $this->input->post();
			$this->id= $post["id"];
			$this->kodelokasi = $post["kodelokasi"];
			$this->kodeunit = $post["kodeunit"];
			$this->kodekelompok = $post["kodekelompok"];
			$this->idsub = $post["idsub"];
			$this->idsubsub = $post["idsubsub"];
			$this->nomorurut = $post["nomorurut"];
			$this->nomorinventaris = $post["nomorinventaris"];
			$this->tipe = $post["tipe"];
			$this->warna = $post["warna"];
			$this->status = $post["status"];
			$this->tanggal=$post["tanggal"];
			$this->pic=$post["pic"];
			$this->db->update($this->_table, $this, array('id' => $post['id']));
		}

		public function delete($id)
	    {
	        return $this->db->delete($this->_table, array("id" => $id));
	    }
	}

?>