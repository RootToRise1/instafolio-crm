<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h4 class="tw-font-semibold tw-text-lg"><?php echo _l('hr_payslips'); ?></h4>
                    <?php if (is_admin() || staff_can('create', 'hr')) { ?>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#generate_payslip">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('hr_generate_payslip'); ?>
                    </button>
                    <?php } ?>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('hr_pay_period'); ?></th>
                                        <th><?php echo _l('hr_base_salary'); ?></th>
                                        <th><?php echo _l('hr_pro_rata_amount'); ?></th>
                                        <th><?php echo _l('hr_allowances'); ?></th>
                                        <th><?php echo _l('hr_deductions'); ?></th>
                                        <th><?php echo _l('hr_net_salary'); ?></th>
                                        <th><?php echo _l('hr_status'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($payslips)) { ?>
                                    <?php foreach ($payslips as $payslip) { ?>
                                    <tr>
                                        <td><?php echo _d($payslip['pay_period_start']); ?> - <?php echo _d($payslip['pay_period_end']); ?></td>
                                        <td><?php echo app_format_money($payslip['basic_salary'], 'USD'); ?></td>
                                        <td><?php echo app_format_money($payslip['pro_rata_amount'], 'USD'); ?></td>
                                        <td><?php echo app_format_money($payslip['allowances'], 'USD'); ?></td>
                                        <td><?php echo app_format_money($payslip['deductions'] + $payslip['tax'], 'USD'); ?></td>
                                        <td><strong><?php echo app_format_money($payslip['net_salary'], 'USD'); ?></strong></td>
                                        <td>
                                            <?php
                                            $status_class = 'warning';
                                            if ($payslip['status'] == 'calculated') $status_class = 'info';
                                            elseif ($payslip['status'] == 'approved') $status_class = 'success';
                                            elseif ($payslip['status'] == 'paid') $status_class = 'primary';
                                            ?>
                                            <span class="badge bg-<?php echo $status_class; ?>"><?php echo _l('hr_' . $payslip['status']); ?></span>
                                        </td>
                                        <td>
                                            <a href="<?php echo admin_url('hr/payslip/' . $payslip['id']); ?>" class="btn btn-default btn-icon">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <?php if ($payslip['status'] != 'paid' && (is_admin() || staff_can('edit', 'hr'))) { ?>
                                            <a href="<?php echo admin_url('hr/update_payslip_status/' . $payslip['id'] . '/paid'); ?>" class="btn btn-success btn-icon">
                                                <i class="fa fa-check"></i>
                                            </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <?php } else { ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted"><?php echo _l('hr_no_records'); ?></td>
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

<?php if (is_admin() || staff_can('create', 'hr')) { ?>
<div class="modal fade" id="generate_payslip" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('hr_generate_payslip'); ?></h4>
            </div>
            <?php echo form_open(admin_url('hr/generate_payslip')); ?>
            <div class="modal-body">
                <?php echo render_select('staff_id', $this->staff_model->get('', ['active' => 1]), ['staffid', ['firstname', 'lastname']], _l('hr_employee')); ?>
                <?php echo render_date_input('pay_period_start', _l('hr_pay_period_start')); ?>
                <?php echo render_date_input('pay_period_end', _l('hr_pay_period_end')); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('hr_generate_payslip'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php } ?>

<?php init_tail(); ?>
