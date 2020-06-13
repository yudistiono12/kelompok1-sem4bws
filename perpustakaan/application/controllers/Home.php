<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

	public function index()
	{
		$data['title'] = 'Home';
		$this->load->view('home/templates/header');
		$this->load->view('home/index');
		$this->load->view('home/templates/footer');
	}

	public function profil()
	{
		$data['title'] = 'profile';
		$this->load->view('home/templates/header');
		$this->load->view('home/profile');
		$this->load->view('home/templates/footer');
	}

	public function rules()
	{
		$data['title'] = 'rules';
		$this->load->view('home/templates/header');
		$this->load->view('home/rules');
		$this->load->view('home/templates/footer');
	}

	public function login()
	{
		// if ($this->session->userdata('email')) {
		// 	redirect('profile');
		// }

		$this->form_validation->set_rules('username', 'username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');

		if ($this->form_validation->run() == false) {
			$data['title'] = 'Tempat Login';
			$this->load->view('home/templates/header', $data);
			$this->load->view('login_admin', $data);
			$this->load->view('home/templates/footer', $data);
		} else {
			//validasinya sukses
			$this->_masuk();
		}
	}
	private function _masuk()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$user = $this->db->get_where('autentikasi', ['username' => $username])->row_array();

		//jika usernya ada
		if ($user) {
			//jika usernya aktif
			//cek password
			if (md5($password, $user['password'])) {
				$data = [
					'id_autentikasi' => $user['id_autentikasi'],
					'id_jenis' => $user['id_jenis'],
				];

				if ($user['id_jenis'] == 1) {
					redirect('admin/dashboard');
				} else {
					redirect('anggota');
				}
			} else {
				$this->session->set_flashdata(
					'message',
					'<div class="alert alert-danger" role="alert">Password anda salah</div>'
				);
				redirect('home/login');
			}
		} else {
			$this->session->set_flashdata(
				'message',
				'<div class="alert alert-danger" role="alert">Akun anda tidak ditemukan!</div>'
			);
			redirect('home/login');
		}
	}

	public function registrasi()
	{
		$this->form_validation->set_rules('username', 'Username', 'required|trim');

		$this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]');
		$this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');


		if ($this->form_validation->run() == false) {
			$data['title'] = 'Form Registrasi';
			$this->load->view('home/templates/header', $data);
			$this->load->view('register_admin', $data);
			$this->load->view('home/templates/footer');
		} else {
			$data = [
				'username' => htmlspecialchars($this->input->post('username', true)),
				'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
				'id_jenis' => 1
			];

			$this->db->insert('autentikasi', $data);

			// $this->_sendEmail();

			$this->session->set_flashdata(
				'message',
				'<div class="alert alert-success" role="alert">Data anda ditambahkan. Silahkan Login</div>'
			);
			redirect('home/login');
		}
	}
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */