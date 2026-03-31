<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (isset($member)) { ?>
<?php $bank_details = $hr_bank_details ?: []; ?>
<?php $primary_bank = null; foreach($bank_details as $b) { if($b['is_primary']) { $primary_bank = $b; break; } } ?>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            <?php echo _l('hr_bank_details_info'); ?>
        </div>
    </div>
</div>

<?php if (count($bank_details) > 0) { ?>
<div class="row">
    <div class="col-md-12">
        <h4 class="tw-font-semibold tw-mb-3"><?php echo _l('hr_saved_bank_accounts'); ?></h4>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo _l('hr_bank_name'); ?></th>
                        <th><?php echo _l('hr_account_name'); ?></th>
                        <th><?php echo _l('hr_account_number'); ?></th>
                        <th><?php echo _l('hr_routing_number'); ?></th>
                        <th><?php echo _l('hr_is_primary'); ?></th>
                        <th><?php echo _l('options'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($bank_details as $bank) { ?>
                    <tr>
                        <td><?php echo e($bank['bank_name']); ?></td>
                        <td><?php echo e($bank['account_name']); ?></td>
                        <td><?php echo e($bank['account_number']); ?></td>
                        <td><?php echo e($bank['routing_number']); ?></td>
                        <td>
                            <?php if($bank['is_primary']) { ?>
                            <span class="label label-success"><?php echo _l('yes'); ?></span>
                            <?php } else { ?>
                            <span class="label label-default"><?php echo _l('no'); ?></span>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('hr/delete_bank_details/' . $bank['id']); ?>" class="btn btn-danger btn-xs _delete">
                                <i class="fa fa-trash"></i>
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

<div class="row">
    <div class="col-md-12">
        <h4 class="tw-font-semibold tw-mt-4 tw-mb-3"><?php echo _l('hr_add_bank_account'); ?></h4>
    </div>
</div>

<?php echo form_open(admin_url('hr/add_bank_details/' . $member->staffid)); ?>
<div class="row">
    <div class="col-md-6">
        <?php echo render_input('bank_name', 'hr_bank_name', '', 'text', ['required' => 'required']); ?>
    </div>
    <div class="col-md-6">
        <?php echo render_input('account_name', 'hr_account_name', '', 'text', ['required' => 'required']); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php echo render_input('account_number', 'hr_account_number', '', 'text', ['required' => 'required']); ?>
    </div>
    <div class="col-md-6">
        <?php echo render_input('routing_number', 'hr_routing_number', ''); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php echo render_input('iban', 'hr_iban', ''); ?>
    </div>
    <div class="col-md-6">
        <?php echo render_input('swift_code', 'hr_swift_code', ''); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="checkbox checkbox-primary">
            <input type="checkbox" name="is_primary" id="is_primary" value="1">
            <label for="is_primary"><?php echo _l('hr_set_as_primary_account'); ?></label>
        </div>
    </div>
    <div class="col-md-6 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-plus"></i> <?php echo _l('hr_add_bank_account'); ?>
        </button>
    </div>
</div>
<?php echo form_close(); ?>

<?php } else { ?>
<div class="alert alert-warning">
    <?php echo _l('hr_save_employee_first'); ?>
</div>
<?php } ?>
