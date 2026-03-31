<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (isset($member)) { ?>
<?php echo form_hidden('isedit'); ?>
<?php echo form_hidden('staffid', $member->staffid); ?>
<?php } ?>

<div class="row">
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->firstname : ''); ?>
        <?php $attrs = (isset($member) ? [] : ['autofocus' => true]); ?>
        <?php echo render_input('firstname', 'staff_add_edit_firstname', $value, 'text', $attrs); ?>
    </div>
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->lastname : ''); ?>
        <?php echo render_input('lastname', 'staff_add_edit_lastname', $value); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->email : ''); ?>
        <?php echo render_input('email', 'staff_add_edit_email', $value, 'email', ['autocomplete' => 'off']); ?>
    </div>
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->phonenumber : ''); ?>
        <?php echo render_input('phonenumber', 'staff_add_edit_phonenumber', $value); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->facebook : ''); ?>
        <?php echo render_input('facebook', 'staff_add_edit_facebook', $value); ?>
    </div>
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->linkedin : ''); ?>
        <?php echo render_input('linkedin', 'staff_add_edit_linkedin', $value); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->skype : ''); ?>
        <?php echo render_input('skype', 'staff_add_edit_skype', $value); ?>
    </div>
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->website : ''); ?>
        <?php echo render_input('website', 'staff_website', $value); ?>
    </div>
</div>

<?php if ((isset($member) && $member->profile_image == null) || !isset($member)) { ?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="profile_image" class="control-label"><?php echo _l('staff_edit_profile_image'); ?></label>
            <input type="file" name="profile_image" class="form-control" id="profile_image" accept="image/*">
        </div>
    </div>
</div>
<?php } ?>

<?php if (isset($member) && $member->profile_image != null) { ?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php echo staff_profile_image($member->staffid, ['img', 'img-responsive', 'staff-profile-image-thumb'], 'thumb'); ?>
        </div>
    </div>
    <div class="col-md-6">
        <a href="<?php echo admin_url('hr/remove_staff_profile_image/' . $member->staffid); ?>" class="btn btn-danger mtop25"><i class="fa fa-remove"></i> <?php echo _l('delete'); ?></a>
    </div>
</div>
<?php } ?>

<div class="row">
    <div class="col-md-12">
        <hr />
        <?php if (is_admin()) { ?>
        <div class="checkbox checkbox-primary">
            <?php
            $isadmin = '';
            if (isset($member) && $member->admin == 1) {
                $isadmin = ' checked';
            }
            ?>
            <input type="checkbox" name="administrator" id="administrator" <?php echo e($isadmin); ?>>
            <label for="administrator"><?php echo _l('staff_add_edit_administrator'); ?></label>
        </div>
        <?php } ?>
        <div class="checkbox checkbox-primary">
            <?php
            $active = '';
            if (isset($member) && $member->active == 1) {
                $active = ' checked';
            }
            ?>
            <input type="checkbox" name="active" id="active" <?php echo e($active); ?>>
            <label for="active"><?php echo _l('staff_add_edit_active'); ?></label>
        </div>
    </div>
</div>

<?php if (!isset($member)) { ?>
<div class="row">
    <div class="col-md-12">
        <hr />
        <label for="password" class="control-label"><?php echo _l('staff_add_edit_password'); ?></label>
        <div class="input-group">
            <input type="password" class="form-control password" name="password" autocomplete="off">
            <span class="input-group-addon">
                <a href="#" class="show_password" onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
            </span>
            <span class="input-group-addon">
                <a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
            </span>
        </div>
    </div>
</div>
<?php } ?>
