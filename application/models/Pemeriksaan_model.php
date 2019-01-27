<?php 

	defined('BASEPATH') OR exit('No direct script access');

	/**
	 * 
	 */
	class Pemeriksaan_model extends CI_Model
	{
		private $_table = "tb_pemeriksaan";

		public $id;
		public $id_ruang;
		public $pic;
		public $tanggalcek;
		
		public function rules()
		{
			return [

				['field' => 'id_ruang',
				'label' => 'id_ruang',
				'rules' => 'required'],

				['field'=>  'pic',
				'label' => 'pic',
				'rules' => 'required'],

				['field'=> 'tanggalcek',
				'label' => 'tanggalcek',
				'rules' => 'date']

			];
		}

		
		public function getAll()
		{
			$this->db->select('tb_pemeriksaan.*,(select nama from tb_ruang where tb_ruang.id=tb_pemeriksaan.id_ruang) as nama_ruang');
			return $this->db->get($this->_table)->result();
		}


		public function getById($id)
		{
			return $this->db->get_where($this->_table, ["id" => $id])->row();
		}

		public function save()
		{
			$post = $this->input->post();
			$this->id_ruang = $post["id_ruang"];
			$this->pic=$post["pic"];
			$this->tanggalcek=$post["tanggalcek"];
			$this->db->insert($this->_table, $this);
		}

		public function update()
		{
			$post = $this->input->post();
			$this->id= $post["id"];
			$this->id_ruang = $post["id_ruang"];
			$this->pic=$post["pic"];
			$this->tanggalcek=$post["tanggalcek"];
			
			if (!empty($_FILES["foto"]["nama"])) {
			    $this->foto = $this->_uploadImage();
			} else {
			    $this->foto = $post["old_image"];
			}

			$this->db->update($this->_table, $this, array('id' => $post['id']));
		}

		public function delete($id)
	    {
	        return $this->db->delete($this->_table, array("id" => $id));
	    }

	    
			}

?>