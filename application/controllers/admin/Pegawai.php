<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('logged_in')) {
				$session_data = $this->session->userdata('logged_in');
				$data['username'] = $session_data['username'];
				$data['level'] = $session_data['level'];
				$current_controller = 'admin/'.$this->router->fetch_class();
				$this->load->library('acl');
				if (!$this->acl->is_public($current_controller)) {
					if (!$this->acl->is_allowed($current_controller,$data['level'])) {
						echo '<script>alert("Tidak Dapat Akses")</script>';
						redirect('Login/logout','refresh');
					}
				}
			}else{
				echo '<script>alert("Login Dahulu")</script>';
				redirect('Login');
			}
		$this->load->model("pegawai_model");
			$this->load->library('form_validation');
	}
	public function index()
	{
		$data['pegawai_data'] = $this->pegawai_model->get_data();
		$this->load->view('admin/pegawai/data_view',$data);
	}
	public function create()
	{
		$this->load->model('Pegawai_model');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('nama','Nama','required');
		$this->form_validation->set_rules('alamat','Alamat','required');
		$this->form_validation->set_rules('nohp','No HP','required');
		$this->form_validation->set_rules('username','Nama','required|is_unique[tb_pegawai.username]');
		$this->form_validation->set_rules('password','Password','required');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('admin/pegawai/create_view');
		} else {
			$config['upload_path'] = './assets/img/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']  = '2000';
			$config['max_width']  = '10240';
			$config['max_height']  = '7680';
			
			$this->load->library('upload', $config);
			
			if ( ! $this->upload->do_upload('foto')){
				$error = array('error' => $this->upload->display_errors());
				$this->load->view('admin/pegawai/create_view',$error);
			}
			else{
				$this->Pegawai_model->insert();
				$this->load->view('admin/pegawai/create_sukses');
			}
			
		}
	}
	public function update($id)
	{
		$this->load->model('Pegawai_model');
		$this->load->library('form_validation');
		$data['pegawai'] = $this->Pegawai_model->get_id($id);
		$this->form_validation->set_rules('nama','Nama','required');
		$this->form_validation->set_rules('alamat','Alamat','required');
		$this->form_validation->set_rules('nohp','No HP','required');
		$this->form_validation->set_rules('username','Nama','required');
		$this->form_validation->set_rules('password','Password','required');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('admin/pegawai/update_view',$data);
		} else {
			$config['upload_path'] = './assets/img/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']  = '2000';
			$config['max_width']  = '1024';
			$config['max_height']  = '768';
			
			$this->load->library('upload', $config);
			
			if ( ! $this->upload->do_upload('foto')){
				$data['error'] = $this->upload->display_errors();
				$this->load->view('admin/pegawai/update_view',$data);
			}
			else{
				$this->Pegawai_model->update($id);
				$this->load->view('admin/pegawai/update_sukses');
			}
			
		}
	}
	public function delete($id)
	{
		$this->load->model('Pegawai_model');
		$this->Pegawai_model->delete($id);
		$this->load->view('admin/pegawai/delete_sukses');
	}
}
