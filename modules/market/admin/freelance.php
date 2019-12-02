<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2015 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 02 Jun 2015 07:53:31 GMT
 */
if (! defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

if (empty($array_config['freelancegroup'])) {
    $contents = nv_theme_alert($lang_module['freelance_error_config_title'], $lang_module['freelance_error_config_content'], 'warning');
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

// Cập nhật lại danh sách Cộng tác viên
$array_userid = $array_userid_old = $array_userinfo = array();
$result = $db->query('SELECT userid FROM ' . NV_GROUPS_GLOBALTABLE . '_users WHERE group_id=' . $array_config['freelancegroup']);
while (list ($userid) = $result->fetch(3)) {
    $array_userid[] = $userid;
}
$array_userid = array_unique($array_userid);

$result = $db->query('SELECT userid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_freelance');
while (list ($userid) = $result->fetch(3)) {
    $array_userid_old[] = $userid;
}

if ($array_userid != $array_userid_old) {
    $sth = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_freelance (userid) VALUES(:userid)');
    foreach ($array_userid as $userid) {
        if (! in_array($userid, $array_userid_old)) {
            $sth->bindParam(':userid', $userid, PDO::PARAM_INT);
            $sth->execute();
        }
    }
    
    foreach ($array_userid_old as $userid_old) {
        if (! in_array($userid_old, $array_userid)) {
            $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_freelance WHERE userid=' . $userid_old);
        }
    }
    $nv_Cache->delMod($module_name);
}

if (! empty($array_userid)) {
    $result = $db->query('SELECT userid, username, first_name, last_name, email FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid IN (' . implode(',', $array_userid) . ')');
    while ($row = $result->fetch()) {
        list ($row['total'], $row['salary'], $row['pay']) = $db->query('SELECT total, salary, pay FROM ' . NV_PREFIXLANG . '_' . $module_data . '_freelance WHERE userid=' . $row['userid'])->fetch(3);
        $array_userinfo[$row['userid']] = $row;
    }
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

if (! empty($array_userinfo)) {
    foreach ($array_userinfo as $userinfo) {
        $userinfo['rest'] = nv_market_number_format($userinfo['total'] - $userinfo['pay']);
        $userinfo['total'] = nv_market_number_format($userinfo['total']);
        $userinfo['pay'] = nv_market_number_format($userinfo['pay']);
        $userinfo['fullname'] = nv_show_name_user($userinfo['first_name'], $userinfo['last_name'], $userinfo['username']);
        $xtpl->assign('DATA', $userinfo);
        $xtpl->parse('main.loop');
    }
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['freelance'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';