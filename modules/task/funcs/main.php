<?php
/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sun, 07 Jan 2018 13:39:44 GMT
 */
if (!defined('NV_IS_MOD_TASK')) die('Stop!!!');

if ($nv_Request->isset_request('delete_id', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $id = $nv_Request->get_int('delete_id', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    list ($task_name, $useradd) = $db->query('SELECT title, useradd FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id=' . $id)->fetch(3);

    if ($id > 0 and $delete_checkss == md5($id . NV_CACHE_PREFIX . $client_info['session_id']) && nv_check_task_admin($useradd)) {
        nv_task_delete($id);

        $content = sprintf($lang_module['logs_delete_note'], $task_name);
        nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['logs_delete'], $content, $user_info['userid']);

        $nv_Cache->delMod($module_name);
        Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
} elseif ($nv_Request->isset_request('delete_list', 'post')) {
    $listall = $nv_Request->get_title('listall', 'post', '');
    $array_id = explode(',', $listall);
    $array_title = array();
    if (!empty($array_id)) {
        foreach ($array_id as $id) {
            $array_title[] = nv_task_delete($id);
        }
        $content = sprintf($lang_module['logs_delete_note'], implode(', ', $array_title));
        nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['logs_delete'], $content, $user_info['userid']);
        $nv_Cache->delMod($module_name);
        die('OK');
    }
    die('NO');
}

if ($nv_Request->isset_request('change_read', 'get')) {
    $id = $nv_Request->get_int('change_read', 'get');
    $task_name = $db->query('SELECT title FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id=' . $id)->fetch();
    if ($id > 0) {
        $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_follow SET readed=0 WHERE taskid=' . $id . ' AND userid=' . $user_info['userid']);
        $nv_Cache->delMod($module_name);
        Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
}

$array_search = array(
    'search' => $nv_Request->isset_request('search', 'post,get'),
    'q' => $nv_Request->get_title('q', 'post,get'),
    'userid' => $nv_Request->get_int('userid', 'get', 0),
    'useradd' => $nv_Request->get_int('useradd', 'get', 0),
    'status' => $nv_Request->get_int('status', 'get', -1),
    'priority' => $nv_Request->get_int('priority', 'get', 0),
    'datesource' => $nv_Request->get_string('datesource', 'get', ''),
    'from' => $nv_Request->get_string('from', 'get', ''),
    'to' => $nv_Request->get_string('to', 'get', ''),
    'type' => $nv_Request->get_int('type', 'get', 0)
);

if (!class_exists('PHPExcel')) {
    if (file_exists(NV_ROOTDIR . '/includes/class/PHPExcel.php')) {
        require_once NV_ROOTDIR . '/includes/class/PHPExcel.php';
    }
}

$array_groups = nv_groups_list_pub();
$array_data = array();
$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
$per_page = 20;
$page = $nv_Request->get_int('page', 'post,get', 1);
$where = '';
$join = '';
$is_full = 1;

if (!empty($array_search['q'])) {
    $base_url .= '&q=' . $array_search['q'];
    $where .= ' AND (title LIKE "%' . $array_search['q'] . '%" OR description LIKE "%' . $array_search['q'] . '%")';
}

if (!empty($array_search['priority'])) {
    $base_url .= '&priority=' . $array_search['priority'];
    $where .= ' AND priority=' . $array_search['priority'];
}

if (!empty($array_search['userid'])) {
    $is_full = 0;
    $base_url .= '&userid=' . $array_search['userid'];
    $where .= ' AND t2.userid=' . $array_search['userid'] . ' AND ' . $array_search['userid'] . ' IN (t1.performer)';
}

if (!empty($array_search['useradd'])) {
    $is_full = 0;
    $base_url .= '&useradd=' . $array_search['useradd'];
    $where .= ' AND t1.useradd=' . $array_search['useradd'];
}

if ($array_search['status'] >= 0) {
    $base_url .= '&status=' . $array_search['status'];
    $where .= ' AND status=' . $array_search['status'];
} elseif (!$array_search['search'] && !empty($array_config['default_status'])) {
    $where .= ' AND status IN (' . $array_config['default_status'] . ')';
}

if ($array_search['type'] == 1) {
    $base_url .= '&type=' . $array_search['type'];
    $where .= ' AND t1.status = 0 AND t1.begintime > 0 AND t1.begintime <= ' . NV_CURRENTTIME;
} elseif ($array_search['type'] == 2) {
    $base_url .= '&type=' . $array_search['type'];
    $where .= ' AND t1.status = 1 AND t1.exptime> 0 AND t1.exptime <= ' . NV_CURRENTTIME;
}

if (!empty($array_search['datesource'])) {
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('from', 'get'), $m)) {
        $date = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
        $where .= ' AND ' . $array_search['datesource'] . ' >= ' . $date;
    }

    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('to', 'get'), $m)) {
        $date = mktime(23, 59, 59, $m[2], $m[1], $m[3]);
        $where .= ' AND ' . $array_search['datesource'] . ' <= ' . $date;
    }
}

