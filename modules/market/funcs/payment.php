<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (! defined('NV_IS_MOD_MARKET'))
    die('Stop!!!');

$id = $nv_Request->get_int('id', 'post,get', 0);
$groupid = $nv_Request->get_int('groupid', 'post,get', 0);
$mod = $nv_Request->get_title('mod', 'post,get', '');
$array_option = array();
$contents = '';

$array_info = $db->query('SELECT id, title, catid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $id)->fetch();
$array_info['checksum'] = md5($global_config['sitekey'] . $user_info['userid'] . $array_info['id']);
$array_info['mod'] = $mod;

if ($array_info) {
    if ($mod == 'refresh') {
        if ($array_config['refresh_allow']) {
            $array_info['id'] = 0;
            $array_option = unserialize($array_config['refresh_config']);
        }
    } elseif ($mod == 'group') {
        $_array_option = unserialize($array_config['specialgroup_config']);
        $array_groups = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block_cat WHERE useradd=1 AND bid=' . $groupid . ' ORDER BY weight ASC')->fetch();
        if ($array_groups) {
            if (isset($_array_option[$groupid])) {
                $array_option = $_array_option[$groupid];
            }
        }
    }
    
    $contents = nv_theme_market_payment($array_info, $array_option, $id, $mod);
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents, false);
include NV_ROOTDIR . '/includes/footer.php';