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

            // Token
            $token = base64_encode(random_bytes(32));
            $user_token = [
                'email' => $data['email'],
                'token' => $token,
                'date_created' => time()
            ];

            // save data
            $this->db->insert('user_token', $user_token);
            $this->db->insert('user', $data);

            // kirim email ke user yg regis
            $this->_sendEmail($user_token, 'verify');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Congratulation! your account has been created. Please check your email to verify!</div>');
            redirect('auth');
        }
    }

    private function _sendEmail($user_token, $type)
    {
        $from = "anubae80@zohomail.com";
        $fromName = base_url();
        $pass = "CrushliciousMask712";
        $to = $user_token['email'];
        $token = $user_token['token'];

        $subject = $type . ' for CI3_Login';
        $message = 'Click this link to <a href="' . base_url() . 'auth/' . $type . '?email=' . $to . '&token=' . urlencode($token) . '">' . $type . '</a>.';

        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp.zoho.com';
        $config['smtp_crypto'] = 'ssl';
        $config['smtp_user'] = $from;
        $config['smtp_pass'] = $pass;
        $config['smtp_port'] = 465;
        $config['mailtype'] = 'html';
        $config['mailpath'] = '/usr/sbin/sendmail';
        // $config['charset'] = 'utf-8';      
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;

        $this->load->library('email', $config);
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");

        $this->email->from($from, $fromName);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);

        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }

    public function verify()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();
        if ($user) {
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();
            if ($user_token) {
                // cek token kurang dari sehari
                if (time() - $user_token['date_created'] < (60 * 60 * 24)) {
                    // update active user dan hapus token
                    $this->db->set('is_active', 1)->where(['email' => $email])->update('user');
                    $this->db->delete('user_token', ['email' => $email]);

                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">' . $email . ' has been activated!</div>');
                    redirect('auth');
                } else {
                    // hapus token
                    $this->db->delete('user', ['email' => $email]);
                    $this->db->delete('user_token', ['email' => $email]);

                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Token expired!</div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Token invalid!</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Account not found!</div>');
            redirect('auth');
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

    public function forgotPassword()
    {
        // rulse dari tiap kolom
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

        // jika form validasi gagal
        if ($this->form_validation->run() === FALSE) {
            $data['title'] = "Forgot Password";
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/forgotPassword');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email');

            $user = $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();
            if ($user) {
                $token = base64_encode(random_bytes(32));
                $user_token = [
                    'email' => $email,
                    'token' => $token,
                    'date_created' => time()
                ];

                // save data token
                $this->db->insert('user_token', $user_token);

                // kirim email ke user yg forgotPassword
                $this->_sendEmail($user_token, 'resetPassword');

                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Please check your email to reset your password!</div>');
                redirect('auth/forgotPassword');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email not found or not verify!</div>');
                redirect('auth/forgotPassword');
            }
        }
    }

    public function resetPassword()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();
        if ($user) {
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();
            if ($user_token) {
                // cek token kurang dari sehari
                if (time() - $user_token['date_created'] < (60 * 60 * 24)) {
                    $this->session->set_userdata('reset_email', $email);
                    $this->session->set_userdata('reset_token', $token);
                    redirect('auth/changePassword');
                } else {
                    // hapus token
                    $this->db->delete('user', ['email' => $email]);
                    $this->db->delete('user_token', ['email' => $email]);

                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Token expired!</div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Token invalid!</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Account not found!</div>');
            redirect('auth');
        }
    }

    public function changePassword()
    {
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'Password dont matches!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

        // jika form validasi gagal
        if ($this->form_validation->run() === FALSE) {
            $data['title'] = "Change Password";
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/changePassword');
            $this->load->view('templates/auth_footer');
        } else {
            $password = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
            $email = $this->session->userdata('reset_email');
            $token = $this->session->userdata('reset_token');

            // save data
            $this->db->set('password', $password)->where('email', $email)->update('user');
            // hapus token
            $this->db->delete('user_token', ['token' => $token]);

            // unset session
            $this->session->unset_userdata('reset_email');
            $this->session->unset_userdata('reset_token');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Password for ' . $email . ' has been change!</div>');
            redirect('auth');
        }
    }
}
