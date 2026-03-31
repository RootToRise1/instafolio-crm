<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (isset($member)) { ?>
<?php $salary_info = $hr_salary_info ?: []; ?>
<?php $prorata_info = $hr_prorata_info ?: null; ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-heading">
                <h4 class="panel-title"><?php echo _l('hr_salary_information'); ?></h4>
            </div>
            <div class="panel-body">
                
                <?php if (!empty($salary_info)) { ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_base_salary'); ?></label>
                            <p class="form-control-static tw-text-lg tw-font-semibold">
                                <?php echo app_format_money($salary_info['base_salary'], $base_currency); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_hourly_rate'); ?></label>
                            <p class="form-control-static">
                                <?php echo isset($salary_info['hourly_rate']) ? app_format_money($salary_info['hourly_rate'], $base_currency) : '-'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_monthly_salary'); ?></label>
                            <p class="form-control-static tw-text-lg tw-font-semibold">
                                <?php echo app_format_money($salary_info['base_salary'], $base_currency); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_daily_rate'); ?></label>
                            <p class="form-control-static">
                                <?php echo app_format_money(round($salary_info['base_salary'] / 30, 2), $base_currency); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <?php if ($prorata_info && isset($prorata_info['basic_salary'])) { ?>
                <hr />
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="tw-font-semibold tw-mb-3"><?php echo _l('hr_prorata_calculation'); ?> <small>(<?php echo date('F Y'); ?>)</small></h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_working_days'); ?></label>
                            <p class="form-control-static"><?php echo $prorata_info['working_days'] ?? 0; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_eligible_days'); ?></label>
                            <p class="form-control-static"><?php echo $prorata_info['salary_days'] ?? 0; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_prorata_factor'); ?></label>
                            <p class="form-control-static"><?php echo round(($prorata_info['prorata_factor'] ?? 1) * 100, 2); ?>%</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_prorata_salary'); ?></label>
                            <p class="form-control-static tw-text-lg tw-font-semibold tw-text-success">
                                <?php echo app_format_money($prorata_info['prorata_salary'] ?? 0, $base_currency); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($prorata_info['joining_date']) && $prorata_info['joining_date']) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <?php echo _l('hr_join_date_note'); ?>: <?php echo _d($prorata_info['joining_date']); ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php } ?>
                
                <?php } else { ?>
                <div class="alert alert-warning">
                    <i class="fa fa-warning"></i>
                    <?php echo _l('hr_no_salary_info'); ?>
                </div>
                <?php } ?>
                
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4 class="tw-font-semibold tw-mt-4 tw-mb-3"><?php echo _l('hr_update_salary'); ?></h4>
    </div>
</div>

<?php echo form_open(admin_url('hr/update_salary/' . $member->staffid)); ?>
<div class="row">
    <div class="col-md-6">
        <?php echo render_input('base_salary', 'hr_base_salary', isset($salary_info['base_salary']) ? $salary_info['base_salary'] : '', 'number'); ?>
    </div>
    <div class="col-md-6">
        <?php echo render_input('hourly_rate', 'hr_hourly_rate', isset($salary_info['hourly_rate']) ? $salary_info['hourly_rate'] : '', 'number'); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> <?php echo _l('submit'); ?>
        </button>
    </div>
</div>
<?php echo form_close(); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel_s tw-mt-4">
            <div class="panel-heading">
                <h4 class="panel-title"><?php echo _l('hr_leave_balances'); ?></h4>
            </div>
            <div class="panel-body">
                <?php if (!empty($leave_balances)) { ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo _l('hr_leave_type'); ?></th>
                                <th><?php echo _l('hr_allocated'); ?></th>
                                <th><?php echo _l('hr_used'); ?></th>
                                <th><?php echo _l('hr_pending'); ?></th>
                                <th><?php echo _l('hr_balance'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($leave_balances as $lb) { ?>
                            <tr>
                                <td><?php echo e($lb['leave_type_name']); ?></td>
                                <td><?php echo $lb['allocated']; ?></td>
                                <td><?php echo $lb['used']; ?></td>
                                <td><?php echo $lb['pending']; ?></td>
                                <td>
                                    <?php 
                                    $balance_class = $lb['balance'] <= 0 ? 'text-danger' : ($lb['balance'] < 3 ? 'text-warning' : 'text-success');
                                    ?>
                                    <span class="<?php echo $balance_class; ?> tw-font-semibold"><?php echo $lb['balance']; ?></span>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } else { ?>
                <div class="alert alert-info">
                    <?php echo _l('hr_no_leave_allocations'); ?>
                    <a href="<?php echo admin_url('hr/auto_allocate_leave/' . $member->staffid); ?>" class="btn btn-xs btn-primary tw-ml-2">
                        <i class="fa fa-plus"></i> <?php echo _l('hr_allocate_leave'); ?>
                    </a>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php } else { ?>
<div class="alert alert-warning">
    <?php echo _l('hr_save_employee_first'); ?>
</div>
<?php } ?>
