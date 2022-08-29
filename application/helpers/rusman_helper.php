<?php

// check login and role user
function is_logged_in()
{
    // make new instans CI3
    $ci3 = get_instance();

    // check login
    if (!$ci3->session->userdata('email')) {
        redirect('auth');
    } else {
        // check role user
        $role_id = $ci3->session->userdata('role_id');
        // check controller
        $menu = $ci3->uri->segment(1);

        $queryMenu = $ci3->db->get_where('user_menu', ['menu' => $menu])->row_array();
        $menu_id = $queryMenu['id'];

        // check access menu
        $userAccess = $ci3->db->get_where('user_access_menu', [
            'role_id' => $role_id,
            'menu_id' => $menu_id
        ]);

        // jika $userAccess tidak menghasilkan data apa-apa (tidak ada access)
        if ($userAccess->num_rows() < 1) {
            redirect('auth/blocked');
        }
    }
}


function check_access($role_id, $menu_id)
{
    // make new instans CI3
    $ci3 = get_instance();

    $ci3->db->where('role_id', $role_id);
    $ci3->db->where('menu_id', $menu_id);
    $result = $ci3->db->get('user_access_menu');

    if ($result->num_rows() > 0) {
        return "checked = 'checked'";
    }
}
