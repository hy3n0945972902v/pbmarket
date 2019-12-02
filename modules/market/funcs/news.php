<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (!defined('NV_IS_MOD_MARKET')) die('Stop!!!');

if ($nv_Request->isset_request('submit', 'post')) {
    $content = $nv_Request->get_textarea('content', '', NV_ALLOWED_HTML_TAGS);

    $timeout = $nv_Request->get_int($module_data . '_' . $op . '_timeout', 'cookie', 0);
    $difftimeout = 360;

    if (!($timeout == 0 or NV_CURRENTTIME - $timeout > $difftimeout or defined('NV_IS_ADMIN'))) {
        $timeout = nv_convertfromSec($difftimeout - NV_CURRENTTIME + $timeout);
        nv_jsonOutput(array(
            'error' => 1,
            'msg' => sprintf($lang_module['news_timeout'], $timeout)
        ));
    }

    if (empty($content)) {
        nv_jsonOutput(array(
            'error' => 1,
            'msg' => $lang_module['error_required_content'],
            'input' => 'content'
        ));
    }

    $stmt = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_news (content, addtime) VALUES(:content, ' . NV_CURRENTTIME . ')');
    $stmt->bindParam(':content', $content, PDO::PARAM_STR, strlen($content));
    if ($stmt->execute()) {

        if ($difftimeout) {
            $nv_Request->set_Cookie($module_data . '_' . $op . '_timeout', NV_CURRENTTIME, $difftimeout);
        }

        nv_jsonOutput(array(
            'error' => 0,
            'msg' => $lang_module['news_success']
        ));
    }
    nv_jsonOutput(array(
        'error' => 1,
        'msg' => $lang_module['error_unknow']
    ));
}

$xtpl = new XTemplate('news.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['news'];

$array_mod_title[] = array(
    'title' => $page_title
);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';