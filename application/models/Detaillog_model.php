<?php 

	defined('BASEPATH') OR exit('No direct script access');

	/**
	 * 
	 */
	class Detaillog_model extends CI_Model
	{
		private $_table = "tb_detail_log";

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
		public $tanggaledit;
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

				['field'=> 'tanggaledit',
				'label' => 'tanggaledit',
				'rules' => 'date'],

				['field'=>  'pic',
				'label' => 'pic',
				'rules' => 'required']
			];
		}

		public function getAll()
		{
			$this->db->select('
				tb_detail_log.*,
				(select kode from tb_subkelompok where id=tb_detail_log.idsub) as kode_subkelompok,
				(select kode from tb_subsubkelompok where id=tb_detail_log.idsubsub) as kode_subsubkelompok,
				');
			return $this->db->get($this->_table)->result();
		}

		public function getByKode($id)
		{
			return $this->db->get_where($this->_table, ["kode" => $id])->row();
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
			$this->tanggaledit=$post["tanggaledit"];
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
			$this->tanggaledit=$post["tanggaledit"];
			$this->pic=$post["pic"];
			$this->db->update($this->_table, $this, array('kode' => $post['kode']));
		}

		public function delete($id)
	    {
	        return $this->db->delete($this->_table, array("kode" => $id));
	    }
	}

?>