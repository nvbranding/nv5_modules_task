<?php

/**
 * @Project NUKEVIET 4.x
 * @Author TDFOSS.,LTD (contact@tdfoss.vn)
 * @Copyright (C) 2018 TDFOSS.,LTD. All rights reserved
 * @Createdate Sat, 13 Jan 2018 13:35:09 GMT
 */
if (!defined('NV_SYSTEM')) die('Stop!!!');

define('NV_IS_MOD_TASK', true);

if (!defined('NV_IS_USER')) {
    Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt($client_info['selfurl']));
    die();
}

$array_config = $module_config[$module_name];
require_once NV_ROOTDIR . '/modules/task/site.functions.php';

$array_task_status = array(
    0 => $lang_module['task_status_0'],
    1 => $lang_module['task_status_1'],
    2 => $lang_module['task_status_2'],
    3 => $lang_module['task_status_3']
);

$array_priority = array(
    1 => $lang_module['priority1'],
    2 => $lang_module['priority2'],
    3 => $lang_module['priority3']
);

if (!isset($site_mods['workforce'])) {
    $workforce_list = array();
    $where = '';

    $where .= !empty($array_config['groups_use']) ? ' AND userid IN (SELECT userid FROM ' . NV_USERS_GLOBALTABLE . '_groups_users WHERE group_id IN (' . $array_config['groups_use'] . '))' : '';
    $result = $db->query('SELECT userid, first_name, last_name, username, email FROM ' . NV_USERS_GLOBALTABLE . ' WHERE active=1' . $where);
    while ($row = $result->fetch()) {
        $row['fullname'] = nv_show_name_user($row['first_name'], $row['last_name'], $row['username']);
        $workforce_list[$row['userid']] = $row;
    }
}

