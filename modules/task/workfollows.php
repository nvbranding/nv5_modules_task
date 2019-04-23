<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Apr 20, 2010 10:47:41 AM
 */
if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$array_workfollow_field = array();
$result = $db->query('SHOW FULL COLUMNS FROM nv4_vi_task');
while ($item = $result->fetch()) {
    $array_workfollow_field[$item['field']] = !empty($item['comment']) ? $item['comment'] : $item['field'];
}