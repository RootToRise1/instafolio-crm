<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (isset($member)) { ?>
<?php $documents = $hr_documents ?: []; ?>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            <?php echo _l('hr_documents_info'); ?>
        </div>
    </div>
</div>

<?php if (count($documents) > 0) { ?>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo _l('hr_document_type'); ?></th>
                        <th><?php echo _l('hr_document_name'); ?></th>
                        <th><?php echo _l('hr_expiry_date'); ?></th>
                        <th><?php echo _l('hr_verified'); ?></th>
                        <th><?php echo _l('hr_uploaded'); ?></th>
                        <th><?php echo _l('options'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($documents as $doc) { ?>
                    <tr>
                        <td><span class="label label-info"><?php echo e($doc['document_type']); ?></span></td>
                        <td><?php echo e($doc['document_name']); ?></td>
                        <td>
                            <?php 
                            if($doc['expiry_date']) {
                                $expiry = new DateTime($doc['expiry_date']);
                                $now = new DateTime();
                                $is_expired = $expiry < $now;
                                $class = $is_expired ? 'text-danger' : '';
                            ?>
                            <span class="<?php echo $class; ?>"><?php echo _d($doc['expiry_date']); ?></span>
                            <?php if($is_expired) { ?>
                            <span class="label label-danger"><?php echo _l('hr_expired'); ?></span>
                            <?php } ?>
                            <?php } else { ?>
                            -
                            <?php } ?>
                        </td>
                        <td>
                            <?php if($doc['verified']) { ?>
                            <span class="label label-success"><i class="fa fa-check"></i> <?php echo _l('yes'); ?></span>
                            <?php } else { ?>
                            <span class="label label-warning"><?php echo _l('no'); ?></span>
                            <?php } ?>
                        </td>
                        <td><?php echo _dt($doc['created_at']); ?></td>
                        <td>
                            <?php if($doc['file_path']) { ?>
                            <a href="<?php echo site_url($doc['file_path']); ?>" target="_blank" class="btn btn-info btn-xs">
                                <i class="fa fa-download"></i>
                            </a>
                            <?php } ?>
                            <a href="<?php echo admin_url('hr/delete_document/' . $doc['id']); ?>" class="btn btn-danger btn-xs _delete">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } else { ?>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning">
            <?php echo _l('hr_no_documents'); ?>
        </div>
    </div>
</div>
<?php } ?>

<div class="row">
    <div class="col-md-12">
        <h4 class="tw-font-semibold tw-mt-4 tw-mb-3"><?php echo _l('hr_upload_document'); ?></h4>
    </div>
</div>

<?php echo form_open(admin_url('hr/add_document/' . $member->staffid), ['enctype' => 'multipart/form-data']); ?>
<div class="row">
    <div class="col-md-6">
        <?php
        $doc_types = [
            ['id' => 'ID Card', 'name' => 'ID Card'],
            ['id' => 'Passport', 'name' => 'Passport'],
            ['id' => 'Driving License', 'name' => 'Driving License'],
            ['id' => 'Education Certificate', 'name' => 'Education Certificate'],
            ['id' => 'Experience Letter', 'name' => 'Experience Letter'],
            ['id' => 'Contract', 'name' => 'Contract'],
            ['id' => 'NDA', 'name' => 'NDA'],
            ['id' => 'Offer Letter', 'name' => 'Offer Letter'],
            ['id' => 'Other', 'name' => 'Other'],
        ];
        echo render_select('document_type', $doc_types, ['id', 'name'], 'hr_document_type', '', [], [], true);
        ?>
    </div>
    <div class="col-md-6">
        <?php echo render_input('document_name', 'hr_document_name', '', 'text', ['required' => 'required']); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php echo render_date_input('expiry_date', 'hr_expiry_date', ''); ?>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="document_file" class="control-label"><?php echo _l('hr_upload_file'); ?></label>
            <input type="file" name="document_file" class="form-control" id="document_file">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-upload"></i> <?php echo _l('hr_upload_document'); ?>
        </button>
    </div>
</div>
<?php echo form_close(); ?>

<?php } else { ?>
<div class="alert alert-warning">
    <?php echo _l('hr_save_employee_first'); ?>
</div>
<?php } ?>
