<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai_model extends CI_Model {

	public function get_data()
	{
		$query = $this->db->get('tb_pegawai');
		return $query->result();
	}
	public function get_id($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('tb_pegawai');
		return $query->result()[0];
	}
	public function insert()
	{
		$data = array(
			'nama' => $this->input->post('nama'),
			'alamat' => $this->input->post('alamat'),
			'nohp' => $this->input->post('nohp'),
			'username' => $this->input->post('username'),
			'password' => md5($this->input->post('password')),
			'level' => $this->input->post('level'),
			'foto' => $this->upload->data('file_name')
		);
		$this->db->insert('tb_pegawai',$data);
	}
	public function update($id)
	{
		$set = array(
			'nama' => $this->input->post('nama'),
			'alamat' => $this->input->post('alamat'),
			'nohp' => $this->input->post('nohp'),
			'username' => $this->input->post('username'),
			'password' => md5($this->input->post('password')),
			'level' => $this->input->post('level'),
			'foto' => $this->upload->data('file_name')
		);
		$this->db->where('id',$id);
		$this->db->update('tb_pegawai',$set);
	}
	public function delete($id)
	{
		$this->db->where('id',$id);
		$this->db->delete('tb_pegawai');
	}
}

?>