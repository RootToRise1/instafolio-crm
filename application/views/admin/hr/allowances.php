<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo isset($allowance) ? _l('hr_edit_allowance') : _l('hr_add_allowance'); ?></h4>
                        <?php echo form_open(admin_url('hr/allowances'), ['id' => 'allowance-form']); ?>
                        <?php if (isset($allowance)) { ?>
                        <input type="hidden" name="id" value="<?php echo $allowance['id']; ?>">
                        <?php } ?>
                        <?php echo render_input('name', _l('hr_allowance_name'), 'text', isset($allowance) ? $allowance['name'] : ''); ?>
                        <?php echo render_select('type', [
                            ['id' => 'fixed', 'name' => _l('hr_fixed')],
                            ['id' => 'percentage', 'name' => _l('hr_percentage')],
                        ], ['id', 'name'], _l('hr_allowance_type'), isset($allowance) ? $allowance['type'] : 'fixed'); ?>
                        <?php echo render_input('amount', _l('hr_amount'), 'number', isset($allowance) ? $allowance['amount'] : '', ['min' => 0, 'step' => '0.01']); ?>
                        <div class="checkbox">
                            <input type="checkbox" name="is_taxable" id="is_taxable" value="1" <?php echo (!isset($allowance) || (isset($allowance) && $allowance['is_taxable'])) ? 'checked' : ''; ?>>
                            <label for="is_taxable"><?php echo _l('hr_is_taxable'); ?></label>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                        <a href="<?php echo admin_url('hr/allowances'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
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
                                    <th><?php echo _l('hr_allowance_name'); ?></th>
                                    <th><?php echo _l('hr_allowance_type'); ?></th>
                                    <th><?php echo _l('hr_amount'); ?></th>
                                    <th><?php echo _l('hr_is_taxable'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($allowances)) { ?>
                                <?php foreach ($allowances as $a) { ?>
                                <tr>
                                    <td><?php echo e($a['name']); ?></td>
                                    <td><span class="badge bg-info"><?php echo _l('hr_' . $a['type']); ?></span></td>
                                    <td><?php echo app_format_money($a['amount'], 'USD'); ?></td>
                                    <td><?php echo $a['is_taxable'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-muted"></i>'; ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('hr/allowances/' . $a['id']); ?>" class="btn btn-default btn-icon">
                                            <i class="fa fa-pencil-square-o"></i>
                                        </a>
                                        <a href="<?php echo admin_url('hr/delete_allowance/' . $a['id']); ?>" class="btn btn-danger btn-icon _delete">
                                            <i class="fa fa-remove"></i>
                                        </a>
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
