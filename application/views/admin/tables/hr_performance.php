<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'staff_id',
    'reviewer_id',
    'review_period',
    'review_date',
    'rating',
    'status',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'hr_performance_reviews';

$join = [
    'LEFT JOIN ' . db_prefix() . 'staff as staff ON staff.staffid = ' . db_prefix() . 'hr_performance_reviews.staff_id',
    'LEFT JOIN ' . db_prefix() . 'staff as reviewer ON reviewer.staffid = ' . db_prefix() . 'hr_performance_reviews.reviewer_id',
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
        } elseif ($aColumns[$i] == 'reviewer_id') {
            $_data = e($aRow['reviewer_firstname'] . ' ' . $aRow['reviewer_lastname']);
        } elseif ($aColumns[$i] == 'review_date') {
            $_data = _d($aRow['review_date']);
        } elseif ($aColumns[$i] == 'rating') {
            $_data = $aRow['rating'] ? number_format($aRow['rating'], 1) . '/5' : '-';
        } elseif ($aColumns[$i] == 'status') {
            $badge_class = 'info';
            if ($aRow['status'] == 'completed') $badge_class = 'success';
            elseif ($aRow['status'] == 'cancelled') $badge_class = 'secondary';
            $_data = '<span class="badge bg-' . $badge_class . '">' . _l('hr_review_' . $aRow['status']) . '</span>';
        }
        
        $row[] = $_data;
    }

    $row[] = '';

    $output['aaData'][] = $row;
}