<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo isset($designation) ? _l('hr_edit_designation') : _l('hr_add_designation'); ?></h4>
                        <?php echo form_open(admin_url('hr/setup/designations')); ?>
                        <?php if (isset($designation)) { ?>
                        <input type="hidden" name="id" value="<?php echo $designation['id']; ?>">
                        <?php } ?>
                        <?php echo render_input('name', _l('hr_designation_name')); ?>
                        <?php 
                        $dept_options = [];
                        if (isset($departments) && is_array($departments)) {
                            foreach ($departments as $dept) {
                                $dept_options[] = ['id' => $dept['id'], 'name' => $dept['name']];
                            }
                        }
                        echo render_select('department_id', $dept_options, ['id', 'name'], _l('hr_department'), isset($designation['department_id']) ? $designation['department_id'] : ''); 
                        ?>
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
                                    <th><?php echo _l('hr_designation_name'); ?></th>
                                    <th><?php echo _l('hr_department'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($items) && is_array($items)) { ?>
                                <?php foreach ($items as $item) { 
                                    $dept_name = '-';
                                    foreach ($departments as $dept) {
                                        if (isset($dept['id']) && $dept['id'] == $item['department_id']) {
                                            $dept_name = $dept['name'];
                                            break;
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><?php echo e($item['name']); ?></td>
                                    <td><?php echo e($dept_name); ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('hr/setup/designations/' . $item['id']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
                                        <a href="<?php echo admin_url('hr/delete_setup/designations/' . $item['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
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
