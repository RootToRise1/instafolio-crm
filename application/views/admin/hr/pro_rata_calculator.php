<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('hr_pro_rata_calculator'); ?></h4>
                        <p class="tw-text-muted tw-mb-4"><?php echo _l('hr_pro_rata_desc'); ?></p>
                        
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_employee'); ?></label>
                            <select name="staff_id" id="staff_id" class="form-control selectpicker" data-live-search="true">
                                <option value=""><?php echo _l('hr_select'); ?></option>
                                <?php foreach ($staff_members as $staff) { ?>
                                <option value="<?php echo $staff['staffid']; ?>"><?php echo e($staff['firstname'] . ' ' . $staff['lastname']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('hr_select_month'); ?></label>
                            <input type="month" name="month" id="month" class="form-control" value="<?php echo date('Y-m'); ?>">
                        </div>
                        
                        <button type="button" class="btn btn-primary" onclick="calculateProRata()">
                            <i class="fa fa-calculator tw-mr-1"></i>
                            <?php echo _l('hr_calculate_pro_rata'); ?>
                        </button>
                        
                        <div id="pro_rata_result" class="tw-mt-6 tw-hidden">
                            <div class="tw-bg-gray-50 tw-p-4 tw-rounded">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="tw-text-muted tw-text-sm"><?php echo _l('hr_joined_date'); ?></p>
                                        <p class="tw-font-semibold" id="joined_date">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="tw-text-muted tw-text-sm"><?php echo _l('hr_working_days'); ?></p>
                                        <p class="tw-font-semibold" id="working_days">-</p>
                                    </div>
                                </div>
                                <div class="row tw-mt-4">
                                    <div class="col-md-6">
                                        <p class="tw-text-muted tw-text-sm"><?php echo _l('hr_base_salary'); ?></p>
                                        <p class="tw-font-semibold" id="base_salary">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="tw-text-muted tw-text-sm"><?php echo _l('hr_prorated_salary'); ?></p>
                                        <p class="tw-font-semibold tw-text-success tw-text-xl" id="pro_rata_amount">-</p>
                                    </div>
                                </div>
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
function calculateProRata() {
    var staff_id = $('#staff_id').val();
    var month = $('#month').val();
    
    if (!staff_id || !month) {
        alert('Please select employee and month');
        return;
    }
    
    $.ajax({
        url: admin_url + 'hr/pro_rata_calculator',
        type: 'GET',
        data: { staff_id: staff_id, month: month },
        dataType: 'json',
        success: function(response) {
            if (response.pro_rata !== undefined) {
                $('#pro_rata_amount').text(formatMoney(response.pro_rata));
                $('#pro_rata_result').removeClass('tw-hidden');
            }
        }
    });
}

function formatMoney(amount) {
    return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}
</script>
