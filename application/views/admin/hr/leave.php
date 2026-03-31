<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                            <h4 class="tw-font-semibold tw-text-lg"><?php echo _l('hr_leave_requests'); ?></h4>
                        </div>
                        <?php
                        $table_data = [
                            _l('hr_staff_name'),
                            _l('hr_leave_type'),
                            _l('hr_from_date'),
                            _l('hr_to_date'),
                            _l('hr_days'),
                            _l('hr_reason'),
                            _l('hr_status'),
                            _l('hr_applied_on'),
                        ];
                        render_datatable($table_data, 'hr_leave');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    initDataTable('.table-hr_leave', window.location.href);
});
</script>
