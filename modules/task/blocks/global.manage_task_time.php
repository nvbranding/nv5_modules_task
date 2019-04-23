<?php

/**
 * @Project NUKEVIET 4.x
 * @Author TDFOSS.,LTD (quanglh268@tdfoss.vn)
 * @Copyright (C) 2018 TDFOSS.,LTD. All rights reserved
 * @Createdate Tue, 05 Feb 2018 08:34:29 GMT
 */
if (!defined('NV_MAINFILE')) die('Stop!!!');

if (!nv_function_exists('nv_task_list_time')) {

    function nv_block_config_task_list_time($module, $data_block, $lang_block)
    {
        $html = '';

        $array_updown = array(
            'addtime' => $lang_block['updown1'],
            'edittime' => $lang_block['updown2']
        );

        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_block['updown'] . ':</label>';
        $html .= '<div class="col-sm-18">';
        $html .= '<select class="form-control col-sm-18" name="config_updown">';
        foreach ($array_updown as $index => $value) {
            $se = ($index == $data_block['updown']) ? 'selected="selected"' : '';
            $html .= '<option name="config_updown[]" value="' . $index . '" ' . $se . '> ' . $value . ' </option>';
        }

        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '	<label class="control-label col-sm-6">' . $lang_block['numrow'] . ':</label>';
        $html .= '	<div class="col-sm-18"><input class="form-control" type="text" name="config_numrow" value="' . $data_block['numrow'] . '"/></div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '	<label class="control-label col-sm-6">' . $lang_block['characters'] . ':</label>';
        $html .= '	<div class="col-sm-18"><input class="form-control" type="text" name="config_characters" value="' . $data_block['characters'] . '"/></div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_block['type'] . ':</label>';
        $html .= '<div class="col-sm-9">';

        $aray_status = array(
            0 => $lang_block['type_0'],
            1 => $lang_block['type_1']
        );

        $html .= '<select class="form-control" name="config_type">';
        foreach ($aray_status as $index => $value) {
            $se = $index == $data_block['type'] ? 'selected="selected"' : '';
            $html .= '<option value="' . $index . '" ' . $se . '>' . $value . '</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '	<label class="control-label col-sm-6">' . $lang_block['numday'] . ':</label>';
        $html .= '	<div class="col-sm-18"><input class="form-control" type="text" name="config_numday" value="' . $data_block['numday'] . '"/></div>';
        $html .= '</div>';

        return $html;
    }

    function nv_block_config_task_list_time_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 10);
        $return['config']['characters'] = $nv_Request->get_int('config_characters', 'post', 100);
        $return['config']['type'] = $nv_Request->get_int('config_type', 'post', 0);
        $return['config']['updown'] = $nv_Request->get_string('config_updown', 'post', 'addtime');
        $return['config']['numday'] = $nv_Request->get_int('config_numday', 'post', 0);

        return $return;
    }

    function nv_task_list_time($block_config)
    {
        global $db, $global_config, $site_mods, $module_name, $my_footer, $lang_module, $module_config, $user_info, $array_config, $workforce_list;

        $module = $block_config['module'];
        $mod_data = $site_mods[$module]['module_data'];

        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/blocks/global.manage_task_time.tpl')) {
            $block_theme = $global_config['module_theme'];
        } elseif (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/blocks/global.manage_task_time.tpl')) {
            $block_theme = $global_config['site_theme'];
        } else {
            $block_theme = 'default';
        }

        if ($module != $module_name) {
            $my_footer .= '<script type="text/javascript" src="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/js/task.js"></script>';
            require_once NV_ROOTDIR . '/modules/task/language/' . NV_LANG_DATA . '.php';
            require_once NV_ROOTDIR . '/modules/task/site.functions.php';
            $array_config = $module_config[$module];
        }

        $where = '';
        if ($block_config['type'] == 0) {
            $where = ' AND begintime != 0 AND begintime >= ' . NV_CURRENTTIME . ' AND begintime <= ' . (NV_CURRENTTIME + (86400 * intval($block_config['numday'])));
        } elseif ($block_config['type'] == 1) {
            $where = ' AND exptime != 0 AND exptime >= ' . NV_CURRENTTIME . ' AND exptime <= ' . (NV_CURRENTTIME + (86400 * intval($block_config['numday'])));
        }

        $list = array();
        $sql = 'SELECT id, title, performer, groups_made, status, addtime, edittime FROM ' . NV_PREFIXLANG . '_' . $mod_data . ' t1 INNER JOIN ' . NV_PREFIXLANG . '_' . $mod_data . '_follow t2 ON t1.id=t2.taskid WHERE 1=1' . $where . ' ORDER BY ' . $block_config['updown'] . ' DESC LIMIT ' . $block_config['numrow'];
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            $list[$row['id']] = $row;
        }

        $array_groups = nv_groups_list_pub();

        $xtpl = new XTemplate('block.manage_task_time.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/task');
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('BLOCK_THEME', $block_theme);

        if (!empty($list)) {
            foreach ($list as $view) {
                $view['title'] = nv_clean60($view['title'], $block_config['characters']);
                $view['status'] = $lang_module['task_status_' . $view['status']];

                $view['performer_str'] = array();
                $performer = !empty($view['performer']) ? explode(',', $view['performer']) : array();
                $groups_made = !empty($view['groups_made']) ? explode(',', $view['groups_made']) : array();
                if(!empty($performer)){
                    foreach ($performer as $userid) {
                        $view['performer_str'][] = $workforce_list[$userid]['fullname'];
                    }
                }
                if(!empty($groups_made)){
                    foreach ($groups_made as $groupid) {
                        $view['performer_str'][] = $array_groups[$groupid];
                    }
                }
                $view['performer_str'] = !empty($view['performer_str']) ? implode(', ', $view['performer_str']) : '';

                $view['link_view'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $mod_data . '&amp;' . NV_OP_VARIABLE . '=detail&amp;id=' . $view['id'];
                $xtpl->assign('TASK_VIEW', $view);
                $xtpl->parse('main.task');
            }
        }

        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    $content = nv_task_list_time($block_config);
}