if (empty($workforce_list)) {
    $contents = nv_theme_alert($lang_module['workforce_empty_title'], $lang_module['workforce_empty_content'], 'danger');
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

$array_leader = $workforce_assign = $workforce_leader = $workforce_member = array();
$array_userid = $array_userid_allow = nv_task_premission($module_name, 'array_userid');

$count = $db->query('SELECT COUNT(*) FROM ' . NV_USERS_GLOBALTABLE . '_groups_users WHERE is_leader=1 AND approved=1 AND userid=' . $user_info['userid'])->fetchColumn();
if ($count > 0) {
    // nếu là trưởng nhóm bất kỳ thì có thể giao việc cho các trưởng nhóm khác
    $_query = $db->query('SELECT userid FROM ' . NV_USERS_GLOBALTABLE . '_groups_users where is_leader=1');
    while (list ($userid) = $_query->fetch(3)) {
        $array_leader[] = $userid;
    }
    $array_userid_allow = $array_userid_allow + $array_leader;
} else {
    // lấy danh sách userid thuộc các nhóm người này tham gia
    $result = $db->query('SELECT userid FROM ' . NV_USERS_GLOBALTABLE . '_groups_users WHERE approved=1 AND group_id IN (SELECT group_id FROM ' . NV_USERS_GLOBALTABLE . '_groups_users WHERE approved=1 AND userid=' . $user_info['userid'] . ')');
    while (list ($userid) = $result->fetch(3)) {
        $array_userid[] = $userid;
    }
    $array_userid_allow = $array_userid_allow + $array_userid;
}
$array_userid_allow = !empty($array_userid_allow) ? array_unique($array_userid_allow) : array();

foreach (array_keys($workforce_list) as $userid) {
    if (in_array($userid, $array_userid_allow)) {
        $workforce_assign[$userid] = $workforce_list[$userid];
    }
}

foreach (array_keys($workforce_list) as $userid) {
    if (in_array($userid, $array_leader)) {
        $workforce_leader[$userid] = $workforce_list[$userid];
    }
}

foreach (array_keys($workforce_list) as $userid) {
    if (in_array($userid, $array_userid) && !in_array($userid, $array_leader)) {
        $workforce_member[$userid] = $workforce_list[$userid];
    }
}

function nv_task_delete($id)
{
    global $db, $module_data;

    $rows = $db->query('SELECT title FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id=' . $id)->fetch();
    if ($rows) {
        $count = $db->exec('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id = ' . $id);
        if ($count) {
            $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_follow WHERE taskid = ' . $id);
            if (defined('NV_TASK')) {
                $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_task WHERE projectid=' . $id);
            }

            // xóa custom field
            $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_info WHERE rows_id=' . $id);
        }
        return $rows['title'];
    }
    return '';
}

$array_field_config = array();
$result_field = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_field WHERE show_profile=1 ORDER BY weight ASC');
while ($row_field = $result_field->fetch()) {
    $language = unserialize($row_field['language']);
    $row_field['title'] = (isset($language[NV_LANG_DATA])) ? $language[NV_LANG_DATA][0] : $row['field'];
    $row_field['description'] = (isset($language[NV_LANG_DATA])) ? nv_htmlspecialchars($language[NV_LANG_DATA][1]) : '';
    if (!empty($row_field['field_choices'])) {
        $row_field['field_choices'] = unserialize($row_field['field_choices']);
    } elseif (!empty($row_field['sql_choices'])) {
        $row_field['sql_choices'] = explode('|', $row_field['sql_choices']);
        $query = 'SELECT ' . $row_field['sql_choices'][2] . ', ' . $row_field['sql_choices'][3] . ' FROM ' . $row_field['sql_choices'][1];
        $result = $db->query($query);
        $weight = 0;
        while (list ($key, $val) = $result->fetch(3)) {
            $row_field['field_choices'][$key] = $val;
        }
    }
    $array_field_config[] = $row_field;
}

/**
 * nv_task_download()
 *
 * @param mixed $array_data
 * @param mixed $type
 * @return
 *
 */
function nv_task_download($title, $array_data, $type = 'xlsx')
{
    global $module_name, $admin_info, $lang_module, $workforce_list, $user_info, $array_field_config;

    if (empty($array_data)) {
        die('Nothing download!');
    }
    $array = array(
        'objType' => '',
        'objExt' => ''
    );
    switch ($type) {
        case 'xlsx':
            $array['objType'] = 'Excel2007';
            $array['objExt'] = 'xlsx';
            break;
        case 'ods':
            $array['objType'] = 'OpenDocument';
            $array['objExt'] = 'ods';
            break;
        default:
            $array['objType'] = 'CSV';
            $array['objExt'] = 'csv';
    }
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    // Set properties
    $objPHPExcel->getProperties()
        ->setCreator($admin_info['username'])
        ->setLastModifiedBy($admin_info['username'])
        ->setTitle($title)
        ->setSubject($title)
        ->setDescription($title)
        ->setCategory($module_name);
    $columnIndex = 0; // Cot bat dau ghi du lieu
    $rowIndex = 3; // Dong bat dau ghi du lieu
    // thông tin thành viên
    $objPHPExcel->getActiveSheet()
        ->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex) . $rowIndex, $lang_module['number'])
        ->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex + 1) . $rowIndex, $lang_module['title'])
        ->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex + 2) . $rowIndex, $lang_module['useradd'])
        ->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex + 3) . $rowIndex, $lang_module['performer'])
        ->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex + 4) . $rowIndex, $lang_module['begintime'])
        ->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex + 5) . $rowIndex, $lang_module['exptime'])
        ->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex + 6) . $rowIndex, $lang_module['description'])
        ->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex + 7) . $rowIndex, $lang_module['status'])
        ->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex + 8) . $rowIndex, $lang_module['priority']);
    $i = $columnIndex + 9;

    if(!empty($array_field_config)){
        foreach($array_field_config as $field){
            $objPHPExcel->getActiveSheet()->setCellValue(PHPExcel_Cell::stringFromColumnIndex($i) . $rowIndex, $field['title']);
            $i++;
        }
    }

    // Hiển thị thông tin câu trả lời
    $i = $rowIndex + 1;
    $number = 1;
    foreach ($array_data as $data) {
        // số thứ tự
        $col = PHPExcel_Cell::stringFromColumnIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $number);
        // thông tin thành viên
        $col = PHPExcel_Cell::stringFromColumnIndex(1);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $data['title']);
        $col = PHPExcel_Cell::stringFromColumnIndex(2);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $data['useradd_str']);
        $col = PHPExcel_Cell::stringFromColumnIndex(3);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $data['performer']);
        $col = PHPExcel_Cell::stringFromColumnIndex(4);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $data['begintime']);
        $col = PHPExcel_Cell::stringFromColumnIndex(5);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $data['exptime']);
        $col = PHPExcel_Cell::stringFromColumnIndex(6);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $data['description']);
        $col = PHPExcel_Cell::stringFromColumnIndex(7);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $data['status']);
        $col = PHPExcel_Cell::stringFromColumnIndex(8);
        $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $data['priority_title']);

        // thông tin tùy biến
        $j = $columnIndex + 9;
        if(!empty($array_field_config)){
            foreach($array_field_config as $field){
                $value = isset($data['custom_field'][$field['field']]) ? $data['custom_field'][$field['field']] : '';
                $col = PHPExcel_Cell::stringFromColumnIndex($j++);
                $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $value);
            }
        }

        $i++;
        $number++;
    }
    $highestRow = $i - 1;
    $highestColumn = PHPExcel_Cell::stringFromColumnIndex($j - 1);
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Sheet 1');
    // Set page orientation and size
    $objPHPExcel->getActiveSheet()
        ->getPageSetup()
        ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
    $objPHPExcel->getActiveSheet()
        ->getPageSetup()
        ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    // Excel title
    $objPHPExcel->getActiveSheet()->mergeCells('A2:' . $highestColumn . '2');
    $objPHPExcel->getActiveSheet()->setCellValue('A2', $title);
    $objPHPExcel->getActiveSheet()
        ->getStyle('A2')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()
        ->getStyle('A2')
        ->getAlignment()
        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    // Cấu hình hiển thị trong excel
    $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'argb' => 'FF000000'
                )
            )
        )
    );
    $objPHPExcel->getActiveSheet()
        ->getStyle('A3' . ':' . $highestColumn . $highestRow)
        ->applyFromArray($styleArray);

    // Set font size
    $objPHPExcel->getActiveSheet()
        ->getStyle("A1:" . $highestColumn . $highestRow)
        ->getFont()
        ->setSize(13);

    $styleArray = array(
        'font' => array(
            'bold' => true
        )
    );
    $objPHPExcel->getActiveSheet()
        ->getStyle('A2')
        ->applyFromArray($styleArray);

    // Set font size
    $objPHPExcel->getActiveSheet()
        ->getStyle("A2")
        ->getFont()
        ->setSize(16);

    $objPHPExcel->getActiveSheet()
        ->getStyle("A3:" . $highestColumn . 3)
        ->getFont()
        ->setBold(true);

    // Set auto column width
    foreach (range('A', $highestColumn) as $columnID) {
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension($columnID)
            ->setAutoSize(true);
    }

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $array['objType']);
    $file_src = NV_ROOTDIR . NV_BASE_SITEURL . NV_TEMP_DIR . '/' . change_alias($lang_module['post_list'] . '-' . nv_date('d/m/Y', NV_CURRENTTIME)) . '.' . $array['objExt'];
    $objWriter->save($file_src);

    $download = new NukeViet\Files\Download($file_src, NV_ROOTDIR . NV_BASE_SITEURL . NV_TEMP_DIR);
    $download->download_file();
    die();
}

