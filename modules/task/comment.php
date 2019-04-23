<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 21 Jan 2014 01:32:02 GMT
 */
if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}
if (!isset($is_delete)) {
    require_once NV_ROOTDIR . '/modules/' . $site_mods[$module]['module_file'] . '/language/' . NV_LANG_DATA . '.php';
    if (isset($site_mods['notification']) && file_exists(NV_ROOTDIR . '/modules/notification/site.functions.php')) {
        require_once NV_ROOTDIR . '/modules/' . $site_mods[$module]['module_file'] . '/site.functions.php';
        
        $array_userid = nv_task_get_follow($id, $module);
        if (!empty($array_userid)) {
            require_once NV_ROOTDIR . '/modules/notification/site.functions.php';
            list ($title) = $db->query('SELECT title FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . ' WHERE id=' . $id)->fetch(3);
            $content = sprintf($lang_module['comment_task'], $user_info['full_name'] , $title);
            $url = NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module . '&' . NV_OP_VARIABLE . '=detail&id=' . $id . '#cid_' . $new_id;
            nv_send_notification($array_userid, $content, 'comment_task', $module, $url);
        }
    }
}

$db->query('UPDATE ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . ' SET edittime=' . NV_CURRENTTIME . ' WHERE id=' . $id);