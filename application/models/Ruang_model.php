<?php 

	defined('BASEPATH') OR exit('No direct script access');

	/**
	 * 
	 */
	class Ruang_model extends CI_Model
	{
		private $_table = "tb_ruang";

		public $id;
		public $nama;
		
		public function rules()
		{
			return [
				['field' => 'nama',
				'label' => 'nama',
				'rules' => 'required'],

				
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

		public function subsubkelompok()
		{
			$this->db->order_by('nama', 'ASC');
			$tb_subsubkelompok = $this->db->get('tb_subsubkelompok');

			return $tb_subsubkelompok->result_array();
		}


		public function save()
		{
			$post = $this->input->post();
			$this->nama = $post["nama"];
			$this->db->insert($this->_table, $this);
		}

		public function update()
		{
			$post = $this->input->post();
			$this->id= $post["id"];
			$this->nama= $post["nama"];
			$this->db->update($this->_table, $this, array('id' => $post['id']));
		}

		public function delete($id)
	    {
	        $this->db->where("id",$id);
	        return $this->db->delete($this->_table);
	    }
	}

?>