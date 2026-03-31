<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-text-lg tw-mb-4"><?php echo _l('hr_attendance'); ?></h4>
                        
                        <!-- Check In/Out Widget -->
                        <div class="row tw-mb-6">
                            <div class="col-md-4">
                                <div class="panel panel-info">
                                    <div class="panel-heading text-center">
                                        <h4><?php echo date('d M Y'); ?></h4>
                                        <h2 id="clock-display" class="tw-font-bold tw-text-3xl"><?php echo date('H:i'); ?></h2>
                                    </div>
                                    <div class="panel-body text-center">
                                        <?php
                                        $staff_id = get_staff_user_id();
                                        $CI = &get_instance();
                                        $today = $CI->hr_model->get_today_attendance($staff_id);
                                        $on_break = false;
                                        $checked_in = $today && !$today->clock_out;
                                        
                                        if ($checked_in && $today) {
                                            $active_break = $CI->db->where('attendance_id', $today->id)
                                                                  ->where('break_end IS NULL', null)
                                                                  ->get(db_prefix() . 'hr_attendance_breaks')->row();
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
                            <div class="col-md-8">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4><?php echo _l('hr_today_attendance'); ?></h4>
                                    </div>
                                    <div class="panel-body">
                                        <?php if ($today): ?>
                                        <table class="table table-striped">
                                            <tr>
                                                <th><?php echo _l('hr_check_in'); ?>:</th>
                                                <td><?php echo $today->clock_in ? date('H:i', strtotime($today->clock_in)) : '-'; ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo _l('hr_check_out'); ?>:</th>
                                                <td><?php echo $today->clock_out ? date('H:i', strtotime($today->clock_out)) : '-'; ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo _l('hr_total_hours'); ?>:</th>
                                                <td><?php echo $today->total_hours ? number_format($today->total_hours, 2) : '-'; ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo _l('hr_status'); ?>:</th>
                                                <td><span class="badge badge-<?php echo $today->status == 'present' ? 'success' : ($today->status == 'late' ? 'warning' : 'secondary'); ?>"><?php echo _l('hr_' . $today->status); ?></span></td>
                                            </tr>
                                        </table>
                                        <?php else: ?>
                                        <p class="text-muted text-center"><?php echo _l('hr_no_records'); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Attendance Report Link -->
                        <div class="row">
                            <div class="col-md-12">
                                <a href="<?php echo admin_url('hr/attendance_report'); ?>" class="btn btn-default">
                                    <i class="fa fa-file-text"></i> <?php echo _l('hr_attendance_report'); ?>
                                </a>
                            </div>
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
