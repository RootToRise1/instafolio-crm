<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (count($employee_leave) > 0) { ?>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php echo _l('hr_leave_type'); ?></th>
                <th><?php echo _l('hr_start_date'); ?></th>
                <th><?php echo _l('hr_end_date'); ?></th>
                <th><?php echo _l('hr_days'); ?></th>
                <th><?php echo _l('hr_status'); ?></th>
                <th><?php echo _l('hr_reason'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employee_leave as $record) { ?>
            <tr>
                <td>
                    <?php 
                    foreach ($leave_types as $type) {
                        if ($type['id'] == $record['leave_type_id']) {
                            echo e($type['name']);
                            break;
                        }
                    }
                    ?>
                </td>
                <td><?php echo _d($record['start_date']); ?></td>
                <td><?php echo _d($record['end_date']); ?></td>
                <td><?php echo $record['days']; ?></td>
                <td>
                    <?php
                    $status_class = 'label-default';
                    if ($record['status'] == 'approved') {
                        $status_class = 'label-success';
                    } elseif ($record['status'] == 'pending') {
                        $status_class = 'label-warning';
                    } elseif ($record['status'] == 'rejected') {
                        $status_class = 'label-danger';
                    }
                    ?>
                    <span class="label <?php echo $status_class; ?>">
                        <?php echo _l('hr_status_' . $record['status']); ?>
                    </span>
                </td>
                <td>
                    <?php echo $record['reason'] ? e($record['reason']) : '-'; ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } else { ?>
<div class="alert alert-info">
    <?php echo _l('hr_no_leave_records'); ?>
</div>
<?php } ?>
