<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$pending = [];
try {
    $CI = &get_instance();
    if (method_exists($CI, 'load')) {
        $CI->load->model('hr/hr_model');
        if (method_exists($CI->hr_model, 'get_pending_leave_requests')) {
            $pending = $CI->hr_model->get_pending_leave_requests();
        }
    }
} catch (Exception $e) {
    $pending = [];
}
?>
<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('hr_pending_leave_requests'); ?>">
    <div class="panel_s">
        <div class="widget-dragger"></div>
        <div class="panel-body p-0">
            <div class="hr-leaves">
                <div class="hr-leaves-header">
                    <div class="hr-leaves-title">
                        <i class="fa fa-calendar-check-o"></i>
                        <span><?php echo _l('hr_pending_leaves'); ?></span>
                    </div>
                    <?php if (!empty($pending)) { ?>
                        <span class="hr-leaves-count"><?php echo count($pending); ?></span>
                    <?php } ?>
                </div>
                
                <div class="hr-leaves-body">
                    <?php if (!empty($pending) && count($pending) > 0) { ?>
                        <div class="hr-leaves-list">
                            <?php foreach (array_slice($pending, 0, 3) as $request) { ?>
                                <div class="hr-leaves-item">
                                    <div class="hr-leaves-user">
                                        <div class="hr-leaves-avatar">
                                            <?php echo substr($request['firstname'] ?? 'U', 0, 1) . substr($request['lastname'] ?? 'U', 0, 1); ?>
                                        </div>
                                        <div class="hr-leaves-user-info">
                                            <span class="hr-leaves-name"><?php echo ($request['firstname'] ?? '') . ' ' . substr($request['lastname'] ?? '', 0, 1); ?>.</span>
                                            <span class="hr-leaves-type"><?php echo $request['leave_type_name'] ?? ''; ?></span>
                                        </div>
                                    </div>
                                    <div class="hr-leaves-actions">
                                        <span class="hr-leaves-date"><?php echo isset($request['start_date']) ? date('d M', strtotime($request['start_date'])) : ''; ?></span>
                                        <a href="<?php echo admin_url('hr/approve_leave/' . ($request['id'] ?? 0)); ?>" class="hr-leaves-approve" title="<?php echo _l('hr_approve'); ?>" onclick="return confirm('<?php echo _l('hr_approve_confirm'); ?>');">
                                            <i class="fa fa-check"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if (count($pending) > 3) { ?>
                            <a href="<?php echo admin_url('hr/leave?status=pending'); ?>" class="hr-leaves-more">
                                <?php echo _l('hr_view_all_pending'); ?> (<?php echo count($pending); ?>) <i class="fa fa-arrow-right"></i>
                            </a>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="hr-leaves-empty">
                            <i class="fa fa-check-circle"></i>
                            <span><?php echo _l('hr_no_pending'); ?></span>
                        </div>
                    <?php } ?>
                    <div class="hr-leaves-spacer"></div>
                </div>
                
                <div class="hr-leaves-footer">
                    <a href="<?php echo admin_url('hr/leave'); ?>" class="hr-leaves-link">
                        <i class="fa fa-calendar"></i>
                        <span><?php echo _l('hr_go_to_leave'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
