<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo isset($holiday) ? _l('hr_edit_holiday') : _l('hr_add_holiday'); ?></h4>
                        <?php echo form_open(admin_url('hr/setup/holidays')); ?>
                        <?php if (isset($holiday)) { ?>
                        <input type="hidden" name="id" value="<?php echo $holiday['id']; ?>">
                        <?php } ?>
                        <?php echo render_input('name', _l('hr_holiday_name'), 'text', isset($holiday) ? $holiday['name'] : ''); ?>
                        <div class="form-group">
                            <label for="date" class="control-label"><?php echo _l('hr_date'); ?></label>
                            <input type="date" name="date" class="form-control" value="<?php echo isset($holiday) ? $holiday['date'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="description" class="control-label"><?php echo _l('hr_description'); ?></label>
                            <textarea name="description" class="form-control" rows="3"><?php echo isset($holiday) ? $holiday['description'] : ''; ?></textarea>
                        </div>
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
                                    <th><?php echo _l('hr_holiday_name'); ?></th>
                                    <th><?php echo _l('hr_date'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($items) && is_array($items)) { ?>
                                <?php foreach ($items as $item) { ?>
                                <tr>
                                    <td><?php echo e($item['name']); ?></td>
                                    <td><?php echo _d($item['date']); ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('hr/setup/holidays/' . $item['id']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
                                        <a href="<?php echo admin_url('hr/delete_setup/holidays/' . $item['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
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
