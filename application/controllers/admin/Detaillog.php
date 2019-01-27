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
			$this->load->model("detail_model");
			$this->load->library('form_validation');
		}

		public function index()
		{
			
			$data["detail_data"] = $this->detail_model->getAll();
			$this->load->view("admin/detail/list", $data);
		}

		public function select_lokasibarang()
		{
			$data["kode_data"] = $this->detail_model->lokasibarang();
			$this->load->view('admin/detail/new_form', $data);
		}

		public function select_unitkerja()
		{
			$data["kode_data"] = $this->detail_model->unitkerja();
			$this->load->view('admin/detail/new_form', $data);
		}

		public function select_kelompokbarang()
		{
			$data["kode_data"] = $this->detail_model->kelompokbarang();
			$this->load->view('admin/detail/new_form', $data);
		}

		public function select_subkelompok()
		{
			$data["kode_data"] = $this->detail_model->subkelompok();
			$this->load->view('admin/detail/new_form', $data);
		}

		public function select_subsubkelompok()
		{
			$data["kode_data"] = $this->detail_model->subsubkelompok();
			$this->load->view('admin/detail/new_form', $data);
		}

		public function add()
		{
			$detail = $this->detail_model;
			$validation = $this->form_validation;
			$validation->set_rules('warna','Warna','required');

			if($validation->run()){
				$detail->save();
				$this->session->set_flashdata('success', 'Berhasil disimpan');
			}

			$this->load->view('admin/detail/new_form');
		}
		public function edit($id = null)
	    {
	        if (!isset($id)) redirect('admin/detail');
	       
	        $detail = $this->detail_model;
	        $validation = $this->form_validation;
	        $validation->set_rules('warna','Warna','required');

	        if ($validation->run()) {
	            $detail->update();
	            $this->session->set_flashdata('success', 'Berhasil disimpan');
	        }

	        $data["detail"] = $detail->getById($id);
	        if (!$data["detail"]) show_404();
	        
	        $this->load->view("admin/detail/edit_form", $data);
	    }

	    public function delete($id=null)
	    {
	        if (!isset($id)) show_404();
	        
	        if ($this->detail_model->delete($id)) {
	            redirect(site_url('admin/detail'));
	        }
	    }
	}

?>