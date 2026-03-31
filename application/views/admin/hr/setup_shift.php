<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo isset($items) && count($items) > 0 ? _l('hr_add_shift') : _l('hr_add_shift'); ?></h4>
                        <?php echo form_open(admin_url('hr/setup/shifts')); ?>
                        <?php if (isset($id) && $id) { ?>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <?php } ?>
                        <?php echo render_input('name', _l('hr_shift_name'), 'text', isset($shift) ? $shift['name'] : ''); ?>
                        <div class="form-group">
                            <label for="start_time" class="control-label"><?php echo _l('hr_start_time'); ?></label>
                            <input type="time" name="start_time" class="form-control" value="<?php echo isset($shift) ? $shift['start_time'] : '09:00'; ?>">
                        </div>
                        <div class="form-group">
                            <label for="end_time" class="control-label"><?php echo _l('hr_end_time'); ?></label>
                            <input type="time" name="end_time" class="form-control" value="<?php echo isset($shift) ? $shift['end_time'] : '17:00'; ?>">
                        </div>
                        <?php echo render_input('grace_period_minutes', _l('hr_grace_period'), 'number', isset($shift) ? $shift['grace_period_minutes'] : '15', ['min' => 0]); ?>
                        <div class="checkbox">
                            <input type="checkbox" name="is_night_shift" id="is_night_shift" value="1" <?php echo isset($shift) && $shift['is_night_shift'] ? 'checked' : ''; ?>>
                            <label for="is_night_shift"><?php echo _l('hr_night_shift'); ?></label>
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
                                    <th><?php echo _l('hr_shift_name'); ?></th>
                                    <th><?php echo _l('hr_start_time'); ?></th>
                                    <th><?php echo _l('hr_end_time'); ?></th>
                                    <th><?php echo _l('hr_night_shift'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($items) && is_array($items)) { ?>
                                <?php foreach ($items as $item) { ?>
                                <tr>
                                    <td><?php echo e($item['name']); ?></td>
                                    <td><?php echo $item['start_time']; ?></td>
                                    <td><?php echo $item['end_time']; ?></td>
                                    <td><?php echo $item['is_night_shift'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-muted"></i>'; ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('hr/setup/shifts/' . $item['id']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
                                        <a href="<?php echo admin_url('hr/delete_setup/shifts/' . $item['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } else { ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted"><?php echo _l('hr_no_records'); ?></td>
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
