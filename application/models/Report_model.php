<?php 

	defined('BASEPATH') OR exit('No direct script access');

	/**
	 * 
	 */
	class Report_model extends CI_Model
	{
		public function getAll()
		{
			return $this->db->query('SELECT tb_detail.kodelokasi, tb_detail.kodeunit, tb_detail.kodekelompok, tb_subkelompok.kode AS tb_subkelompokkode, tb_subsubkelompok.kode AS tb_subsubkelompokkode, tb_detail.nomorurut, concat(tb_lokasibarang.kode,"." , tb_unitkerja.kode,"/" , tb_kelompokbarang.kode,"." , tb_subkelompok.kode,"." , tb_subsubkelompok.kode,"/" , tb_detail.nomorurut) AS nomorinventaris, tb_subsubkelompok.nama AS nama, tb_detail.tipe, tb_detail.warna, tb_detail.status, tb_detail.pic, tb_detail.tanggal FROM tb_unitkerja INNER JOIN (tb_lokasibarang INNER JOIN (tb_kelompokbarang INNER JOIN (tb_subsubkelompok INNER JOIN (tb_subkelompok INNER JOIN tb_detail ON tb_subkelompok.id = tb_detail.idsub) ON tb_subsubkelompok.id = tb_detail.idsubsub) ON tb_kelompokbarang.kode = tb_detail.kodekelompok) ON tb_lokasibarang.kode = tb_detail.kodelokasi) ON tb_unitkerja.kode = tb_detail.kodeunit')->result();

			

			
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