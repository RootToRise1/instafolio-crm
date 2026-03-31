<?php

// Version 2.3.0 and above
hooks()->add_filter('before_get_task_statuses','my_add_custom_task_status');

// Prior to version 2.3.0
// Uncomment the code below and remove the code above if you are using version older then 2.3.0
// add_action('before_get_task_statuses','my_add_custom_task_status');


function my_add_custom_task_status($current_statuses){
    // Push new status to the current statuses
    $current_statuses[] = array(
           'id'=>55, // new status with id 50
           'color'=>'#ac208d',
           'name'=>'Internal Task',
           'order'=>15,
           'filter_default'=>true, // true or false

        );
    // Push another status (delete this code if you need to add only 1 status)
    $current_statuses[] = array(
          'id'=>51, //new status with new id 51
          'color'=>'#03aeee',
          'name'=>'Cross Check',
          'order'=>11,
          'filter_default'=>true // true or false
        );

    // Return the statuses
    return $current_statuses;
}

hooks()->add_filter('before_get_project_statuses','my_add_custom_project_status');

function my_add_custom_project_status($current_statuses){
    // Push new status to the current statuses
    $current_statuses[] = array(
           'id'=>65, // new status with id 50
           'color'=>'#ec168e',
           'name'=>'Discussion Phase',
           'order'=>10,
           'filter_default'=>false, // true or false
        );
    // Return the statuses
    return $current_statuses;
}