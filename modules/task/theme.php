<?php

/**
 * @Project NUKEVIET 4.x
 * @Author TDFOSS.,LTD (contact@tdfoss.vn)
 * @Copyright (C) 2018 TDFOSS.,LTD. All rights reserved
 * @Createdate Sat, 13 Jan 2018 13:35:09 GMT
 */
if (!defined('NV_IS_MOD_TASK')) die('Stop!!!');

/**
 * nv_theme_task_main()
 *
 * @param mixed $array_data
 * @return
 */
function nv_theme_task_main($array_data)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op;

    $xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_task_detail()
 *
 * @param mixed $rows
 * @param mixed $content_comment
 * @return
 */
function nv_theme_task_detail($rows, $content_comment, $array_control)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_task_status, $array_priority, $array_field_config, $custom_fields;

    if (!empty($rows['description'])) {
        $rows['description'] = nv_nl2br($rows['description']);
    }

    $xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('ROW', $rows);
    $xtpl->assign('CONTROL', $array_control);

    if (!empty($rows['description'])) {
        $xtpl->parse('main.description');
    }

    if (!empty($content_comment)) {
        $xtpl->assign('COMMENT', $content_comment);
        $xtpl->parse('main.comment');
    }

    foreach ($array_task_status as $index => $value) {
        $sl = $index == $rows['status'] ? 'selected="selected"' : '';
        $xtpl->assign('STATUS', array(
            'index' => $index,
            'value' => $value,
            'selected' => $sl
        ));
        $xtpl->parse('main.status');
    }
    foreach ($array_priority as $index => $value) {
        $sl = $index == $rows['priority'] ? 'selected="selected"' : '';
        $xtpl->assign('PRIORITY', array(
            'index' => $index,
            'value' => $value,
            'selected' => $sl
        ));
        $xtpl->parse('main.priority');
    }

    if (nv_check_task_admin($rows['useradd'])) {
        $xtpl->parse('main.admin');
    }

    if ($rows['projectid']) {
        $xtpl->parse('main.project');
    }

    if (!empty($array_field_config)) {
        foreach ($array_field_config as $row) {
            if ($row['show_profile']) {
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
                if (empty($value)) {
                    $value = '-';
                }
                $xtpl->assign('FIELD', array(
                    'title' => $row['title'],
                    'value' => $value
                ));
                $xtpl->parse('main.field.loop');
            }
        }
        $xtpl->parse('main.field');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}