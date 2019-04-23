<?php

/**
 * @Project NUKEVIET 4.x
 * @Author TDFOSS.,LTD (contact@tdfoss.vn)
 * @Copyright (C) 2018 TDFOSS.,LTD. All rights reserved
 * @Createdate Sat, 13 Jan 2018 13:35:09 GMT
 */
if (!defined('NV_IS_FILE_SITEINFO')) die('Stop!!!');

$lang_siteinfo = nv_get_lang_module($mod);
require_once NV_ROOTDIR . '/modules/task/site.functions.php';

// task chưa bắt đầu
$number = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . ' t1 INNER JOIN ' . NV_PREFIXLANG . '_' . $mod_data . '_follow t2 ON t1.id=t2.taskid WHERE t1.status = 0 AND t1.begintime > 0 AND t1.begintime <= ' . NV_CURRENTTIME . ' AND userid=' . $user_info['userid'] . ' AND ' . $user_info['userid'] . ' IN (t1.performer)')->fetchColumn();
if ($number > 0) {
    $siteinfo[] = array(
        'key' => $lang_siteinfo['siteinfo_status_o'],
        'value' => $number,
        'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $mod . '&amp;type=1&amp;userid=' . $user_info['userid']
    );
}

// task quá hạn
$number = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . ' t1 INNER JOIN ' . NV_PREFIXLANG . '_' . $mod_data . '_follow t2 ON t1.id=t2.taskid WHERE t1.status = 1 AND t1.exptime > 0 AND t1.exptime <= ' . NV_CURRENTTIME . ' AND userid=' . $user_info['userid'])->fetchColumn();
if ($number > 0) {
    $siteinfo[] = array(
        'key' => $lang_siteinfo['siteinfo_status_1'],
        'value' => $number,
        'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $mod . '&amp;type=2&amp;userid=' . $user_info['userid']
    );
}