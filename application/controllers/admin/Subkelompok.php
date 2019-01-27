<?php
	
	defined('BASEPATH') OR exit('No direct script access allowed');
	/**
	 * 
	 */
	class Subkelompok extends CI_Controller
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
			$this->load->model("subkelompok_model");
			$this->load->library('form_validation');
		}

		public function index()
		{
			
			$data["subkelompok_data"] = $this->subkelompok_model->getAll();
			$this->load->view("admin/subkelompok/list", $data);
		}

		public function add()
		{
			$subkelompok = $this->subkelompok_model;
			$validation = $this->form_validation;
			$validation->set_rules($subkelompok->rules());

			if($validation->run()){
				$subkelompok->save();
				$this->session->set_flashdata('success', 'Berhasil disimpan');
			}

			$this->load->view('admin/subkelompok/new_form');
		}
		public function edit($id = null)
	    {
	        if (!isset($id)) redirect('admin/subkelompok');
	       
	        $subkelompok = $this->subkelompok_model;
	        $validation = $this->form_validation;
	        $validation->set_rules($subkelompok->rules());

	        if ($validation->run()) {
	            $subkelompok->update();
	            $this->session->set_flashdata('success', 'Berhasil disimpan');
	        }

	        $data["subkelompok"] = $subkelompok->getById($id);
	        if (!$data["subkelompok"]) show_404();
	        
	        $this->load->view("admin/subkelompok/edit_form", $data);
	    }

	    public function delete($id=null)
	    {
	        if (!isset($id)) show_404();
	        
	        if ($this->subkelompok_model->delete($id)) {
	            redirect(site_url('admin/subkelompok'));
	        }
	    }
	}

?>