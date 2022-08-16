        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

            <div class="row">
                <div class="col-lg">
                    <?php if (validation_errors()) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?= validation_errors() ?>
                        </div>
                    <?php endif; ?>
                    <?= $this->session->flashdata('message'); ?>

                    <a href="" class="btn btn-info mb-3" data-toggle="modal" data-target="#newSubMenuModal">Add New Sub Menu</a>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Title</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Url</th>
                                <th scope="col">Icon</th>
                                <th scope="col">Active</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($subMenu as $sm) : ?>
                                <tr>
                                    <th scope="row"><?= $i++; ?></th>
                                    <td><?= $sm['title']; ?></td>
                                    <td><?= $sm['menu']; ?></td>
                                    <td><?= $sm['url']; ?></td>
                                    <td>( <i class="<?= $sm['icon']; ?>"></i> ) <?= $sm['icon']; ?></td>
                                    <td>
                                        <?php
                                        $is_active = "active";
                                        if ($sm['is_active'] == 0) $is_active = "not active";
                                        echo $is_active;
                                        ?>
                                    </td>
                                    <td>
                                        <a href="" class="badge badge-primary" data-toggle="modal" data-target="#editSubMenuModal<?= $sm['id']; ?>">edit</a>
                                        <a href="" class="badge badge-danger" data-toggle="modal" data-target="#deleteSubMenuModal<?= $sm['id']; ?>">delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Modal Add SUb Menu -->
        <div class="modal fade" id="newSubMenuModal" tabindex="-1" aria-labelledby="newSubMenuModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newSubMenuModalLabel">Add New SubMenu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="<?= base_url('menu/submenu'); ?>" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" class="form-control" id="title" name="title" placeholder="SubMenu title">
                            </div>
                            <div class="form-group">
                                <select name="menu_id" id="menu_id" class="form-control">
                                    <option value="">Select Menu</option>
                                    <?php foreach ($menu as $m) : ?>
                                        <option value="<?= $m['id']; ?>"><?= $m['menu']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="url" name="url" placeholder="SubMenu Url">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="icon" name="icon" placeholder="SubMenu Icon">
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_active" id="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        Active?
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit SubMenu -->
        <?php foreach ($subMenu as $sm) : ?>
            <div class="modal fade" id="editSubMenuModal<?= $sm['id']; ?>" tabindex="-1" aria-labelledby="editSubMenuModal<?= $sm['id']; ?>Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editSubMenuModal<?= $sm['id']; ?>Label">Edit Sub Menu</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= base_url('menu/editsub/') . $sm['id']; ?>" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="title" name="title" placeholder="SubMenu title" value="<?= $sm['title']; ?>">
                                </div>
                                <div class="form-group">
                                    <select name="menu_id" id="menu_id" class="form-control">
                                        <option value="">Select Menu</option>
                                        <?php foreach ($menu as $m) : ?>
                                            <option value="<?= $m['id']; ?>" <?php if ($m['id'] == $sm['menu_id']) echo 'selected'; ?>><?= $m['menu']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="url" name="url" placeholder="SubMenu Url" value="<?= $sm['url']; ?>">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="icon" name="icon" placeholder="SubMenu Icon" value="<?= $sm['icon']; ?>">
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" name="is_active" id="is_active" checked>
                                        <label class="form-check-label" for="is_active">
                                            Active?
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Modal Delete SUb Menu -->
        <?php foreach ($subMenu as $sm) : ?>
            <div class="modal fade" id="deleteSubMenuModal<?= $sm['id']; ?>" tabindex="-1" aria-labelledby="deleteSubMenuModal<?= $sm['id']; ?>Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editSubMenuModal<?= $sm['id']; ?>Label">Edit Sub Menu</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <p>Are you sure to delete <?= $sm['title']; ?>?</p>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <a href="<?= base_url('menu/deletesub/') . $sm['id']; ?>" type="submit" class="btn btn-primary">Save changes</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>