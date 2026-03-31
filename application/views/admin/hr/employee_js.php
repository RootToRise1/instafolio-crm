<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
$(function() {
    appValidateForm($('.staff-form'), {
        firstname: 'required',
        lastname: 'required',
        email: {
            required: true,
            email: true
        }
    });
});

function delete_employee(id) {
    if (confirm(app.lang.confirm_action_prompt)) {
        window.location.href = admin_url + 'hr/delete/' + id;
    }
}
</script>
