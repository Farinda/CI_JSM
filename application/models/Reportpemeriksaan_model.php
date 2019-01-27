<?php 

	defined('BASEPATH') OR exit('No direct script access');

	/**
	 * 
	 */
	class Reportpemeriksaan_model extends CI_Model
	{
		public function getAll()
		{
			$this->db->select('tb_pemeriksaan.*,(select nama from tb_ruang where tb_ruang.id=tb_pemeriksaan.id_ruang) as nama_ruang,(SELECT concat(tb_lokasibarang.kode,"." , tb_unitkerja.kode,"/" , tb_kelompokbarang.kode,"." , tb_subkelompok.kode,"." , tb_subsubkelompok.kode,"/" , tb_detail.nomorurut) AS nomorinventaris FROM tb_unitkerja INNER JOIN (tb_lokasibarang INNER JOIN (tb_kelompokbarang INNER JOIN (tb_subsubkelompok INNER JOIN (tb_subkelompok INNER JOIN tb_detail ON tb_subkelompok.id = tb_detail.idsub) ON tb_subsubkelompok.id = tb_detail.idsubsub) ON tb_kelompokbarang.kode = tb_detail.kodekelompok) ON tb_lokasibarang.kode = tb_detail.kodelokasi) ON tb_unitkerja.kode = tb_detail.kodeunit where tb_detail.id=tb_pemeriksaan.id_detail) as nomorinventaris');
			return $this->db->get('tb_pemeriksaan')->result();
			
		}
		
		}

		

		/**public function getById($id)
		{
			return $this->db->get_where($this->_table, ["id" => $id])->row();
		}

		public function save()
		{
			$post = $this->input->post();
			$this->id = uniqid();
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
	    
	}*/

?>