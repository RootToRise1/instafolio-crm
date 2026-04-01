<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$staff_id = get_staff_user_id();
$current_time = date('H:i');
$checked_in = false;
$on_break = false;
$clock_in_time = null;
$attendance_id = null;

try {
    $CI = &get_instance();
    if (method_exists($CI, 'load')) {
        $CI->load->model('hr/hr_model');
        if (method_exists($CI->hr_model, 'get_today_attendance')) {
            $attendance = $CI->hr_model->get_today_attendance($staff_id);
            if ($attendance) {
                $checked_in = empty($attendance->clock_out);
                $clock_in_time = $attendance->clock_in;
                $attendance_id = $attendance->id;
                
                // Check for active break
                if ($checked_in && $attendance_id) {
                    $CI->db->where('attendance_id', $attendance_id);
                    $CI->db->where('break_end IS NULL', null);
                    $active_break = $CI->db->get(db_prefix() . 'hr_attendance_breaks')->row();
                    $on_break = $active_break ? true : false;
                }
            }
        }
    }
} catch (Exception $e) {
    // Silently fail and show default state
}
?>
<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('hr_my_attendance'); ?>">
    <div class="panel_s">
        <div class="widget-dragger"></div>
        <div class="panel-body p-0">
            <div class="hr-attendance">
                <div class="hr-att-header">
                    <div class="hr-att-title">
                        <i class="fa fa-clock-o"></i>
                        <span><?php echo _l('hr_attendance'); ?></span>
                    </div>
                    <div class="hr-att-time" id="live-clock"><?php echo $current_time; ?></div>
                </div>
                
                <div class="hr-att-body">
                    <div class="hr-att-status">
                        <?php if ($checked_in) { ?>
                            <div class="hr-att-status-badge <?php echo $on_break ? 'break' : 'active'; ?>">
                                <i class="fa <?php echo $on_break ? 'fa-coffee' : 'fa-circle'; ?>"></i>
                                <span><?php echo $on_break ? _l('hr_on_break') : _l('hr_working'); ?></span>
                            </div>
                        <?php } else { ?>
                            <div class="hr-att-status-badge idle">
                                <i class="fa fa-moon-o"></i>
                                <span><?php echo _l('hr_not_checked_in'); ?></span>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <div class="hr-att-info">
                        <?php if ($checked_in) { ?>
                            <div class="hr-att-info-box">
                                <span class="hr-att-info-label"><?php echo _l('hr_check_in'); ?></span>
                                <span class="hr-att-info-value"><?php echo $clock_in_time ? date('H:i', strtotime($clock_in_time)) : '-'; ?></span>
                            </div>
                            <div class="hr-att-info-box">
                                <span class="hr-att-info-label"><?php echo _l('hr_worked'); ?></span>
                                <span class="hr-att-info-value" id="worked-hours" data-checkin="<?php echo $clock_in_time ?? ''; ?>">--</span>
                            </div>
                        <?php } else { ?>
                            <div class="hr-att-date"><?php echo date('l, d M Y'); ?></div>
                        <?php } ?>
                    </div>
                </div>
                
                <div class="hr-att-footer">
                    <?php if ($checked_in) { ?>
                        <?php if ($on_break) { ?>
                            <a href="<?php echo admin_url('hr/break_out'); ?>" class="hr-att-btn hr-att-btn-break-end btn-block">
                                <i class="fa fa-play"></i>
                                <span><?php echo _l('hr_end_break'); ?></span>
                            </a>
                        <?php } else { ?>
                            <a href="<?php echo admin_url('hr/break_in'); ?>" class="hr-att-btn hr-att-btn-break">
                                <i class="fa fa-pause"></i>
                                <span><?php echo _l('hr_break'); ?></span>
                            </a>
                            <a href="<?php echo admin_url('hr/clock_out'); ?>" class="hr-att-btn hr-att-btn-out">
                                <i class="fa fa-sign-out"></i>
                                <span><?php echo _l('hr_check_out'); ?></span>
                            </a>
                        <?php } ?>
                    <?php } else { ?>
                        <a href="<?php echo admin_url('hr/clock_in'); ?>" class="hr-att-btn hr-att-btn-in btn-block">
                            <i class="fa fa-sign-in"></i>
                            <span><?php echo _l('hr_check_in'); ?></span>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Bind click events properly
    $('.hr-att-btn-in, .hr-att-btn-out, .hr-att-btn-break, .hr-att-btn-break-end').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        if (!url || url === 'javascript:void(0)' || url === '#') {
            // Try to get URL from onclick
            var onclick = $(this).attr('onclick');
            if (onclick) {
                var match = onclick.match(/hr_attendance_action\('([^']+)'/);
                if (match && match[1]) {
                    url = match[1];
                }
            }
        }
        
        if (!url || url === 'javascript:void(0)' || url === '#') {
            alert('Error: No valid URL found');
            return;
        }
        
        var btn = $(this);
        var originalHtml = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        btn.prop('disabled', true);
        
        console.log('HR Attendance URL:', url);
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Success:', response);
                if (response.success) {
                    alert_float('success', response.message || 'Action completed successfully');
                } else {
                    alert_float('danger', response.message || 'An error occurred');
                }
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            },
            error: function(xhr, status, error) {
                console.log('Error:', status, error);
                window.location.href = '<?php echo admin_url("hr/my_attendance"); ?>';
            }
        });
    });
});
</script>
