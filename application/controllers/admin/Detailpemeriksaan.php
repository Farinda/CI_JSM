<?php
	
	defined('BASEPATH') OR exit('No direct script access allowed');
	/**
	 * 
	 */
	class Detailpemeriksaan extends CI_Controller
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
				$this->load->model("detailpemeriksaan_model");
				$this->load->library('form_validation');
		}

		public function index($id=null)
		{
			if($id == null){
				redirect('admin/pemeriksaan');
			}
			$data['id_pemeriksaan'] = $id;
			$data['pemeriksaan'] = $this->db->where('id',$id)->get('tb_pemeriksaan')->row(0);
			$data["detailpemeriksaan_data"] = $this->detailpemeriksaan_model->get_by_pemeriksaan($id);
			$this->load->view("admin/detailpemeriksaan/list", $data);
		}

		public function select_ruang()
		{
			$data["ruang_data"] = $this->detailpemeriksaan_model->ruang();
			$this->load->view('admin/detailpemeriksaan/new_form', $data);
		}

		public function select_detail()
		{
			$data["detail_data"] = $this->detailpemeriksaan_model->detail();
			$this->load->view('admin/detailpemeriksaan/new_form', $data);
		}

		public function add($id_pemeriksaan)
		{
			$detailpemeriksaan = $this->detailpemeriksaan_model;
			$validation = $this->form_validation;
			$validation->set_rules('id_pemeriksaan','id_pemeriksaan','required');

			if($validation->run()){
				$detailpemeriksaan->save($id_pemeriksaan);
				$this->session->set_flashdata('success', 'Berhasil disimpan');
				redirect('admin/detailpemeriksaan/index/'.$id_pemeriksaan);
			}
			$data['pemeriksaan'] = $this->db->where('id',$id_pemeriksaan)->get('tb_pemeriksaan')->row(0);
			$this->load->view('admin/detailpemeriksaan/new_form',$data);
		}
		public function edit($id = null)
	    {
	        if (!isset($id)) redirect('admin/pemeriksaan/');
	       
	        $detailpemeriksaan = $this->detailpemeriksaan_model;
	        $validation = $this->form_validation;
	        $validation->set_rules('id_pemeriksaan','id_pemeriksaan','required');

	        if ($validation->run()) {
	            $detailpemeriksaan->update();
	            $this->session->set_flashdata('success', 'Berhasil disimpan');
	        }

	        $data["detailpemeriksaan"] = $detailpemeriksaan->getById($id);

			$data['pemeriksaan'] = $this->db->where('id',$data['detailpemeriksaan']->id_pemeriksaan)->get('tb_pemeriksaan')->row(0);
	        if (!$data["detailpemeriksaan"]) show_404();
	        
	        $this->load->view("admin/detailpemeriksaan/edit_form", $data);
	    }

	    public function delete($id=null)
	    {
	        if (!isset($id)) show_404();
	        
	        if ($this->detailpemeriksaan_model->delete($id)) {
	            redirect(site_url('admin/pemeriksaan'));
	        }
	    }

	    public function reportkartu($id_pemeriksaan)
	    {
	    	$data['pemeriksaan'] = $this->db->where('id',$id_pemeriksaan)->get('tb_pemeriksaan')->row(0);
	    	$data['detailpemeriksaan'] = $this->detailpemeriksaan_model->get_by_pemeriksaan($id_pemeriksaan);
	    	$this->load->view('admin/detailpemeriksaan/reportkartu', $data);
	    }
	}

?>