if ($is_full) {
    $where .= nv_task_premission($module_name, 'where');
}

$is_download = false;
if ($nv_Request->isset_request('download', 'post,get') and class_exists('PHPExcel')) {
    $is_download = true;
    $db->sqlreset()
        ->select('COUNT(DISTINCT id)')
        ->from(NV_PREFIXLANG . '_' . $module_data);

    $sth = $db->prepare($db->sql());
    $sth->execute();
    $num_items = $sth->fetchColumn();

    $db->select('DISTINCT id, title, performer, groups_made, begintime, exptime, realtime, description, useradd, projectid, edittime, status, priority')->order('priority ASC, id DESC');
    $sth = $db->prepare($db->sql());
    $sth->execute();
} else {
    $db->sqlreset()
        ->select('COUNT(DISTINCT id), t2.readed')
        ->from(NV_PREFIXLANG . '_' . $module_data . ' t1')
        ->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_follow t2 ON t1.id=t2.taskid')
        ->where('1=1' . $where);

    $sth = $db->prepare($db->sql());
    $sth->execute();
    $num_items = $sth->fetchColumn();

    $db->select('DISTINCT id, title, performer, groups_made, begintime, exptime, realtime, description, useradd, projectid, edittime, status, priority, t2.readed')
        ->order('priority ASC, id DESC')
        ->from(NV_PREFIXLANG . '_' . $module_data . ' t1')
        ->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_follow t2 ON t1.id=t2.taskid')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $sth = $db->prepare($db->sql());
    $sth->execute();
}

$array_projects = array();

