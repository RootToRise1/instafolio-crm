<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h4 class="tw-font-semibold tw-text-lg"><?php echo _l('hr_salaries'); ?></h4>
                    <?php if (is_admin() || staff_can('create', 'hr')) { ?>
                    <a href="<?php echo admin_url('hr/salary'); ?>" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('hr_add_salary'); ?>
                    </a>
                    <?php } ?>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('hr_staff_name'); ?></th>
                                        <th><?php echo _l('hr_base_salary'); ?></th>
                                        <th><?php echo _l('hr_currency'); ?></th>
                                        <th><?php echo _l('hr_pay_frequency'); ?></th>
                                        <th><?php echo _l('hr_effective_from'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($salaries)) { ?>
                                    <?php foreach ($salaries as $salary) { ?>
                                    <tr>
                                        <td><?php echo e($salary['firstname'] . ' ' . $salary['lastname']); ?></td>
                                        <td><?php echo app_format_money($salary['base_salary'], $salary['currency']); ?></td>
                                        <td><?php echo e($salary['currency']); ?></td>
                                        <td><?php echo _l('hr_' . $salary['pay_frequency']); ?></td>
                                        <td><?php echo _d($salary['effective_from']); ?></td>
                                        <td>
                                            <a href="<?php echo admin_url('hr/salary/' . $salary['staff_id']); ?>" class="btn btn-default btn-icon">
                                                <i class="fa fa-pencil-square-o"></i>
                                            </a>
                                            <a href="<?php echo admin_url('hr/payslips/' . $salary['staff_id']); ?>" class="btn btn-info btn-icon">
                                                <i class="fa fa-file-text-o"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <?php } else { ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted"><?php echo _l('hr_no_records'); ?></td>
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
</div>
<?php init_tail(); ?>
