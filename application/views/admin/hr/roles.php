<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h4 class="tw-font-semibold"><?php echo _l('hr_roles_management'); ?></h4>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold tw-mb-4"><?php echo isset($role) ? _l('hr_edit_role') : _l('hr_add_role'); ?></h5>
                                <?php echo form_open(admin_url('hr/roles'), ['id' => 'role-form']); ?>
                                <?php if (isset($role)) { ?>
                                <input type="hidden" name="id" value="<?php echo $role['id']; ?>">
                                <?php } ?>
                                <?php echo render_input('name', 'hr_role_name', isset($role) ? $role['name'] : ''); ?>
                                
                                <div class="form-group">
                                    <label class="control-label"><?php echo _l('hr_permissions'); ?></label>
                                    <div class="checkbox">
                                        <input type="checkbox" name="permissions[view_hr]" id="view_hr" value="1" <?php echo (isset($permissions['view_hr']) && $permissions['view_hr']) ? 'checked' : ''; ?>>
                                        <label for="view_hr"><?php echo _l('hr_view_hr'); ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="permissions[edit_hr]" id="edit_hr" value="1" <?php echo (isset($permissions['edit_hr']) && $permissions['edit_hr']) ? 'checked' : ''; ?>>
                                        <label for="edit_hr"><?php echo _l('hr_edit_hr'); ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="permissions[delete_hr]" id="delete_hr" value="1" <?php echo (isset($permissions['delete_hr']) && $permissions['delete_hr']) ? 'checked' : ''; ?>>
                                        <label for="delete_hr"><?php echo _l('hr_delete_hr'); ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="permissions[approve_leave]" id="approve_leave" value="1" <?php echo (isset($permissions['approve_leave']) && $permissions['approve_leave']) ? 'checked' : ''; ?>>
                                        <label for="approve_leave"><?php echo _l('hr_approve_leave'); ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="permissions[view_payroll]" id="view_payroll" value="1" <?php echo (isset($permissions['view_payroll']) && $permissions['view_payroll']) ? 'checked' : ''; ?>>
                                        <label for="view_payroll"><?php echo _l('hr_view_payroll'); ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="permissions[manage_payroll]" id="manage_payroll" value="1" <?php echo (isset($permissions['manage_payroll']) && $permissions['manage_payroll']) ? 'checked' : ''; ?>>
                                        <label for="manage_payroll"><?php echo _l('hr_manage_payroll'); ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="permissions[view_reports]" id="view_reports" value="1" <?php echo (isset($permissions['view_reports']) && $permissions['view_reports']) ? 'checked' : ''; ?>>
                                        <label for="view_reports"><?php echo _l('hr_view_reports'); ?></label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="checkbox">
                                        <input type="checkbox" name="active" id="role_active" value="1" <?php echo (!isset($role) || (isset($role) && $role['active'])) ? 'checked' : ''; ?>>
                                        <label for="role_active"><?php echo _l('hr_active'); ?></label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                                <?php if (isset($role)) { ?>
                                <a href="<?php echo admin_url('hr/roles'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
                                <?php } ?>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="panel_s">
                            <div class="panel-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('hr_role_name'); ?></th>
                                            <th><?php echo _l('hr_permissions'); ?></th>
                                            <th><?php echo _l('hr_active'); ?></th>
                                            <th><?php echo _l('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($roles)) { ?>
                                        <?php foreach ($roles as $r) { 
                                            $perms = json_decode($r['permissions'], true) ?? [];
                                        ?>
                                        <tr>
                                            <td><strong><?php echo e($r['name']); ?></strong></td>
                                            <td>
                                                <?php
                                                $perm_labels = [];
                                                if (!empty($perms['view_hr'])) $perm_labels[] = _l('hr_view_hr');
                                                if (!empty($perms['edit_hr'])) $perm_labels[] = _l('hr_edit_hr');
                                                if (!empty($perms['approve_leave'])) $perm_labels[] = _l('hr_approve_leave');
                                                if (!empty($perms['view_payroll'])) $perm_labels[] = _l('hr_view_payroll');
                                                if (!empty($perms['manage_payroll'])) $perm_labels[] = _l('hr_manage_payroll');
                                                if (empty($perm_labels)) {
                                                    echo '<span class="text-muted">No permissions</span>';
                                                } else {
                                                    echo '<span class="badge bg-info">' . implode('</span> <span class="badge bg-info">', $perm_labels) . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($r['active']) { ?>
                                                <span class="badge bg-success"><?php echo _l('yes'); ?></span>
                                                <?php } else { ?>
                                                <span class="badge bg-secondary"><?php echo _l('no'); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo admin_url('hr/roles/' . $r['id']); ?>" class="btn btn-default btn-icon">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        <?php } else { ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted"><?php echo _l('hr_no_records'); ?></td>
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
</div>
<?php init_tail(); ?>
