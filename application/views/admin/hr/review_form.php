<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo $title; ?></h4>
                        <?php echo form_open(admin_url('hr/add_review')); ?>
                        <?php echo render_select('staff_id', $staff_members, ['staffid', ['firstname', 'lastname']], _l('hr_employee')); ?>
                        <?php echo render_date_input('review_date', _l('hr_review_date')); ?>
                        <?php echo render_textarea('objectives', _l('hr_objectives'), '', ['rows' => 4]); ?>
                        <?php echo render_textarea('comments', _l('hr_comments'), '', ['rows' => 4]); ?>
                        <?php echo render_input('rating', _l('hr_rating'), 'number', '', ['min' => 1, 'max' => 5]); ?>
                        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                        <a href="<?php echo admin_url('hr/performance'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
