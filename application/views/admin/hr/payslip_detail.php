<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-6">
                            <h3 class="tw-font-bold"><?php echo _l('hr_payslip'); ?></h3>
                            <div>
                                <?php if ($payslip['status'] != 'paid') { ?>
                                <a href="<?php echo admin_url('hr/update_payslip_status/' . $payslip['id'] . '/paid'); ?>" class="btn btn-success">
                                    <i class="fa fa-check tw-mr-1"></i>
                                    <?php echo _l('hr_paid'); ?>
                                </a>
                                <?php } else { ?>
                                <span class="badge bg-primary tw-text-lg tw-py-2 tw-px-4"><?php echo _l('hr_paid'); ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <div class="row tw-mb-6">
                            <div class="col-md-6">
                                <h5 class="tw-text-muted tw-text-sm tw-uppercase tw-mb-2"><?php echo _l('hr_employee'); ?></h5>
                                <p class="tw-font-medium tw-text-lg"><?php echo e($payslip['firstname'] . ' ' . $payslip['lastname']); ?></p>
                                <p class="tw-text-muted"><?php echo e($payslip['email']); ?></p>
                            </div>
                            <div class="col-md-6 tw-text-right">
                                <h5 class="tw-text-muted tw-text-sm tw-uppercase tw-mb-2"><?php echo _l('hr_pay_period'); ?></h5>
                                <p class="tw-font-medium tw-text-lg"><?php echo _d($payslip['pay_period_start']); ?> - <?php echo _d($payslip['pay_period_end']); ?></p>
                                <p class="tw-text-muted"><?php echo _l('hr_days_worked'); ?>: <?php echo floatval($payslip['days_worked']); ?> / <?php echo floatval($payslip['days_paid']); ?></p>
                            </div>
                        </div>

                        <hr class="tw-my-6">

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold tw-mb-4"><?php echo _l('hr_earnings'); ?></h5>
                                <table class="table tw-mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="tw-pl-0"><?php echo _l('hr_pro_rata'); ?> (<?php echo floatval($payslip['days_worked']); ?> days)</td>
                                            <td class="tw-text-right tw-pr-0"><?php echo app_format_money($payslip['pro_rata_amount'], 'USD'); ?></td>
                                        </tr>
                                        <?php if ($payslip['basic_salary'] > $payslip['pro_rata_amount']) { ?>
                                        <tr>
                                            <td class="tw-pl-0"><?php echo _l('hr_basic_salary'); ?></td>
                                            <td class="tw-text-right tw-pr-0"><?php echo app_format_money($payslip['basic_salary'], 'USD'); ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php if ($payslip['allowances'] > 0) { ?>
                                        <tr>
                                            <td class="tw-pl-0"><?php echo _l('hr_allowances'); ?></td>
                                            <td class="tw-text-right tw-pr-0"><?php echo app_format_money($payslip['allowances'], 'USD'); ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php if ($payslip['bonus'] > 0) { ?>
                                        <tr>
                                            <td class="tw-pl-0"><?php echo _l('hr_bonus'); ?></td>
                                            <td class="tw-text-right tw-pr-0"><?php echo app_format_money($payslip['bonus'], 'USD'); ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold tw-mb-4"><?php echo _l('hr_deductions'); ?></h5>
                                <table class="table tw-mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="tw-pl-0"><?php echo _l('hr_tax'); ?></td>
                                            <td class="tw-text-right tw-pr-0 tw-text-danger">-<?php echo app_format_money($payslip['tax'], 'USD'); ?></td>
                                        </tr>
                                        <?php
                                        $salary = $this->hr_model->get_staff_salary($payslip['staff_id']);
                                        if ($salary) {
                                        ?>
                                        <?php if ($salary['social_security'] > 0) { ?>
                                        <tr>
                                            <td class="tw-pl-0"><?php echo _l('hr_social_security'); ?></td>
                                            <td class="tw-text-right tw-pr-0 tw-text-danger">-<?php echo app_format_money($salary['social_security'], 'USD'); ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php if ($salary['health_insurance'] > 0) { ?>
                                        <tr>
                                            <td class="tw-pl-0"><?php echo _l('hr_health_insurance'); ?></td>
                                            <td class="tw-text-right tw-pr-0 tw-text-danger">-<?php echo app_format_money($salary['health_insurance'], 'USD'); ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php if ($salary['other_deductions'] > 0) { ?>
                                        <tr>
                                            <td class="tw-pl-0"><?php echo _l('hr_other_deductions'); ?></td>
                                            <td class="tw-text-right tw-pr-0 tw-text-danger">-<?php echo app_format_money($salary['other_deductions'], 'USD'); ?></td>
                                        </tr>
                                        <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr class="tw-my-6">

                        <div class="row tw-mb-4">
                            <div class="col-md-6 col-md-offset-6">
                                <table class="table tw-mb-0">
                                    <tbody>
                                        <tr class="tw-text-xl">
                                            <td class="tw-pl-0 tw-font-bold"><?php echo _l('hr_net_salary'); ?></td>
                                            <td class="tw-text-right tw-pr-0 tw-font-bold tw-text-success tw-text-xl"><?php echo app_format_money($payslip['net_salary'], 'USD'); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php if ($payslip['payment_date']) { ?>
                        <div class="tw-text-center tw-text-muted tw-mt-6">
                            <p><?php echo _l('hr_payment_date'); ?>: <?php echo _d($payslip['payment_date']); ?></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
