<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php if (isset($member)) { ?>
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo e($member->firstname . ' ' . $member->lastname); ?>
                    <small class="text-muted">#<?php echo $member->staffid; ?></small>
                </h4>
            </div>
        </div>
        <?php } ?>
        
        <?php echo form_open_multipart($this->uri->uri_string(), ['class' => 'staff-form', 'autocomplete' => 'off']); ?>
        
        <div class="row">
            <div class="col-md-12" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (isset($member)) { ?>
                        <div class="horizontal-scrollable-tabs panel-full-width-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="<?php echo (!$this->input->get('group') || $this->input->get('group') == 'profile') ? 'active' : ''; ?>">
                                        <a href="#tab_profile" aria-controls="tab_profile" role="tab" data-toggle="tab">
                                            <i class="fa fa-user-circle menu-icon"></i>
                                            <?php echo _l('staff_profile_string'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation" class="<?php echo $this->input->get('group') == 'employment' ? 'active' : ''; ?>">
                                        <a href="#tab_employment" aria-controls="tab_employment" role="tab" data-toggle="tab">
                                            <i class="fa fa-briefcase menu-icon"></i>
                                            <?php echo _l('hr_employment_details'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation" class="<?php echo $this->input->get('group') == 'bank' ? 'active' : ''; ?>">
                                        <a href="#tab_bank" aria-controls="tab_bank" role="tab" data-toggle="tab">
                                            <i class="fa fa-university menu-icon"></i>
                                            <?php echo _l('hr_bank_details'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation" class="<?php echo $this->input->get('group') == 'emergency' ? 'active' : ''; ?>">
                                        <a href="#tab_emergency" aria-controls="tab_emergency" role="tab" data-toggle="tab">
                                            <i class="fa fa-phone menu-icon"></i>
                                            <?php echo _l('hr_emergency_contact'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation" class="<?php echo $this->input->get('group') == 'documents' ? 'active' : ''; ?>">
                                        <a href="#tab_documents" aria-controls="tab_documents" role="tab" data-toggle="tab">
                                            <i class="fa fa-file-text menu-icon"></i>
                                            <?php echo _l('hr_documents'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation" class="<?php echo $this->input->get('group') == 'salary' ? 'active' : ''; ?>">
                                        <a href="#tab_salary" aria-controls="tab_salary" role="tab" data-toggle="tab">
                                            <i class="fa fa-money menu-icon"></i>
                                            <?php echo _l('hr_salary'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation" class="<?php echo $this->input->get('group') == 'attendance' ? 'active' : ''; ?>">
                                        <a href="#tab_attendance" aria-controls="tab_attendance" role="tab" data-toggle="tab">
                                            <i class="fa fa-clock-o menu-icon"></i>
                                            <?php echo _l('hr_attendance'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation" class="<?php echo $this->input->get('group') == 'leave' ? 'active' : ''; ?>">
                                        <a href="#tab_leave" aria-controls="tab_leave" role="tab" data-toggle="tab">
                                            <i class="fa fa-calendar menu-icon"></i>
                                            <?php echo _l('hr_leave'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <div class="tab-content tw-mt-5">
                            <div role="tabpanel" class="tab-pane <?php echo (!$this->input->get('group') || $this->input->get('group') == 'profile') ? 'active' : ''; ?>" id="tab_profile">
                                <?php $this->load->view('admin/hr/groups/profile_form'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane <?php echo $this->input->get('group') == 'employment' ? 'active' : ''; ?>" id="tab_employment">
                                <?php $this->load->view('admin/hr/groups/employment'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane <?php echo $this->input->get('group') == 'bank' ? 'active' : ''; ?>" id="tab_bank">
                                <?php $this->load->view('admin/hr/groups/bank_details'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane <?php echo $this->input->get('group') == 'emergency' ? 'active' : ''; ?>" id="tab_emergency">
                                <?php $this->load->view('admin/hr/groups/emergency_contact'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane <?php echo $this->input->get('group') == 'documents' ? 'active' : ''; ?>" id="tab_documents">
                                <?php $this->load->view('admin/hr/groups/documents'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane <?php echo $this->input->get('group') == 'salary' ? 'active' : ''; ?>" id="tab_salary">
                                <?php $this->load->view('admin/hr/groups/salary'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane <?php echo $this->input->get('group') == 'attendance' ? 'active' : ''; ?>" id="tab_attendance">
                                <?php $this->load->view('admin/hr/groups/attendance'); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane <?php echo $this->input->get('group') == 'leave' ? 'active' : ''; ?>" id="tab_leave">
                                <?php $this->load->view('admin/hr/groups/leave'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="btn-bottom-toolbar text-right">
            <?php if (!isset($member) || $this->input->get('group') == 'profile' || $this->input->get('group') == 'employment') { ?>
            <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            <?php } ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/hr/employee_js'); ?>
</body>
</html>
