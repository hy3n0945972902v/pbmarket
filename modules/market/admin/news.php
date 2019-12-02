<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2015 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 02 Jun 2015 07:53:31 GMT
 */
if (!defined('NV_IS_FILE_ADMIN')) die('Stop!!!');

$date = $nv_Request->get_string('date', 'get', date('d/m/Y', NV_CURRENTTIME));

$array_data = array();
$result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_news WHERE DATE_FORMAT(FROM_UNIXTIME(addtime),"%d%m%Y")=' . str_replace('/', '', $date));
while ($row = $result->fetch()) {
    $array_data[] = $row;
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('DATE', $date);

if (!empty($array_data)) {
    foreach ($array_data as $data) {
        $xtpl->assign('DATA', $data);
        $xtpl->parse('main.loop');
    }
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['news'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';