<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_login extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		// if ($this->session->userdata('logged_in')) {
		// 	$session_data = $this->session->userdata('logged_in');
		// 	$data['username'] = $session_data['username'];
		// 	$data['level'] = $session_data['level'];
		// 	$current_controller = $this->router->fetch_class();
		// 	$this->load->library('acl');
		// 	if (!$this->acl->is_public($current_controller)) {
		// 		if (!$this->acl->is_allowed($current_controller,$data['level'])) {
		// 			echo '<script>alert("Tidak Dapat Akses")</script>';
		// 			redirect('Login/logout','refresh');
		// 		}
		// 	}
		// }else{
		// 	echo '<script>alert("Login Dahulu")</script>';
		// 	redirect('Login');
		// }
	}
	public function index()
	{
		$this->load->view('admin/overview');
	}
	public function detail($filter = null)
		{
			$this->load->model('detail_model');
			switch ($filter) {
				case 'all':
					
					break;
				case 'rusak':
					$this->db->where('status',"Rusak");
					break;

				case 'baik':
					$this->db->where('status',"Baik");
					break;
				
				default:

					break;
			}

			$data["detail_data"] = $this->detail_model->getAll();
			$this->load->view("admin/overview/detail", $data);
		}
}
