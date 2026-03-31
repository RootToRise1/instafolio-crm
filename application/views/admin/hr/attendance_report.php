<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('hr_attendance_report'); ?></h4>
                        <?php echo form_open(admin_url('hr/attendance_report'), ['method' => 'GET']); ?>
                        <div class="tw-flex tw-items-end tw-gap-4">
                            <?php echo render_date_input('start_date', _l('hr_start_date'), ['value' => $start_date]); ?>
                            <?php echo render_date_input('end_date', _l('hr_end_date'), ['value' => $end_date]); ?>
                            <?php echo render_select('department_id', $departments, ['departmentid', 'name'], _l('hr_department'), $department_id); ?>
                            <button type="submit" class="btn btn-primary"><?php echo _l('filter'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
                <div class="panel_s tw-mt-4">
                    <div class="panel-body">
                        <?php if (!empty($report)) { ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo _l('staff_dt_name'); ?></th>
                                    <th><?php echo _l('hr_department'); ?></th>
                                    <th><?php echo _l('hr_present'); ?></th>
                                    <th><?php echo _l('hr_absent'); ?></th>
                                    <th><?php echo _l('hr_late'); ?></th>
                                    <th><?php echo _l('hr_total_hours'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report as $row) { ?>
                                <tr>
                                    <td><?php echo $row['staff_name']; ?></td>
                                    <td><?php echo $row['department']; ?></td>
                                    <td><?php echo $row['present']; ?></td>
                                    <td><?php echo $row['absent']; ?></td>
                                    <td><?php echo $row['late']; ?></td>
                                    <td><?php echo number_format($row['total_hours'], 1); ?></td>
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
