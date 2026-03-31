<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('hr_my_salary'); ?></h4>
                        <?php if ($salary) { ?>
                        <div class="tw-bg-gray-50 tw-p-4 tw-rounded tw-mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="tw-text-muted tw-text-sm"><?php echo _l('hr_base_salary'); ?></p>
                                    <p class="tw-text-xl tw-font-bold"><?php echo app_format_money($salary['base_salary'], $salary['currency']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="tw-text-muted tw-text-sm"><?php echo _l('hr_pay_frequency'); ?></p>
                                    <p class="tw-text-lg"><?php echo _l('hr_' . $salary['pay_frequency']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="tw-text-muted tw-text-sm"><?php echo _l('hr_effective_from'); ?></p>
                                    <p class="tw-text-lg"><?php echo _d($salary['effective_from']); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class="tw-text-center tw-py-8 tw-text-muted">
                            <i class="fa fa-money fa-4x tw-mb-4"></i>
                            <p><?php echo _l('hr_no_salary_configured'); ?></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                
                <?php if (!empty($payslips)) { ?>
                <div class="panel_s tw-mt-4">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('hr_payslips'); ?></h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('hr_pay_period'); ?></th>
                                        <th><?php echo _l('hr_net_salary'); ?></th>
                                        <th><?php echo _l('hr_status'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payslips as $p) { ?>
                                    <tr>
                                        <td><?php echo _d($p['pay_period_start']); ?> - <?php echo _d($p['pay_period_end']); ?></td>
                                        <td><?php echo app_format_money($p['net_salary'], 'USD'); ?></td>
                                        <td><span class="badge bg-<?php echo $p['status'] == 'paid' ? 'success' : 'warning'; ?>"><?php echo _l('hr_' . $p['status']); ?></span></td>
                                        <td>
                                            <a href="<?php echo admin_url('hr/payslip/' . $p['id']); ?>" class="btn btn-default btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
