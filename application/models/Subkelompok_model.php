<?php 

	defined('BASEPATH') OR exit('No direct script access');

	/**
	 * 
	 */
	class Subkelompok_model extends CI_Model
	{
		private $_table = "tb_subkelompok";

		public $id;
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

		public function getById($id)
		{
			return $this->db->get_where($this->_table, ["id" => $id])->row();
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
			$this->id= $post["id"];
			$this->kode= $post["kode"];
			$this->nama= $post["nama"];
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