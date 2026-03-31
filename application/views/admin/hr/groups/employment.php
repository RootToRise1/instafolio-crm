<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-6">
        <?php echo render_select('department_id', $departments, ['id', 'name'], 'hr_department', isset($member) ? $member->department_id : ''); ?>
    </div>
    <div class="col-md-6">
        <?php echo render_select('designation_id', $designations, ['id', 'name'], 'hr_designation', isset($member) ? $member->designation_id : ''); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php echo render_select('hr_role_id', $roles, ['id', 'name'], 'hr_role', isset($member) ? $member->hr_role_id : ''); ?>
    </div>
    <div class="col-md-6">
        <?php echo render_select('hr_shift_id', $shifts, ['id', 'name'], 'hr_shifts', isset($member) ? $member->hr_shift_id : ''); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->date_of_joining : ''); ?>
        <?php echo render_date_input('date_of_joining', 'hr_date_of_joining', $value); ?>
    </div>
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->employment_type : ''); ?>
        <?php
        $employment_types = [
            ['id' => 'full_time', 'name' => _l('hr_full_time')],
            ['id' => 'part_time', 'name' => _l('hr_part_time')],
            ['id' => 'contract', 'name' => _l('hr_contract')],
            ['id' => 'intern', 'name' => _l('hr_intern')],
            ['id' => 'temporary', 'name' => _l('hr_temporary')],
        ];
        echo render_select('employment_type', $employment_types, ['id', 'name'], 'hr_employment_type', $value);
        ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php echo render_select('manager_id', $staff_members, ['staffid', ['firstname', 'lastname']], 'hr_manager', isset($member) ? $member->manager_id : ''); ?>
    </div>
    <div class="col-md-6">
        <?php if (isset($member)) { 
            $tenure = '';
            if ($member->date_of_joining) {
                $join = new DateTime($member->date_of_joining);
                $now = new DateTime();
                $diff = $join->diff($now);
                $tenure = $diff->y . ' years, ' . $diff->m . ' months';
            }
        ?>
        <div class="form-group">
            <label class="control-label"><?php echo _l('hr_tenure'); ?></label>
            <input type="text" class="form-control" value="<?php echo $tenure ?: '-'; ?>" disabled>
        </div>
        <?php } ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4 class="tw-font-semibold tw-mt-4 tw-mb-3"><?php echo _l('hr_probation_details'); ?></h4>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->probation_end_date : ''); ?>
        <?php echo render_date_input('probation_end_date', 'hr_probation_end_date', $value); ?>
    </div>
    <div class="col-md-6">
        <div class="form-group select-placeholder">
            <label for="probation_status" class="control-label"><?php echo _l('hr_probation_status'); ?></label>
            <select name="probation_status" id="probation_status" class="selectpicker" data-width="100%">
                <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                <option value="active" <?php echo (isset($member) && $member->probation_status == 'active') ? 'selected' : ''; ?>><?php echo _l('hr_probation_active'); ?></option>
                <option value="completed" <?php echo (isset($member) && $member->probation_status == 'completed') ? 'selected' : ''; ?>><?php echo _l('hr_probation_completed'); ?></option>
                <option value="extended" <?php echo (isset($member) && $member->probation_status == 'extended') ? 'selected' : ''; ?>><?php echo _l('hr_probation_extended'); ?></option>
            </select>
        </div>
    </div>
</div>
