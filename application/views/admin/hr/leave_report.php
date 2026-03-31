<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('hr_leave_utilization'); ?></h4>
                        <?php if (!empty($report)) { ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo _l('staff_dt_name'); ?></th>
                                    <th><?php echo _l('hr_leave_type'); ?></th>
                                    <th><?php echo _l('hr_allocated'); ?></th>
                                    <th><?php echo _l('hr_used'); ?></th>
                                    <th><?php echo _l('hr_pending'); ?></th>
                                    <th><?php echo _l('hr_available'); ?></th>
                                    <th><?php echo _l('hr_utilization'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report as $row) { ?>
                                <tr>
                                    <td><?php echo $row['staff_name']; ?></td>
                                    <td><?php echo $row['leave_type']; ?></td>
                                    <td><?php echo $row['allocated']; ?></td>
                                    <td><?php echo $row['used']; ?></td>
                                    <td><?php echo $row['pending']; ?></td>
                                    <td><?php echo $row['available']; ?></td>
                                    <td>
                                        <div class="tw-w-full tw-bg-gray-200 tw-rounded tw-h-2">
                                            <div class="tw-bg-primary tw-h-2 tw-rounded" style="width: <?php echo $row['utilization']; ?>%"></div>
                                        </div>
                                        <span class="tw-text-sm"><?php echo number_format($row['utilization'], 1); ?>%</span>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } else { ?>
                        <p class="tw-text-gray-500"><?php echo _l('hr_no_data_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
