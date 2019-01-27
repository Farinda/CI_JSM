<?php
	
	defined('BASEPATH') OR exit('No direct script access allowed');
	/**
	 * 
	 */
	class Report extends CI_Controller
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
			$this->load->model("report_model");
			$this->load->library('form_validation');
		}

		public function index()
		{
			
			$data["report_data"] = $this->report_model->getAll();
			$this->load->view("admin/report/list", $data);
		}

		public function add()
		{
			$report = $this->report_model;
			$validation = $this->form_validation;
			$validation->set_rules($report->rules());

			if($validation->run()){
				$report->save();
				$this->session->set_flashdata('success', 'Berhasil disimpan');
			}

			$this->load->view('admin/report/new_form');
		}
		public function edit($id = null)
	    {
	        if (!isset($id)) redirect('admin/report');
	       
	        $report = $this->report_model;
	        $validation = $this->form_validation;
	        $validation->set_rules($report->rules());

	        if ($validation->run()) {
	            $report->update();
	            $this->session->set_flashdata('success', 'Berhasil disimpan');
	        }

	        $data["report"] = $report->getById($id);
	        if (!$data["report"]) show_404();
	        
	        $this->load->view("admin/report/edit_form", $data);
	    }

	    public function delete($id=null)
	    {
	        if (!isset($id)) show_404();
	        
	        if ($this->report_model->delete($id)) {
	            redirect(site_url('admin/report'));
	        }
	    }
	}

?>