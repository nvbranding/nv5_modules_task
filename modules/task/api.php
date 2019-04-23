<?php

/**
 * @Project NUKEVIET 4.x
 * @Author TDFOSS.,LTD (contact@tdfoss.vn)
 * @Copyright (C) 2018 TDFOSS.,LTD. All rights reserved
 * @Createdate Tue, 02 Jan 2018 08:34:29 GMT
 */
if (!defined('NV_IS_MOD_API')) die('Stop!!!');

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App();

require_once NV_ROOTDIR . '/modules/task/language/' . NV_LANG_DATA . '.php';

// add customer
$app->post('/api/task/add/', function (Request $request, Response $response, array $args) {
    global $db, $nv_Request, $module_config, $lang_module, $array_config;

    $row = array();
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['performer'] = $nv_Request->get_title('performer', 'post', '');
    $row['begintime'] = $nv_Request->get_int('begintime', 'post', 0);
    $row['exptime'] = $nv_Request->get_int('exptime', 'post', 0);
    $row['description'] = $nv_Request->get_textarea('description', '', NV_ALLOWED_HTML_TAGS);
    $row['useradd'] = $nv_Request->get_int('useradd', 'post', 0);
    $row['addtime'] = $nv_Request->get_int('addtime', 'post', NV_CURRENTTIME);

    if (empty($row['title'])) {
        nv_jsonOutput(array(
            'error' => 1,
            'msg' => $lang_module['error_required_title']
        ));
    }

    if (empty($row['performer'])) {
        nv_jsonOutput(array(
            'error' => 1,
            'msg' => $lang_module['error_required_performer']
        ));
    }

    try {
        $_sql = 'INSERT INTO ' . NV_PREFIXLANG . '_task (title, performer, begintime, exptime, description, useradd, addtime) VALUES (:title, :performer, :begintime, :exptime, :description, ' . $row['useradd'] . ', ' . $row['addtime'] . ')';
        $data_insert = array();
        $data_insert['title'] = $row['title'];
        $data_insert['performer'] = $row['performer'];
        $data_insert['begintime'] = $row['begintime'];
        $data_insert['exptime'] = $row['exptime'];
        $data_insert['description'] = $row['description'];
        $new_id = $db->insert_id($_sql, 'id', $data_insert);
        if ($new_id) {
            $row['performer'] = explode(',', $row['performer']);
            $sth = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_task_follow (taskid, userid) VALUES( :taskid, :userid)');
            foreach ($row['performer'] as $userid) {
                $sth->bindParam(':taskid', $new_id, PDO::PARAM_INT);
                $sth->bindParam(':userid', $userid, PDO::PARAM_INT);
                $sth->execute();
            }

            // notification
            require_once NV_ROOTDIR . '/modules/notification/site.functions.php';
            $content = sprintf($lang_module['new_task'], $row['title']);
            $url = NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=task&' . NV_OP_VARIABLE . '=detail&id=' . $new_id;
            nv_send_notification($row['performer'], $content, 'new_task', 'task', $url);
        }
    } catch (PDOException $e) {
        trigger_error($e->getMessage());
        nv_jsonOutput(array(
            'error' => 1,
            'msg' => $e->getMessage()
        ));
    }
    nv_jsonOutput(array(
        'error' => 0
    ));
});