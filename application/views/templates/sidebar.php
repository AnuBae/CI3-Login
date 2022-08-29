        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fab fa-free-code-camp"></i>
                </div>
                <div class="sidebar-brand-text mx-3">CI3 Admin</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- query menu -->
            <?php
            // ambil role id dari session
            $role_id = $this->session->userdata('role_id');
            // pilih semua data id dan menu yang ada di user_menu digabung dengan user_access_menu sesuai dengan id user_menu sama dengan menu_id user_access_menu yang mana role_id user_access_menu sesuai dengan $role_id session dan diurutkan menaik sesuai menu_id user_access_menu
            $queryMenu = "SELECT `user_menu`.`id`, `menu` FROM `user_menu` JOIN `user_access_menu` ON `user_menu`.`id` = `user_access_menu`.`menu_id` WHERE `user_access_menu`.`role_id` = $role_id ORDER BY `user_access_menu`.`menu_id` ASC";
            $menu = $this->db->query($queryMenu)->result_array();
            ?>

            <!-- looping menu -->
            <?php foreach ($menu as $m) : ?>
                <!-- Heading -->
                <div class="sidebar-heading">
                    <?= $m['menu']; ?>
                </div>

                <!-- sub menu sesuai menu -->
                <?php
                $menu_id = $m['id'];
                $querySubMenu = "SELECT * FROM `user_sub_menu` WHERE `menu_id` = $menu_id AND `is_active` = 1";
                $subMenu = $this->db->query($querySubMenu)->result_array();
                ?>

                <!-- looping sub menu -->
                <?php foreach ($subMenu as $sm) : ?>
                    <!-- Nav Item - Dashboard -->
                    <!-- Bantuan Visual - akan active sesuai menu -->
                    <li class="nav-item <?php if ($title == $sm['title']) echo 'active'; ?>">
                        <a class="nav-link pb-0" href="<?= base_url($sm['url']); ?>">
                            <i class="<?= $sm['icon']; ?>"></i>
                            <span><?= $sm['title']; ?></span></a>
                    </li>
                <?php endforeach; ?>

                <!-- Divider -->
                <hr class="sidebar-divider mt-3">

            <?php endforeach; ?>

            <!-- Nav Item - Profile -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('auth/logout'); ?>">
                    <i class="fas fa-sign-out-alt fa-fw"></i>
                    <span>Logout</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->