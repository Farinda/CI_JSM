<?php 

	defined('BASEPATH') OR exit('No direct script access');

	/**
	 * 
	 */
	class Unitkerja_model extends CI_Model
	{
		private $_table = "tb_unitkerja";

		public $kode;
		public $nama;
		public $tanggal;
		public $pic;
		
		public function rules()
		{
			return [
				['field' => 'kode',
				'label' => 'kode',
				'rules' => 'required'],

				['field' => 'nama',
				'label' => 'nama',
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
			return $this->db->get($this->_table)->result();
		}

		public function getByKode($id)
		{
			return $this->db->get_where($this->_table, ["kode" => $id])->row();
		}

		public function save()
		{
			$post = $this->input->post();
			$this->kode = $post["kode"];
			$this->nama = $post["nama"];
			$this->tanggal=$post["tanggal"];
			$this->pic=$post["pic"];
			$this->db->insert($this->_table, $this);
		}

		public function update()
		{
			$post = $this->input->post();
			$this->kode= $post["kode"];
			$this->nama= $post["nama"];
			$this->tanggal=$post["tanggal"];
			$this->pic=$post["pic"];
			$this->db_update($this->_table, $this, array('kode' => $post['kode']));
		}

		public function delete($kode)
	    {
	        return $this->db->delete($this->_table, array("kode" => $id));
	    }
	}

?>