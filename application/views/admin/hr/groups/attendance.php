<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (count($employee_attendance) > 0) { ?>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php echo _l('hr_date'); ?></th>
                <th><?php echo _l('hr_check_in'); ?></th>
                <th><?php echo _l('hr_check_out'); ?></th>
                <th><?php echo _l('hr_total_hours'); ?></th>
                <th><?php echo _l('hr_status'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employee_attendance as $record) { ?>
            <tr>
                <td><?php echo _d($record['date']); ?></td>
                <td><?php echo $record['clock_in'] ? substr($record['clock_in'], 11, 5) : '-'; ?></td>
                <td><?php echo $record['clock_out'] ? substr($record['clock_out'], 11, 5) : '-'; ?></td>
                <td>
                    <?php 
                    if ($record['clock_in'] && $record['clock_out']) {
                        $in = new DateTime($record['clock_in']);
                        $out = new DateTime($record['clock_out']);
                        if ($out < $in) {
                            $out->modify('+1 day');
                        }
                        $diff = $in->diff($out);
                        $hours = $diff->h + ($diff->i / 60);
                        echo number_format($hours, 2);
                    } elseif ($record['total_hours']) {
                        echo number_format($record['total_hours'], 2);
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    $status_class = 'label-default';
                    if ($record['status'] == 'present') {
                        $status_class = 'label-success';
                    } elseif ($record['status'] == 'late') {
                        $status_class = 'label-warning';
                    } elseif ($record['status'] == 'absent') {
                        $status_class = 'label-danger';
                    }
                    ?>
                    <span class="label <?php echo $status_class; ?>">
                        <?php echo _l('hr_status_' . $record['status']); ?>
                    </span>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } else { ?>
<div class="alert alert-info">
    <?php echo _l('hr_no_attendance_records'); ?>
</div>
<?php } ?>
