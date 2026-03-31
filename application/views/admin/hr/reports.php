<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('hr_hr_reports'); ?></h4>
                        <div class="tw-grid tw-grid-cols-2 tw-gap-6">
                            <a href="<?php echo admin_url('hr/reports?type=attendance'); ?>" class="tw-block tw-p-6 tw-bg-white tw-border tw-border-gray-200 tw-rounded-lg hover:tw-bg-gray-50">
                                <h5 class="tw-font-semibold tw-text-lg"><?php echo _l('hr_attendance_report'); ?></h5>
                                <p class="tw-text-gray-500 tw-mt-2"><?php echo _l('hr_attendance_report_desc'); ?></p>
                            </a>
                            <a href="<?php echo admin_url('hr/reports?type=leave'); ?>" class="tw-block tw-p-6 tw-bg-white tw-border tw-border-gray-200 tw-rounded-lg hover:tw-bg-gray-50">
                                <h5 class="tw-font-semibold tw-text-lg"><?php echo _l('hr_leave_utilization'); ?></h5>
                                <p class="tw-text-gray-500 tw-mt-2"><?php echo _l('hr_leave_utilization_desc'); ?></p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
