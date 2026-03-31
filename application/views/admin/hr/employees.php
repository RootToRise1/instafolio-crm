<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
/* Fix row-options visibility for HR Employee table */
table.table-hr-employees .row-options {
    position: static !important;
    left: auto !important;
    opacity: 1 !important;
    visibility: visible !important;
    padding: 2px 0;
    font-size: 12px;
    color: #0073aa;
}
table.table-hr-employees .row-options a {
    color: #0073aa;
}
table.table-hr-employees .row-options a:hover {
    color: #005a87;
    text-decoration: underline;
}
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (staff_can('create', 'hr') || is_admin()) { ?>
                <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="<?php echo admin_url('hr/employee'); ?>" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('hr_add_employee'); ?>
                    </a>
                </div>
                <?php } ?>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php
                        $table_data = [
                            _l('staff_dt_name'),
                            _l('staff_dt_email'),
                            _l('hr_department'),
                            _l('hr_designation'),
                            _l('hr_role'),
                            _l('staff_dt_active'),
                        ];
                        render_datatable($table_data, 'hr-employees');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delete_employee_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('hr/delete'), ['id' => 'delete_employee_form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('delete_staff'); ?></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="delete_employee_id" value="">
                <p><?php echo _l('delete_staff_info'); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-danger _delete"><?php echo _l('confirm'); ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    initDataTable('.table-hr-employees', window.location.href);
});

function delete_employee(id) {
    $('#delete_employee_modal').modal('show');
    $('#delete_employee_id').val(id);
}
</script>
</body>

</html>
