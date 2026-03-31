<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo isset($salary) ? _l('hr_edit_salary') : _l('hr_add_salary'); ?></h4>
                        <?php echo form_open(admin_url('hr/salary'), ['class' => 'hr-salary-form']); ?>
                        <?php if (isset($salary) && $salary) { ?>
                        <input type="hidden" name="id" value="<?php echo $salary['id']; ?>">
                        <input type="hidden" name="staff_id" value="<?php echo $salary['staff_id']; ?>">
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_staff_name'); ?></label>
                            <p class="form-control-static"><?php echo e($salary['firstname'] . ' ' . $salary['lastname']); ?></p>
                        </div>
                        <?php } else { ?>
                        <?php echo render_select('staff_id', $staff_members, ['staffid', ['firstname', 'lastname']], _l('hr_employee')); ?>
                        <?php } ?>
                        
                        <?php echo render_input('base_salary', _l('hr_base_salary'), 'number', isset($salary['base_salary']) ? $salary['base_salary'] : ''); ?>
                        
                        <?php echo render_select('currency', [
                            ['id' => 'USD', 'name' => 'USD - US Dollar'],
                            ['id' => 'EUR', 'name' => 'EUR - Euro'],
                            ['id' => 'GBP', 'name' => 'GBP - British Pound'],
                            ['id' => 'BDT', 'name' => 'BDT - Bangladeshi Taka'],
                        ], ['id', 'name'], _l('hr_currency'), isset($salary['currency']) ? $salary['currency'] : 'USD'); ?>
                        
                        <?php echo render_select('pay_frequency', [
                            ['id' => 'monthly', 'name' => _l('hr_monthly')],
                            ['id' => 'bi-weekly', 'name' => _l('hr_bi_weekly')],
                            ['id' => 'weekly', 'name' => _l('hr_weekly')],
                        ], ['id', 'name'], _l('hr_pay_frequency'), isset($salary['pay_frequency']) ? $salary['pay_frequency'] : 'monthly'); ?>
                        
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_bank_details'); ?></label>
                        </div>
                        <?php echo render_input('bank_name', _l('hr_bank_name'), 'text', isset($salary['bank_name']) ? $salary['bank_name'] : ''); ?>
                        <?php echo render_input('bank_account', _l('hr_bank_account'), 'text', isset($salary['bank_account']) ? $salary['bank_account'] : ''); ?>
                        <?php echo render_input('tax_id', _l('hr_tax_id'), 'text', isset($salary['tax_id']) ? $salary['tax_id'] : ''); ?>
                        
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_deductions'); ?></label>
                        </div>
                        <?php echo render_input('social_security', _l('hr_social_security'), 'number', isset($salary['social_security']) ? $salary['social_security'] : '0'); ?>
                        <?php echo render_input('health_insurance', _l('hr_health_insurance'), 'number', isset($salary['health_insurance']) ? $salary['health_insurance'] : '0'); ?>
                        <?php echo render_input('other_deductions', _l('hr_other_deductions'), 'number', isset($salary['other_deductions']) ? $salary['other_deductions'] : '0'); ?>
                        
                        <?php echo render_date_input('effective_from', _l('hr_effective_from'), isset($salary['effective_from']) ? $salary['effective_from'] : date('Y-m-d')); ?>
                        
                        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
