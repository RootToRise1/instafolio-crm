<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'staff_id',
    'leave_type_id',
    'start_date',
    'end_date',
    'status',
    'created_at',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'hr_leave_requests';

$join = [
    'LEFT JOIN ' . db_prefix() . 'staff as staff ON staff.staffid = ' . db_prefix() . 'hr_leave_requests.staff_id',
    'LEFT JOIN ' . db_prefix() . 'hr_leave_types as lt ON lt.id = ' . db_prefix() . 'hr_leave_requests.leave_type_id',
];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [], $join);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    
    // Calculate days
    $days = '';
    if (!empty($aRow['start_date']) && !empty($aRow['end_date'])) {
        $start = new DateTime($aRow['start_date']);
        $end = new DateTime($aRow['end_date']);
        $diff = $start->diff($end);
        $days = $diff->days + 1;
    }
    
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        
        if ($aColumns[$i] == 'staff_id') {
            $_data = e($aRow['firstname'] . ' ' . $aRow['lastname']);
        } elseif ($aColumns[$i] == 'leave_type_id') {
            $_data = '<span class="badge" style="background-color:' . e($aRow['color'] ?? '#3788d8') . ';color:white;">' . e($aRow['name'] ?? 'Unknown') . '</span>';
        } elseif ($aColumns[$i] == 'start_date' || $aColumns[$i] == 'end_date') {
            $_data = _d($aRow[$aColumns[$i]]);
        } elseif ($aColumns[$i] == 'status') {
            $badge_class = 'warning';
            if ($aRow['status'] == 'approved') $badge_class = 'success';
            elseif ($aRow['status'] == 'rejected') $badge_class = 'danger';
            elseif ($aRow['status'] == 'cancelled') $badge_class = 'secondary';
            $_data = '<span class="badge bg-' . $badge_class . '">' . _l('hr_leave_' . $aRow['status']) . '</span>';
        } elseif ($aColumns[$i] == 'created_at') {
            $_data = _dt($aRow['created_at']);
        }
        
        $row[] = $_data;
    }
    
    // Add days column
    $row[] = $days;
    
    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';
    if ($aRow['status'] == 'pending' && staff_can('approve_leave', 'hr')) {
        $options .= '<a href="' . admin_url('hr/approve_leave/' . $aRow['id']) . '" class="tw-text-success hover:tw-text-success" title="' . _l('hr_approve_leave') . '">
            <i class="fa-regular fa-check-circle fa-lg"></i>
        </a>';
        $options .= '<a href="#" onclick="reject_leave(' . $aRow['id'] . ');return false;" class="tw-text-danger hover:tw-text-danger" title="' . _l('hr_reject_leave') . '">
            <i class="fa-regular fa-times-circle fa-lg"></i>
        </a>';
    }
    $options .= '</div>';

    $row[] = $options;

    $output['aaData'][] = $row;
}
