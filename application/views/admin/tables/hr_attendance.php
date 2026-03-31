<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'staff_id',
    'date',
    'checkin',
    'checkout',
    'late_minutes',
    'status',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'hr_attendance';

$join = [
    'LEFT JOIN ' . db_prefix() . 'staff as staff ON staff.staffid = ' . db_prefix() . 'hr_attendance.staff_id',
];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [], $join);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        
        if ($aColumns[$i] == 'staff_id') {
            $_data = e($aRow['firstname'] . ' ' . $aRow['lastname']);
        } elseif ($aColumns[$i] == 'date') {
            $_data = _d($aRow['date']);
        } elseif ($aColumns[$i] == 'checkin' || $aColumns[$i] == 'checkout') {
            $_data = $aRow[$aColumns[$i]] ? date('H:i', strtotime($aRow[$aColumns[$i]])) : '-';
        } elseif ($aColumns[$i] == 'late_minutes') {
            $_data = $aRow['late_minutes'] > 0 ? $aRow['late_minutes'] . ' min' : '-';
        } elseif ($aColumns[$i] == 'status') {
            $badge_class = 'success';
            if ($aRow['status'] == 'late') $badge_class = 'warning';
            elseif ($aRow['status'] == 'absent') $badge_class = 'danger';
            $_data = '<span class="badge bg-' . $badge_class . '">' . _l('hr_' . $aRow['status']) . '</span>';
        }
        
        $row[] = $_data;
    }

    $row[] = '';

    $output['aaData'][] = $row;
}