<?php 

	defined('BASEPATH') OR exit('No direct script access');

	/**
	 * 
	 */
	class Detailpemeriksaan_model extends CI_Model
	{
		private $_table = "tb_detailpemeriksaan";

		public $id;
		public $id_pemeriksaan;
		public $id_detail;
		public $jumlah;
		public $foto;
		
		public function rules()
		{
			return [

				['field' => 'id_pemeriksaan',
				'label' => 'id_pemeriksaan',
				'rules' => 'required'],

				['field' => 'id_detail',
				'label' => 'id_detail',
				'rules' => 'required'],

				['field' => 'jumlah',
				'label' => 'jumlah',
				'rules' => 'required'],

				['field' => 'status',
				'label' => 'status',
				'rules' => 'required'],

				['field'=> 'foto',
				'label' => 'foto',
				'rules' => 'file']

			];
		}

		
		public function getAll()
		{
			return $this->db->get($this->_table)->result();
		}

		public function get_by_pemeriksaan($id)
		{
			$this->db->select('tb_detailpemeriksaan.*, (SELECT concat(tb_lokasibarang.kode,"." , tb_unitkerja.kode,"/" , tb_kelompokbarang.kode,"." , tb_subkelompok.kode,"." , tb_subsubkelompok.kode,"/" , tb_detail.nomorurut) AS nomorinventaris FROM tb_unitkerja INNER JOIN (tb_lokasibarang INNER JOIN (tb_kelompokbarang INNER JOIN (tb_subsubkelompok INNER JOIN (tb_subkelompok INNER JOIN tb_detail ON tb_subkelompok.id = tb_detail.idsub) ON tb_subsubkelompok.id = tb_detail.idsubsub) ON tb_kelompokbarang.kode = tb_detail.kodekelompok) ON tb_lokasibarang.kode = tb_detail.kodelokasi) ON tb_unitkerja.kode = tb_detail.kodeunit where tb_detail.id=tb_detailpemeriksaan.id_detail) as nomorinventaris,
				(select tb_subsubkelompok.nama from tb_detail inner join tb_subsubkelompok on tb_detail.idsubsub=tb_subsubkelompok.id where tb_detail.id=tb_detailpemeriksaan.id_detail ) as nama_barang', FALSE);
			$this->db->where('id_pemeriksaan',$id);
			return $this->db->get($this->_table)->result();
		}

		public function getById($id)
		{
			return $this->db->get_where($this->_table, ["id" => $id])->row();
		}

		public function save($id_pemeriksaan)
		{
			$post = $this->input->post();
			$this->id_detail = $post["id_detail"];
			$this->id_pemeriksaan = $id_pemeriksaan;
			$this->jumlah = $post["jumlah"];
			$this->status = $post["status"];
			$this->foto=$this->_uploadImage();
			$this->db->insert($this->_table, $this);
		}

		public function update()
		{
			$post = $this->input->post();
			$this->id= $post["id"];
			$this->id_detail = $post["id_detail"];
			$this->id_pemeriksaan = $post["id_pemeriksaan"];
			$this->jumlah = $post["jumlah"];
			$this->status = $post["status"];
			
			if (!empty($_FILES["foto"]["name"])) {
			    $this->foto = $this->_uploadImage();
			}

			$this->db->update($this->_table, $this, array('id' => $post['id']));
		}

		public function delete($id)
	    {
	        return $this->db->delete($this->_table, array("id" => $id));
	    }

	    private function _uploadImage()
{
		    $config['upload_path']          = './upload/pemeriksaan/';
		    $config['allowed_types']        = 'gif|jpg|png';
		    $config['overwrite']			= true;
		    $config['max_size']             = 1024; // 1MB
		    // $config['max_width']            = 1024;
		    // $config['max_height']           = 768;

		    $this->load->library('upload', $config);

		    if ($this->upload->do_upload('foto')) {
		        return $this->upload->data("file_name");
		    }
		    
		    return "default.jpg";
		}
			}

?>