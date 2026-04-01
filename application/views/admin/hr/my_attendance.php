<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.attendance-container {
    max-width: 1200px;
    margin: 0 auto;
}

.attendance-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.attendance-header h2 {
    margin: 0;
    font-weight: 600;
}

.attendance-header .date-display {
    font-size: 14px;
    opacity: 0.9;
}

.clock-display {
    font-size: 48px;
    font-weight: 700;
    font-family: 'Segoe UI', monospace;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.status-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 30px;
    text-align: center;
}

.status-card .status-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.status-card .status-text {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
}

.status-card.not-checked-in .status-icon { color: #94a3b8; }
.status-card.working .status-icon { color: #10b981; }
.status-card.on-break .status-icon { color: #f59e0b; }

.btn-attendance {
    padding: 15px 40px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.btn-attendance:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-clock-in { background: #10b981; color: white; }
.btn-clock-in:hover { background: #059669; }

.btn-clock-out { background: #ef4444; color: white; }
.btn-clock-out:hover { background: #dc2626; }

.btn-break-in { background: #3b82f6; color: white; }
.btn-break-in:hover { background: #2563eb; }

.btn-break-out { background: #f59e0b; color: white; }
.btn-break-out:hover { background: #d97706; }

.btn-group-attendance {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.current-session {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.current-session h4 {
    margin: 0 0 15px 0;
    color: #475569;
}

.session-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.session-info-item {
    background: white;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.session-info-item .label {
    font-size: 12px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.session-info-item .value {
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
    margin-top: 5px;
}

.attendance-history {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.attendance-history h3 {
    margin: 0 0 20px 0;
    color: #1e293b;
}

.table-attendance {
    width: 100%;
    border-collapse: collapse;
}

.table-attendance th {
    text-align: left;
    padding: 12px;
    background: #f8fafc;
    color: #64748b;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-attendance td {
    padding: 15px 12px;
    border-bottom: 1px solid #e2e8f0;
    color: #475569;
}

.table-attendance tr:hover {
    background: #f8fafc;
}

.table-attendance tr:last-child td {
    border-bottom: none;
}

.badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-present { background: #d1fae5; color: #059669; }
.badge-late { background: #fef3c7; color: #d97706; }
.badge-absent { background: #fee2e2; color: #dc2626; }
.badge-on_leave { background: #e0e7ff; color: #4f46e5; }

.no-records {
    text-align: center;
    padding: 40px;
    color: #94a3b8;
}

.no-records i {
    font-size: 48px;
    margin-bottom: 15px;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="attendance-container">
            <!-- Header -->
            <div class="attendance-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2><i class="fa fa-clock-o"></i> My Attendance</h2>
                        <div class="date-display"><?php echo date('l, F j, Y'); ?></div>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <div class="clock-display" id="live-clock"><?php echo date('H:i'); ?></div>
                    </div>
                </div>
            </div>

            <?php
            $today = isset($today_attendance) ? $today_attendance : null;
            $checked_in = $today && !$today->clock_out;
            $on_break = false;
            
            if ($checked_in && $today) {
                $CI = &get_instance();
                $CI->db->where('attendance_id', $today->id);
                $CI->db->where('break_end IS NULL', null);
                $active_break = $CI->db->get(db_prefix() . 'hr_attendance_breaks')->row();
                $on_break = $active_break ? true : false;
            }
            
            $status_class = !$checked_in ? 'not-checked-in' : ($on_break ? 'on-break' : 'working');
            $status_icon = !$checked_in ? 'fa-moon-o' : ($on_break ? 'fa-coffee' : 'fa-check-circle');
            $status_text = !$checked_in ? 'Not Checked In' : ($on_break ? 'On Break' : 'Working');
            ?>

            <!-- Status Card -->
            <div class="status-card <?php echo $status_class; ?>">
                <div class="status-icon">
                    <i class="fa <?php echo $status_icon; ?>"></i>
                </div>
                <div class="status-text"><?php echo $status_text; ?></div>
                
                <div class="btn-group-attendance">
                    <?php if (!$checked_in): ?>
                        <a href="<?php echo admin_url('hr/clock_in'); ?>" class="btn-attendance btn-clock-in">
                            <i class="fa fa-sign-in"></i> Check In
                        </a>
                    <?php else: ?>
                        <?php if ($on_break): ?>
                            <a href="<?php echo admin_url('hr/break_out'); ?>" class="btn-attendance btn-break-out">
                                <i class="fa fa-play"></i> End Break
                            </a>
                        <?php else: ?>
                            <a href="<?php echo admin_url('hr/break_in'); ?>" class="btn-attendance btn-break-in">
                                <i class="fa fa-pause"></i> Take Break
                            </a>
                            <a href="<?php echo admin_url('hr/clock_out'); ?>" class="btn-attendance btn-clock-out">
                                <i class="fa fa-sign-out"></i> Check Out
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Current Session Info -->
            <?php if ($checked_in && $today): ?>
            <div class="current-session">
                <h4><i class="fa fa-info-circle"></i> Current Session</h4>
                <div class="session-info">
                    <div class="session-info-item">
                        <div class="label">Check In Time</div>
                        <div class="value"><?php echo date('H:i', strtotime($today->clock_in)); ?></div>
                    </div>
                    <div class="session-info-item">
                        <div class="label">Work Duration</div>
                        <div class="value" id="work-duration">Calculating...</div>
                    </div>
                    <div class="session-info-item">
                        <div class="label">Break Time</div>
                        <div class="value"><?php echo $today->break_minutes ? number_format($today->break_minutes, 0) . ' min' : '0 min'; ?></div>
                    </div>
                    <div class="session-info-item">
                        <div class="label">Status</div>
                        <div class="value"><span class="badge-status <?php echo $today->status == 'late' ? 'badge-late' : 'badge-present'; ?>"><?php echo ucfirst($today->status); ?></span></div>
                    </div>
                </div>
            </div>
            <script>
            function updateDuration() {
                var checkIn = new Date('<?php echo $today->clock_in; ?>');
                var now = new Date();
                var diff = Math.floor((now - checkIn) / 1000);
                
                var hours = Math.floor(diff / 3600);
                var minutes = Math.floor((diff % 3600) / 60);
                
                document.getElementById('work-duration').textContent = hours + 'h ' + minutes + 'm';
            }
            updateDuration();
            setInterval(updateDuration, 60000);
            </script>
            <?php endif; ?>

            <!-- Attendance History -->
            <div class="attendance-history">
                <h3><i class="fa fa-history"></i> Attendance History</h3>
                <?php if (isset($attendance) && is_array($attendance) && count($attendance) > 0): ?>
                <div class="table-responsive">
                    <table class="table-attendance">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Total Hours</th>
                                <th>Break Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendance as $row): ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                                <td><?php echo isset($row['clock_in']) && $row['clock_in'] ? date('H:i', strtotime($row['clock_in'])) : '-'; ?></td>
                                <td><?php echo isset($row['clock_out']) && $row['clock_out'] ? date('H:i', strtotime($row['clock_out'])) : '-'; ?></td>
                                <td><?php echo isset($row['total_hours']) && $row['total_hours'] ? number_format($row['total_hours'], 1) . ' hrs' : '-'; ?></td>
                                <td><?php echo isset($row['break_minutes']) && $row['break_minutes'] ? number_format($row['break_minutes'], 0) . ' min' : '-'; ?></td>
                                <td>
                                    <span class="badge-status badge-<?php echo $row['status'] ?? 'present'; ?>">
                                        <?php echo isset($row['status']) ? ucfirst($row['status']) : 'Present'; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="no-records">
                    <i class="fa fa-calendar-times-o"></i>
                    <p>No attendance records found</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function updateClock() {
    var now = new Date();
    var hours = String(now.getHours()).padStart(2, '0');
    var minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('live-clock').textContent = hours + ':' + minutes;
}
setInterval(updateClock, 1000);
</script>
<?php init_tail(); ?>