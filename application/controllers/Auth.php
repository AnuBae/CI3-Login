<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    // public function __construct()
    // {
    //     parent::__construct();

    // }

    public function index()
    {
        // check sudah login atau belum
        if ($this->session->userdata('email')) {
            redirect('user');
        }
        // rulse dari tiap kolom
        // untuk siapa, nama lainnya, dan rules (lihat doc ci3)
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        // jika form validasi gagal
        if ($this->form_validation->run() === FALSE) {
            $data['title'] = "CI3 Login";
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            // validasi sukses
            // methode private yg cuma bisa di akses class ini saja
            $this->_login();
        }
    }

    private function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();
        // var_dump($user);
        // die;    

        // usernya ada
        if ($user) {
            // jika usernya aktif
            if ($user['is_active'] == 1) {
                // cek password
                if (password_verify($password, $user['password'])) {
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];
                    $this->session->set_userdata($data);
                    // cek role id
                    if ($user['role_id'] == 1) {
                        redirect('admin');
                    }
                    redirect('user');
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Wrong password!</div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email has not been activated!</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email is not registered!</div>');
            redirect('auth');
        }
    }

    public function registration()
    {
        // check sudah login atau belum
        if ($this->session->userdata('email')) {
            redirect('user');
        }
        // rulse dari tiap kolom
        // untuk siapa, nama lainnya, dan rules (lihat doc ci3)
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'This email has already registered!'
        ]);
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'Password dont matches!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

        // jika form validasi gagal
        if ($this->form_validation->run() === FALSE) {
            $data['title'] = "CI3 Registration";
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registration');
            $this->load->view('templates/auth_footer');
        } else {
            $data = [
                'name' => htmlspecialchars($this->input->post('name', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'image' => 'default.jpg',
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_active' => 0,
                'date_created' => time()
            ];

            // $this->db->insert('user', $data);

            // kirim email ke user yg regis
            $this->_sendEmail();

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Congratulation! your account has been created. Please Login!</div>');
            redirect('auth');
        }
    }

    private function _sendEmail()
    {
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
        $config['smtp_user'] = 'anubae80@gmail.com';
        $config['smtp_pass'] = 'CrushliciousMask712';
        $config['smtp_port'] = 465;
        $config['mailtype'] = 'html';
        // $config['charset'] = 'utf-8';      
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        $config['newline'] = "\r\n";

        $this->load->library('email', $config);
        $this->email->initialize($config);

        $this->email->from('anubae80@gmail.com', 'Rusmansyah Putra NH');
        $this->email->to('rusman.putra.712@gmail.com');
        $this->email->subject('Testing');
        $this->email->message('Hello Word');

        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }

    public function logout()
    {
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">You have been logout!</div>');
        redirect('auth');
    }

    public function blocked()
    {
        // // PR: looping ambil data sub menu from $userAccess['menu_id']
        // $role_id = $this->session->userdata('role_id');
        // $userAccess = $this->db->get_where('user_access_menu', [
        //     'role_id' => $role_id
        // ])->row_array();

        // $data['back'] = $this->uri->segment(3);
        // $this->load->view('auth/blocked', $data);

        $this->load->view('auth/blocked');
    }
}
