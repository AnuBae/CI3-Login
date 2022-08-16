<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // check login and role user with function helper
        is_logged_in();

        // model
        $this->load->model('Menu_model', 'menu');
    }

    public function index()
    {
        $data['title'] = "Menu Management";
        // ambil data dari session
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        // model
        $data['menu'] = $this->menu->getMenu();

        // rules form menu
        $this->form_validation->set_rules('menu', 'Menu', 'required');

        // jika form validasi gagal
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('templates/footer');
        } else {
            $this->db->insert('user_menu', ['menu' => $this->input->post('menu')]);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">New menu added!</div>');
            redirect('menu');
        }
    }

    public function submenu()
    {
        $data['title'] = "Sub Menu Management";
        // ambil data dari session
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // model
        $data['subMenu'] = $this->menu->getSubMenu();
        $data['menu'] = $this->menu->getMenu();

        // rules form menu
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');

        // jika form validasi gagal
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/submenu', $data);
            $this->load->view('templates/footer');
        } else {
            $is_active = $this->input->post('is_active');
            if (!$is_active) {
                $is_active = "0";
            }
            $data = [
                'menu_id' => $this->input->post('menu_id'),
                'title' => $this->input->post('title'),
                'url' => $this->input->post('url'),
                'icon' => $this->input->post('icon'),
                'is_active' => $is_active
            ];

            $this->db->insert('user_sub_menu', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">New sub menu added!</div>');
            redirect('menu/submenu');
        }
    }

    public function edit()
    {
        // rules form menu
        $this->form_validation->set_rules('menu', 'Menu', 'required');

        // jika form validasi gagal
        if ($this->form_validation->run() === FALSE) {
            // benar benar memanggil semua fungsi - pengecekan dan penambahan
            $this->index();
        } else {
            $this->db->where(['id' => $this->uri->segment(3)]);
            $this->db->update('user_menu', ['menu' => $this->input->post('menu')]);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Edit menu succeed!</div>');
            // hanya beralih seakan-akan baru datang
            redirect('menu');
        }
    }

    public function editsub()
    {
        // rules form menu
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');

        // jika form validasi gagal
        if ($this->form_validation->run() === FALSE) {
            // benar benar memanggil semua fungsi - pengecekan dan penambahan
            $this->submenu();
        } else {
            echo $this->uri->segment(3);

            $is_active = $this->input->post('is_active');
            if (!$is_active) {
                $is_active = "0";
            }
            $data = [
                'menu_id' => $this->input->post('menu_id'),
                'title' => $this->input->post('title'),
                'url' => $this->input->post('url'),
                'icon' => $this->input->post('icon'),
                'is_active' => $is_active
            ];
            // var_dump($data);
            // die;

            $this->db->where(['id' => $this->uri->segment(3)]);
            $this->db->update('user_sub_menu', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Edit sub menu succeed!</div>');
            // hanya beralih seakan-akan baru datang
            redirect('menu/submenu');
        }
    }

    public function delete()
    {
        $id = $this->uri->segment(3);
        $this->menu->delete(['id' => $id], 'user_menu');
        // untuk menghapus sub menunya juga?
        $this->menu->delete(['menu_id' => $id], 'user_sub_menu');

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Delete menu succeed!</div>');
        // hanya beralih seakan-akan baru datang
        redirect('menu');
    }

    public function deletesub()
    {
        $this->menu->delete(['id' => $this->uri->segment(3)], 'user_sub_menu');
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Delete sub menu succeed!</div>');
        // hanya beralih seakan-akan baru datang
        redirect('menu/submenu');
    }
}
