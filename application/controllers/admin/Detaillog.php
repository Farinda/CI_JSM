<?php
	
	defined('BASEPATH') OR exit('No direct script access allowed');
	/**
	 * 
	 */
	class Detaillog extends CI_Controller
	{
		
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
			$this->load->model("detaillog_model");
			$this->load->library('form_validation');
		}

		public function index()
		{
			
			$data["detail_data"] = $this->detaillog_model->getAll();
			$this->load->view("admin/detaillog/list", $data);
		}
	}

?>