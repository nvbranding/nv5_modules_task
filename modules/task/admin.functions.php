<?php

/**
 * @Project NUKEVIET 4.x
 * @Author TDFOSS.,LTD (contact@tdfoss.vn)
 * @Copyright (C) 2018 TDFOSS.,LTD. All rights reserved
 * @Createdate Sat, 13 Jan 2018 13:35:09 GMT
 */
if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) die('Stop!!!');

define('NV_IS_FILE_ADMIN', true);

$allow_func = array(
    'main',
    'econtent',
    'config',
    'econtent',
    'fields'
);

$array_config = $module_config[$module_name];

$array_task_status = array(
    0 => $lang_module['task_status_0'],
    1 => $lang_module['task_status_1'],
    2 => $lang_module['task_status_2'],
    3 => $lang_module['task_status_3']
);
$array_allow_useradd = array(
    1 => ''
);