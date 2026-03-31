<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo isset($department) ? _l('hr_edit_department') : _l('hr_add_department'); ?></h4>
                        <?php echo form_open(admin_url('hr/setup/departments')); ?>
                        <?php if (isset($department)) { ?>
                        <input type="hidden" name="id" value="<?php echo $department['id']; ?>">
                        <?php } ?>
                        <?php echo render_input('name', _l('hr_department_name')); ?>
                        <?php echo render_textarea('description', _l('hr_department_description'), isset($department) ? $department['description'] : ''); ?>
                        <?php echo render_select('manager_id', $staff_members, ['staffid', ['firstname', 'lastname']], _l('hr_manager'), isset($department['manager_id']) ? $department['manager_id'] : ''); ?>
                        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo _l('hr_department_name'); ?></th>
                                    <th><?php echo _l('hr_manager'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($items) && is_array($items)) { ?>
                                <?php foreach ($items as $item) { 
                                    $manager = null;
                                    if ($item['manager_id']) {
                                        foreach ($staff_members as $sm) {
                                            if ($sm['staffid'] == $item['manager_id']) {
                                                $manager = $sm['firstname'] . ' ' . $sm['lastname'];
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><?php echo e($item['name']); ?></td>
                                    <td><?php echo $manager ? e($manager) : '-'; ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('hr/setup/departments/' . $item['id']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
                                        <a href="<?php echo admin_url('hr/delete_setup/departments/' . $item['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } else { ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted"><?php echo _l('hr_no_records'); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