function nv_check_task_admin($useradd)
{
    global $user_info, $array_config;

    $group_manage = !empty($array_config['groups_manage']) ? explode(',', $array_config['groups_manage']) : array();
    $group_manage = array_map('intval', $group_manage);

    if (!empty(array_intersect($group_manage, $user_info['in_groups'])) || $user_info['userid'] == $useradd || defined('NV_TASK_LEADER')) {
        return true;
    }
    return false;
}

function nv_task_makeLinks($value, $protocols = array('http', 'mail'), array $attributes = array())
{
    // Link attributes
    $attr = '';
    foreach ($attributes as $key => $val) {
        $attr = ' ' . $key . '="' . htmlentities($val) . '"';
    }

    $links = array();

    // Extract existing links and tags
    $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) {
        return '<' . array_push($links, $match[1]) . '>';
    }, $value);

    // Extract text links for each protocol
    foreach ((array) $protocols as $protocol) {
        switch ($protocol) {
            case 'http':
            case 'https':
                $value = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) {
                    if ($match[1]) $protocol = $match[1];
                    $link = $match[2] ?: $match[3];
                    return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$link</a>") . '>';
                }, $value);
                break;
            case 'mail':
                $value = preg_replace_callback('~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~', function ($match) use (&$links, $attr) {
                    return '<' . array_push($links, "<a $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>';
                }, $value);
                break;
            case 'twitter':
                $value = preg_replace_callback('~(?<!\w)[@#](\w++)~', function ($match) use (&$links, $attr) {
                    return '<' . array_push($links, "<a $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1] . "\">{$match[0]}</a>") . '>';
                }, $value);
                break;
            default:
                $value = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) {
                    return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>';
                }, $value);
                break;
        }
    }

    // Insert all link
    return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) {
        return $links[$match[1] - 1];
    }, $value);
}