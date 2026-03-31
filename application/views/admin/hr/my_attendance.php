<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-text-lg tw-mb-4"><?php echo _l('hr_my_attendance'); ?></h4>
                        
                        <div class="row tw-mb-6">
                            <div class="col-md-4">
                                <div class="panel panel-info">
                                    <div class="panel-heading text-center">
                                        <h4><?php echo date('d M Y'); ?></h4>
                                        <h2 id="clock-display" class="tw-font-bold tw-text-3xl"><?php echo date('H:i'); ?></h2>
                                    </div>
                                    <div class="panel-body text-center">
                                        <?php
                                        $today = isset($today_attendance) ? $today_attendance : null;
                                        $on_break = false;
                                        $checked_in = $today && !$today->clock_out;
                                        
                                        if ($checked_in && $today) {
                                            $CI = &get_instance();
                                            $CI->db->where('attendance_id', $today->id);
                                            $CI->db->where('break_end IS NULL', null);
                                            $active_break = $CI->db->get(db_prefix() . 'hr_attendance_breaks')->row();
                                            $on_break = $active_break ? true : false;
                                        }
                                        ?>
                                        
                                        <?php if (!$checked_in): ?>
                                            <a href="<?php echo admin_url('hr/clock_in'); ?>" class="btn btn-success btn-lg">
                                                <i class="fa fa-sign-in"></i> <?php echo _l('hr_check_in'); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php if ($on_break): ?>
                                                <a href="<?php echo admin_url('hr/break_out'); ?>" class="btn btn-warning btn-lg">
                                                    <i class="fa fa-coffee"></i> <?php echo _l('hr_break_out'); ?>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo admin_url('hr/break_in'); ?>" class="btn btn-info">
                                                    <i class="fa fa-pause"></i> <?php echo _l('hr_break_in'); ?>
                                                </a>
                                                <a href="<?php echo admin_url('hr/clock_out'); ?>" class="btn btn-danger">
                                                    <i class="fa fa-sign-out"></i> <?php echo _l('hr_check_out'); ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h5><?php echo _l('hr_attendance'); ?></h5>
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
                                    <?php if (isset($attendance) && is_array($attendance) && count($attendance) > 0) { ?>
                                    <?php foreach ($attendance as $row) { ?>
                                    <tr>
                                        <td><?php echo _d($row['date']); ?></td>
                                        <td><?php echo isset($row['clock_in']) && $row['clock_in'] ? date('H:i', strtotime($row['clock_in'])) : '-'; ?></td>
                                        <td><?php echo isset($row['clock_out']) && $row['clock_out'] ? date('H:i', strtotime($row['clock_out'])) : '-'; ?></td>
                                        <td><?php echo isset($row['total_hours']) && $row['total_hours'] ? number_format($row['total_hours'], 2) . ' hrs' : '-'; ?></td>
                                        <td><span class="badge bg-<?php echo (isset($row['status']) && $row['status'] == 'present') ? 'success' : ((isset($row['status']) && $row['status'] == 'late') ? 'warning' : 'secondary'); ?>"><?php echo _l('hr_' . (isset($row['status']) ? $row['status'] : 'unknown')); ?></span></td>
                                    </tr>
                                    <?php } ?>
                                    <?php } else { ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted"><?php echo _l('hr_no_records'); ?></td>
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
<?php init_tail(); ?>
<script>
function updateClock() {
    var now = new Date();
    var hours = String(now.getHours()).padStart(2, '0');
    var minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('clock-display').textContent = hours + ':' + minutes;
}
setInterval(updateClock, 1000);
</script>
