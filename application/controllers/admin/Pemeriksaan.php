<?php
	
	defined('BASEPATH') OR exit('No direct script access allowed');
	/**
	 * 
	 */
	class Pemeriksaan extends CI_Controller
	{
		
		public function __construct()
		{
			parent::__construct();
			$this->load->model("pemeriksaan_model");
			$this->load->library('form_validation');
		}

		public function index()
		{
			
			$data["pemeriksaan_data"] = $this->pemeriksaan_model->getAll();
			$this->load->view("admin/pemeriksaan/list", $data);
		}

		public function select_ruang()
		{
			$data["ruang_data"] = $this->pemeriksaan_model->ruang();
			$this->load->view('admin/pemeriksaan/new_form', $data);
		}

		public function select_detail()
		{
			$data["detail_data"] = $this->pemeriksaan_model->detail();
			$this->load->view('admin/pemeriksaan/new_form', $data);
		}

		public function add()
		{
			$pemeriksaan = $this->pemeriksaan_model;
			$validation = $this->form_validation;
			$validation->set_rules($pemeriksaan->rules());

			if($validation->run()){
				$pemeriksaan->save();
				$this->session->set_flashdata('success', 'Berhasil disimpan');
			}

			$this->load->view('admin/pemeriksaan/new_form');
		}
		public function edit($id = null)
	    {
	        if (!isset($id)) redirect('admin/pemeriksaan');
	       
	        $pemeriksaan = $this->pemeriksaan_model;
	        $validation = $this->form_validation;
	        $validation->set_rules($pemeriksaan->rules());

	        if ($validation->run()) {
	            $pemeriksaan->update();
	            $this->session->set_flashdata('success', 'Berhasil disimpan');
	        }

	        $data["pemeriksaan"] = $pemeriksaan->getById($id);
	        if (!$data["pemeriksaan"]) show_404();
	        
	        $this->load->view("admin/pemeriksaan/edit_form", $data);
	    }

	    public function delete($id=null)
	    {
	        if (!isset($id)) show_404();
	        
	        if ($this->pemeriksaan_model->delete($id)) {
	            redirect(site_url('admin/pemeriksaan'));
	        }
	    }
	}

?>