while ($view = $sth->fetch()) {
    $view['realtime'] = (empty($view['realtime'])) ? '' : nv_date('H:i d/m/Y', $view['realtime']);
    $view['useradd_str'] = isset($workforce_list[$view['useradd']]) ? $workforce_list[$view['useradd']]['fullname'] : '-';

    $view['performer_str'] = array();
    $performer = !empty($view['performer']) ? explode(',', $view['performer']) : array();
    $groups_made = !empty($view['groups_made']) ? explode(',', $view['groups_made']) : array();
    if (!empty($performer)) {
        foreach ($performer as $userid) {
            $view['performer_str'][] = isset($workforce_list[$userid]) ? $workforce_list[$userid]['fullname'] : '-';
        }
    }
    if (!empty($groups_made)) {
        foreach ($groups_made as $groupid) {
            $view['performer_str'][] = $array_groups[$groupid];
        }
    }
    $view['performer_str'] = !empty($view['performer_str']) ? implode(', ', $view['performer_str']) : '';
    $view['performer'] = $view['performer_str'];

    $view['status'] = $array_task_status[$view['status']];

    if ($view['projectid']) {
        if (!isset($array_projects[$view['projectid']])) {
            $array_projects = $db->query('SELECT id, title FROM ' . NV_PREFIXLANG . '_projects WHERE id=' . $view['projectid'])->fetch();
            $array_projects['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=projects&amp;' . NV_OP_VARIABLE . '=detail&id=' . $view['projectid'];
            $view['project'] = $array_projects;
        } else {
            $view['project'] = $array_projects[$view['projectid']];
        }
    }

    $view['priority_title'] = $lang_module['priority' . $view['priority']];

    if ($is_download) {
        $view['custom_field'] = array();

        $result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_info WHERE rows_id=' . $view['id']);
        $custom_fields = $result->fetch();

        if ($custom_fields && !empty($array_field_config)) {
            foreach ($array_field_config as $row) {
                $question_type = $row['field_type'];
                if ($question_type == 'checkbox') {
                    $result = explode(',', $custom_fields[$row['field']]);
                    $value = '';
                    foreach ($result as $item) {
                        $value .= $row['field_choices'][$item] . '<br />';
                    }
                } elseif ($question_type == 'multiselect' or $question_type == 'select' or $question_type == 'radio') {
                    $value = $row['field_choices'][$custom_fields[$row['field']]];
                } elseif ($question_type == 'date') {
                    $value = !empty($custom_fields[$row['field']]) ? nv_date('d/m/Y', $custom_fields[$row['field']]) : '';
                } else {
                    $value = $custom_fields[$row['field']];
                }
                $view['custom_field'][$row['field']] = $value;
            }
        }

        $view['description'] = nv_unhtmlspecialchars(strip_tags($view['description']));
    }
    $array_data[$view['id']] = $view;
}

// Download
if ($is_download && !empty($array_data)) {
    foreach ($array_data as $index => $data) {
        $array_data[$index]['begintime'] = !empty($array_data[$index]['begintime']) ? nv_date('H:i d/m/Y', $array_data[$index]['begintime']) : '';
        $array_data[$index]['exptime'] = !empty($array_data[$index]['exptime']) ? nv_date('H:i d/m/Y', $array_data[$index]['exptime']) : '';
        unset($array_data[$index]['performer_str'], $array_data[$index]['readed'], $array_data[$index]['priority']);
    }
    nv_task_download($lang_module['task'], $array_data);
    die();
}

if (!empty($array_data)) {
    $result = $db->query('SELECT taskid, readed FROM ' . NV_PREFIXLANG . '_' . $module_data . '_follow WHERE taskid IN (' . implode(',', array_keys($array_data)) . ') AND userid=' . $user_info['userid']);
    while (list ($taskid, $readed) = $result->fetch(3)) {
        $array_data[$taskid]['readed'] = $readed;
    }
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('SEARCH', $array_search);
$xtpl->assign('URL_ADD', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content');

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
if (!empty($generate_page)) {
    $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}

if (!empty($array_data)) {
    foreach ($array_data as $view) {
        $view['percent'] = 0;
        if (!empty($view['begintime']) && !empty($view['exptime'])) {
            $a = $view['exptime'] - $view['begintime'];
            $b = NV_CURRENTTIME - $view['begintime'];
            $view['percent'] = ($b * 100) / $a;
            $view['percent'] = $view['percent'] <= 100 ? $view['percent'] : 100;
        }

        if ($view['priority'] == 1) {
            $view['priority_class'] = 'color1';
        } elseif ($view['priority'] == 2) {
            $view['priority_class'] = 'color2';
        } else {
            $view['priority_class'] = 'color3';
        }

        if ($view['useradd'] == $user_info['userid']) {
            //
        } elseif (empty($view['readed'])) {
            $xtpl->parse('main.loop.warning');
        }
        $view['priority'] = !empty($view['priority']) ? $array_priority[$view['priority']] : '';
        $view['begintime_start'] = (empty($view['begintime'])) ? '' : nv_date('H:i d/m/Y', $view['begintime']);
        $view['exptime'] = (empty($view['exptime'])) ? '' : nv_date('H:i d/m/Y', $view['exptime']);
        $view['link_view'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=detail&amp;id=' . $view['id'];

        if (nv_check_task_admin($view['useradd'])) {
            $view['link_edit'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&amp;id=' . $view['id'] . '&amp;redirect=' . nv_redirect_encrypt($client_info['selfurl']);
            $view['link_delete'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_id=' . $view['id'] . '&amp;delete_checkss=' . md5($view['id'] . NV_CACHE_PREFIX . $client_info['session_id']);
        }

        $xtpl->assign('VIEW', $view);

        if (nv_check_task_admin($view['useradd'])) {
            $xtpl->parse('main.loop.admin');
        }

        if ($view['begintime'] > NV_CURRENTTIME) {
            $xtpl->parse('main.loop.nonepercent');
        } elseif (!empty($view['percent'])) {
            $xtpl->parse('main.loop.percent');
        }

        if ($view['projectid']) {
            $xtpl->parse('main.loop.project');
        }

        $xtpl->parse('main.loop');
    }
}

if (!empty($workforce_assign)) {
    foreach ($workforce_assign as $user) {
        $user['selected'] = $user['userid'] == $array_search['userid'] ? 'selected="selected"' : '';
        $xtpl->assign('USER', $user);
        $xtpl->parse('main.user');
    }
}

if (!empty($workforce_assign)) {
    foreach ($workforce_assign as $user) {
        $user['selected'] = $user['userid'] == $array_search['useradd'] ? 'selected="selected"' : '';
        $xtpl->assign('USERADD', $user);
        $xtpl->parse('main.useradd');
    }
}

foreach ($array_task_status as $index => $value) {
    $sl = $index == $array_search['status'] ? 'selected="selected"' : '';
    $xtpl->assign('STATUS', array(
        'index' => $index,
        'value' => $value,
        'selected' => $sl
    ));
    $xtpl->parse('main.status');
}

foreach ($array_priority as $index => $value) {
    $sl = $index == $array_search['priority'] ? 'selected="selected"' : '';
    $xtpl->assign('PRIORITY', array(
        'index' => $index,
        'value' => $value,
        'selected' => $sl
    ));
    $xtpl->parse('main.priority');
}

$array_datesource = array(
    'addtime' => $lang_module['datesource_1'],
    'begintime' => $lang_module['datesource_2'],
    'exptime' => $lang_module['datesource_3']
);
foreach ($array_datesource as $index => $value) {
    $sl = $index == $array_search['datesource'] ? 'selected="selected"' : '';
    $xtpl->assign('DATESOURCE', array(
        'index' => $index,
        'value' => $value,
        'selected' => $sl
    ));
    $xtpl->parse('main.datesource');
}

if (!empty($array_data) and class_exists('PHPExcel')) {
    $xtpl->assign('URL_EXCEL', $base_url . '&download=1');
} else {
    $xtpl->parse('main.btn_disabled');
}

$array_action = array(
    'delete_list_id' => $lang_global['delete']
);
foreach ($array_action as $key => $value) {
    $xtpl->assign('ACTION', array(
        'key' => $key,
        'value' => $value
    ));
    $xtpl->parse('main.action_top');
    $xtpl->parse('main.action_bottom');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['task'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';