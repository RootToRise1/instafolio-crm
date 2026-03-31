<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (isset($member)) { ?>
<?php $emergency_contacts = $hr_emergency_contacts ?: []; ?>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            <?php echo _l('hr_emergency_contact_info'); ?>
        </div>
    </div>
</div>

<?php if (count($emergency_contacts) > 0) { ?>
<div class="row">
    <div class="col-md-12">
        <h4 class="tw-font-semibold tw-mb-3"><?php echo _l('hr_saved_contacts'); ?></h4>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo _l('hr_contact_name'); ?></th>
                        <th><?php echo _l('hr_relationship'); ?></th>
                        <th><?php echo _l('hr_phone'); ?></th>
                        <th><?php echo _l('hr_email'); ?></th>
                        <th><?php echo _l('hr_is_primary'); ?></th>
                        <th><?php echo _l('options'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($emergency_contacts as $contact) { ?>
                    <tr>
                        <td><?php echo e($contact['name']); ?></td>
                        <td><?php echo e($contact['relationship']); ?></td>
                        <td><?php echo e($contact['phone']); ?></td>
                        <td><?php echo e($contact['email']); ?></td>
                        <td>
                            <?php if($contact['is_primary']) { ?>
                            <span class="label label-success"><?php echo _l('yes'); ?></span>
                            <?php } else { ?>
                            <span class="label label-default"><?php echo _l('no'); ?></span>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('hr/delete_emergency_contact/' . $contact['id']); ?>" class="btn btn-danger btn-xs _delete">
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
        <h4 class="tw-font-semibold tw-mt-4 tw-mb-3"><?php echo _l('hr_add_emergency_contact'); ?></h4>
    </div>
</div>

<?php echo form_open(admin_url('hr/add_emergency_contact/' . $member->staffid)); ?>
<div class="row">
    <div class="col-md-6">
        <?php echo render_input('name', 'hr_contact_name', '', 'text', ['required' => 'required']); ?>
    </div>
    <div class="col-md-6">
        <?php
        $relationships = [
            ['id' => 'Spouse', 'name' => 'Spouse'],
            ['id' => 'Parent', 'name' => 'Parent'],
            ['id' => 'Sibling', 'name' => 'Sibling'],
            ['id' => 'Child', 'name' => 'Child'],
            ['id' => 'Friend', 'name' => 'Friend'],
            ['id' => 'Other', 'name' => 'Other'],
        ];
        echo render_select('relationship', $relationships, ['id', 'name'], 'hr_relationship', '', [], [], true);
        ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php echo render_input('phone', 'hr_phone', '', 'text', ['required' => 'required']); ?>
    </div>
    <div class="col-md-6">
        <?php echo render_input('email', 'hr_email', '', 'email'); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="address" class="control-label"><?php echo _l('hr_address'); ?></label>
            <textarea name="address" id="address" class="form-control" rows="2"></textarea>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="checkbox checkbox-primary">
            <input type="checkbox" name="is_primary" id="is_primary_contact" value="1">
            <label for="is_primary_contact"><?php echo _l('hr_set_as_primary_contact'); ?></label>
        </div>
    </div>
    <div class="col-md-6 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-plus"></i> <?php echo _l('hr_add_contact'); ?>
        </button>
    </div>
</div>
<?php echo form_close(); ?>

<?php } else { ?>
<div class="alert alert-warning">
    <?php echo _l('hr_save_employee_first'); ?>
</div>
<?php } ?>
