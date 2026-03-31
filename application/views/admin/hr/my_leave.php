<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('hr_new_leave_request'); ?></h4>
                        <?php echo form_open(admin_url('hr/my_leave'), ['id' => 'new-leave-request']); ?>
                        <?php echo render_select('leave_type_id', $leave_types, ['id', 'name'], _l('hr_leave_type')); ?>
                        <?php echo render_date_input('start_date', _l('hr_from_date')); ?>
                        <?php echo render_date_input('end_date', _l('hr_to_date')); ?>
                        <?php echo render_textarea('reason', _l('hr_reason'), '', ['rows' => 3]); ?>
                        <button type="submit" class="btn btn-primary"><?php echo _l('hr_submit_request'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('hr_my_leave_balance'); ?></h4>
                        <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                            <?php if (!empty($leave_balances)) { ?>
                            <?php foreach ($leave_balances as $balance) { ?>
                            <div class="tw-bg-gray-50 tw-p-4 tw-rounded">
                                <p class="tw-font-medium"><?php echo e($balance['name']); ?></p>
                                <p class="tw-text-2xl tw-font-bold"><?php echo floatval($balance['available']); ?> <span class="tw-text-sm tw-font-normal tw-text-gray-500">/ <?php echo floatval($balance['allocated']); ?></span></p>
                            </div>
                            <?php } ?>
                            <?php } else { ?>
                            <div class="tw-col-span-2 tw-text-center tw-text-muted tw-py-4">
                                <?php echo _l('hr_no_leave_balances'); ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="panel_s tw-mt-4">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('hr_my_leave_history'); ?></h4>
                        <?php
                        $table_data = [
                            _l('hr_leave_type'),
                            _l('hr_from_date'),
                            _l('hr_to_date'),
                            _l('hr_days'),
                            _l('hr_status'),
                            _l('hr_applied_on'),
                        ];
                        render_datatable($table_data, 'hr_my_leave');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
