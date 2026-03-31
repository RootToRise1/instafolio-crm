<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo isset($type) ? _l('hr_edit_leave_type') : _l('hr_add_leave_type'); ?></h4>
                        <?php echo form_open(admin_url('hr/leave_types')); ?>
                        <?php if (isset($type)) { ?>
                        <input type="hidden" name="id" value="<?php echo $type['id']; ?>">
                        <?php } ?>
                        <?php echo render_input('name', _l('hr_leave_type_name'), 'text', isset($type) ? $type['name'] : ''); ?>
                        <?php echo render_input('days_per_year', _l('hr_default_days'), 'number', isset($type) ? ($type['days_per_year'] ?? $type['days'] ?? '') : '', ['min' => 0]); ?>
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_color'); ?></label>
                            <input type="color" name="color" class="form-control" value="<?php echo isset($type) ? $type['color'] : '#3788d8'; ?>">
                        </div>
                        <div class="checkbox">
                            <input type="checkbox" name="is_paid" id="is_paid" value="1" <?php echo (isset($type) && $type['is_paid']) ? 'checked' : ''; ?>>
                            <label for="is_paid"><?php echo _l('hr_is_paid'); ?></label>
                        </div>
                        <div class="checkbox">
                            <input type="checkbox" name="require_approval" id="require_approval" value="1" <?php echo (isset($type) && $type['require_approval']) ? 'checked' : ''; ?>>
                            <label for="require_approval"><?php echo _l('hr_requires_approval'); ?></label>
                        </div>
                        <div class="checkbox">
                            <input type="checkbox" name="active" id="active" value="1" <?php echo (!isset($type) || (isset($type) && $type['active'])) ? 'checked' : ''; ?>>
                            <label for="active"><?php echo _l('hr_active'); ?></label>
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
                                    <th><?php echo _l('hr_leave_type_name'); ?></th>
                                    <th><?php echo _l('hr_default_days'); ?></th>
                                    <th><?php echo _l('hr_paid'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($types)) { ?>
                                <?php foreach ($types as $type) { ?>
                                <tr>
                                    <td>
                                        <span class="tw-inline-block tw-w-3 tw-h-3 tw-rounded tw-mr-2" style="background-color: <?php echo e($type['color']); ?>"></span>
                                        <?php echo e($type['name']); ?>
                                    </td>
                                    <td><?php echo intval($type['days_per_year'] ?? $type['days'] ?? 0); ?></td>
                                    <td><?php echo ($type['is_paid'] ?? 0) ? _l('yes') : _l('no'); ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('hr/leave_types/' . $type['id']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
                                        <a href="<?php echo admin_url('hr/delete_leave_type/' . $type['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } else { ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted"><?php echo _l('hr_no_records'); ?></td>
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
