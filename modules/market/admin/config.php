<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-9-2010 14:43
 */
if (!defined('NV_IS_FILE_ADMIN')) die('Stop!!!');

$page_title = $lang_module['config'];
$groups_list = nv_groups_list();

if ($nv_Request->isset_request('savesetting', 'post')) {
    $data['freelancegroup'] = $nv_Request->get_int('freelancegroup', 'post', 0);
    $data['money_unit'] = $nv_Request->get_title('money_unit', 'post', 'đ');
    $data['homedata'] = $nv_Request->get_int('homedata', 'post', 1);
    $data['hometype'] = $nv_Request->get_title('hometype', 'post', 'viewlist');
    $data['style_default'] = $nv_Request->get_title('style_default', 'post', 'viewlist_simple');
    $data['per_page'] = $nv_Request->get_int('per_page', 'post', 20);
    $data['structure_upload'] = $nv_Request->get_title('structure_upload', 'post', 'username_Y');
    $data['no_image'] = $nv_Request->get_title('no_image', 'post', '');
    $data['allow_auto_code'] = $nv_Request->get_int('allow_auto_code', 'post', 0);
    $data['code_format'] = $nv_Request->get_title('code_format', 'post', 'T%06s');
    $data['numother'] = $nv_Request->get_int('numother', 'post', 15);
    $data['maxsizeimage'] = $nv_Request->get_title('maxsizeimage', 'post', '800x800');
    $data['maxsizeupload'] = $nv_Request->get_int('maxsizeupload', 'post', 1342177);
    $data['googlemaps_appid'] = $nv_Request->get_title('googlemaps_appid', 'post', '');
    $data['tags_alias_lower'] = $nv_Request->get_int('tags_alias_lower', 'post', 0);
    $data['tags_alias'] = $nv_Request->get_int('tags_alias', 'post', 0);
    $data['auto_tags'] = $nv_Request->get_int('auto_tags', 'post', 0);
    $data['tags_remind'] = $nv_Request->get_int('tags_remind', 'post', 0);
    $data['usergrouppost'] = $nv_Request->get_typed_array('usergrouppost', 'post', 'int');
    $data['usergrouppost'] = !empty($data['usergrouppost']) ? implode(',', $data['usergrouppost']) : '';
    $data['similar_content'] = $nv_Request->get_int('similar_content', 'post', 80);
    $data['similar_time'] = $nv_Request->get_int('similar_time', 'post', 5);
    $data['fb_enable'] = $nv_Request->get_int('fb_enable', 'post', 0);
    $data['payport'] = $nv_Request->get_int('payport', 'post', 0);
    $data['editor_guest'] = $nv_Request->get_int('editor_guest', 'post', 0);
    $data['maps_appid'] = $nv_Request->get_title('maps_appid', 'post', '');
    $data['priceformat'] = $nv_Request->get_int('priceformat', 'post', 0);

    $data['home_image_size_w'] = $nv_Request->get_int('home_image_size_w', 'post', 150);
    $data['home_image_size_h'] = $nv_Request->get_int('home_image_size_h', 'post', 150);
    $data['home_image_size'] = $data['home_image_size_w'] . 'x' . $data['home_image_size_h'];

    $data['maxsizeimage_w'] = $nv_Request->get_int('maxsizeimage_w', 'post', 250);
    $data['maxsizeimage_h'] = $nv_Request->get_int('maxsizeimage_h', 'post', 125);
    $data['maxsizeimage'] = $data['maxsizeimage_w'] . 'x' . $data['maxsizeimage_h'];

    $data['grouppost'] = $nv_Request->get_typed_array('grouppost', 'post', 'int');
    $data['grouppost'] = !empty($data['grouppost']) ? implode(',', $data['grouppost']) : '';

    $data['grouppostconfig'] = $nv_Request->get_array('grouppostconfig', 'post', array());
    $data['grouppostconfig'] = !empty($data['grouppostconfig']) ? serialize($data['grouppostconfig']) : '';

    $data['auction'] = $nv_Request->get_int('auction', 'post', 0);
    $_groups_post = $nv_Request->get_array('auction_group', 'post', array());
    $data['auction_group'] = !empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';
    $data['auction_register_time'] = $nv_Request->get_int('auction_register_time', 'post', 1440);
    $data['auction_firebase_url'] = $nv_Request->get_title('auction_firebase_url', 'post', '');

    $data['refresh_allow'] = $nv_Request->get_int('refresh_allow', 'post', 0);
    $data['refresh_default'] = $nv_Request->get_int('refresh_default', 'post', 0);
    $data['refresh_free'] = $nv_Request->get_int('refresh_free', 'post', 0);
    $data['refresh_timelimit'] = $nv_Request->get_int('refresh_timelimit', 'post', 0);
    $data['refresh_config'] = $nv_Request->get_array('refresh_config', 'post');
    if (!empty($data['refresh_config'])) {
        foreach ($data['refresh_config'] as $index => $config) {
            if (empty($config['number']) or empty($config['price'])) {
                unset($data['refresh_config'][$index]);
            }
        }
    }
    $data['refresh_config'] = !empty($data['refresh_config']) ? serialize($data['refresh_config']) : '';

    $data['specialgroup_config'] = $nv_Request->get_array('specialgroup_config', 'post');
    if (!empty($data['specialgroup_config'])) {
        foreach ($data['specialgroup_config'] as $groupid => $config) {
            foreach ($config as $index => $value) {
                if (empty($value['time']) or empty($value['price'])) {
                    unset($data['specialgroup_config'][$groupid][$index]);
                }
            }
        }
    }
    $data['specialgroup_config'] = !empty($data['specialgroup_config']) ? serialize($data['specialgroup_config']) : '';

    $sth = $db->prepare("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET config_value = :config_value WHERE lang = '" . NV_LANG_DATA . "' AND module = :module_name AND config_name = :config_name");
    $sth->bindParam(':module_name', $module_name, PDO::PARAM_STR);
    foreach ($data as $config_name => $config_value) {
        $sth->bindParam(':config_name', $config_name, PDO::PARAM_STR);
        $sth->bindParam(':config_value', $config_value, PDO::PARAM_STR);
        $sth->execute();
    }

    nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['config'], "Config", $admin_info['userid']);
    $nv_Cache->delMod('settings');

    Header("Location: " . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . '=' . $op);
    die();
}

$array_block_cat_module = array();
$sql = 'SELECT bid, title, useradd FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block_cat ORDER BY weight ASC';
$result = $db->query($sql);
while ($_row = $result->fetch()) {
    $array_block_cat_module[$_row['bid']] = $_row;
}

$array_config['ck_allow_auto_code'] = $array_config['allow_auto_code'] ? 'checked="checked"' : '';
$array_config['ck_auction'] = $array_config['auction'] ? 'checked="checked"' : '';
$array_config['ck_refresh_allow'] = $array_config['refresh_allow'] ? 'checked="checked"' : '';
$array_config['ck_tags_alias_lower'] = $array_config['tags_alias_lower'] ? 'checked="checked"' : '';
$array_config['ck_tags_alias'] = $array_config['tags_alias'] ? 'checked="checked"' : '';
$array_config['ck_auto_tags'] = $array_config['auto_tags'] ? 'checked="checked"' : '';
$array_config['ck_tags_remind'] = $array_config['tags_remind'] ? 'checked="checked"' : '';
$array_config['ck_fb_enable'] = $array_config['fb_enable'] ? 'checked="checked"' : '';
$array_config['ck_editor_guest'] = $array_config['editor_guest'] ? 'checked="checked"' : '';

list ($array_config['home_image_size_w'], $array_config['home_image_size_h']) = explode('x', $array_config['home_image_size']);
list ($array_config['maxsizeimage_w'], $array_config['maxsizeimage_h']) = explode('x', $array_config['maxsizeimage']);

$xtpl = new XTemplate($op . ".tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('DATA', $array_config);
$xtpl->assign('DES_POINT', $array_config['des_point']);
$xtpl->assign('THOUSANDS_SEP', $array_config['thousands_sep']);

foreach ($groups_list as $group_id => $grtl) {
    if ($group_id >= 10) {
        $_groups = array(
            'value' => $group_id,
            'checked' => $array_config['freelancegroup'] == $group_id ? ' checked="checked"' : '',
            'title' => $grtl
        );
        $xtpl->assign('FREELANCEGROUP', $_groups);
        $xtpl->parse('main.freelancegroup');
    }
}

$array_homedata = array(
    1 => $lang_module['config_homedata_1'],
    2 => $lang_module['config_homedata_2'],
    0 => $lang_module['config_homedata_0']
);
foreach ($array_homedata as $index => $value) {
    $sl = $index == $array_config['homedata'] ? 'selected="selected"' : '';
    $xtpl->assign('HOMEDATA', array(
        'index' => $index,
        'value' => $value,
        'selected' => $sl
    ));
    $xtpl->parse('main.homedata');
}

$array_hometype = array(
    'viewgrid' => $lang_module['config_hometype_viewgrid'],
    'viewlist' => $lang_module['config_hometype_viewlist'],
    'viewlist_simple' => $lang_module['config_hometype_viewgrid_simple']
);
foreach ($array_hometype as $index => $value) {
    $sl = $index == $array_config['hometype'] ? 'selected="selected"' : '';
    $xtpl->assign('HOMETYPE', array(
        'index' => $index,
        'value' => $value,
        'selected' => $sl
    ));
    $xtpl->parse('main.hometype');
}

foreach ($array_hometype as $index => $value) {
    $sl = $index == $array_config['style_default'] ? 'selected="selected"' : '';
    $xtpl->assign('STYLE_DEFAULT', array(
        'index' => $index,
        'value' => $value,
        'selected' => $sl
    ));
    $xtpl->parse('main.style_default');
}

$array_structure_image = array();
$array_structure_image[''] = NV_UPLOADS_DIR . '/' . $module_upload;
$array_structure_image['Y'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date('Y');
$array_structure_image['Ym'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date('Y_m');
$array_structure_image['Y_m'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date('Y/m');
$array_structure_image['Ym_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date('Y_m/d');
$array_structure_image['Y_m_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date('Y/m/d');
$array_structure_image['username'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username';
$array_structure_image['username_Y'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username/' . date('Y');
$array_structure_image['username_Ym'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username/' . date('Y_m');
$array_structure_image['username_Y_m'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username/' . date('Y/m');
$array_structure_image['username_Ym_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username/' . date('Y_m/d');
$array_structure_image['username_Y_m_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username/' . date('Y/m/d');
$structure_image_upload = isset($array_config['structure_upload']) ? $array_config['structure_upload'] : "Ym";

foreach ($array_structure_image as $type => $dir) {
    $xtpl->assign('STRUCTURE_UPLOAD', array(
        'key' => $type,
        'title' => $dir,
        'selected' => $type == $structure_image_upload ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.structure_upload');
}

if (!empty($array_config['grouppostconfig'])) {
    $array_config['grouppostconfig'] = unserialize($array_config['grouppostconfig']);
    foreach ($array_config['grouppostconfig'] as $groupid => $groupconfig) {
        $groupconfig['ck_queue'] = (isset($groupconfig['queue']) and $groupconfig['queue']) ? 'checked="checked"' : '';
        $groupconfig['ck_queue_edit'] = (isset($groupconfig['queue_edit']) and $groupconfig['queue_edit']) ? 'checked="checked"' : '';
        $groupconfig['title'] = $groups_list[$groupid];
        $xtpl->assign('GROUPCONFIG', $groupconfig);
        $xtpl->parse('main.groupconfig');
    }
}
$xtpl->assign('COUNT', count($array_config['grouppostconfig']));

$array_config['grouppost'] = explode(',', $array_config['grouppost']);
foreach ($groups_list as $group_id => $grtl) {
    $_groups = array(
        'value' => $group_id,
        'checked' => in_array($group_id, $array_config['grouppost']) ? ' checked="checked"' : '',
        'title' => $grtl
    );
    $xtpl->assign('GROUPPOST', $_groups);
    $xtpl->parse('main.grouppost');
}

$sys_max_size = min(nv_converttoBytes(ini_get('upload_max_filesize')), nv_converttoBytes(ini_get('post_max_size')));
$p_size = $sys_max_size / 100;
for ($index = 1; $index <= 100; ++$index) {
    $size = floor($index * $p_size);

    $xtpl->assign('SIZE', array(
        'key' => $size,
        'title' => nv_convertfromBytes($size),
        'selected' => ($size == $array_config['maxsizeupload']) ? ' selected="selected"' : ''
    ));

    $xtpl->parse('main.maxfilesize');
}

$array_config['auction_group'] = explode(',', $array_config['auction_group']);
foreach ($groups_list as $group_id => $grtl) {
    $_groups = array(
        'value' => $group_id,
        'checked' => in_array($group_id, $array_config['auction_group']) ? 'checked="checked"' : '',
        'title' => $grtl
    );
    $xtpl->assign('AUCTION_GROUP', $_groups);
    $xtpl->parse('main.auction_group');
}

$array_config['usergrouppost'] = !empty($array_config['usergrouppost']) ? explode(',', $array_config['usergrouppost']) : array();
if (!empty($array_block_cat_module)) {
    foreach ($array_block_cat_module as $index => $value) {
        $ck = in_array($index, $array_config['usergrouppost']) ? 'checked="checked"' : '';
        $xtpl->assign('USERGROUPPOST', array(
            'index' => $index,
            'value' => $value['title'],
            'checked' => $ck
        ));
        $xtpl->parse('main.usergrouppost');
    }
}

// Lam moi tin
if (!empty($array_config['refresh_config'])) {
    $array_config['refresh_config'] = unserialize($array_config['refresh_config']);
} else {
    $array_config['refresh_config'][0] = array(
        'number' => '',
        'price' => ''
    );
}

foreach ($array_config['refresh_config'] as $index => $config) {
    $config['index'] = $index;
    $xtpl->assign('REFRESH_CONFIG', $config);
    $xtpl->parse('main.refresh_config');
}
$xtpl->assign('REFRESH_COUNT', sizeof($array_config['refresh_config']));

$array_config['specialgroup_config'] = !empty($array_config['specialgroup_config']) ? unserialize($array_config['specialgroup_config']) : array();
if (!empty($array_block_cat_module)) {
    foreach ($array_block_cat_module as $index => $value) {
        if ($value['useradd']) {
            $xtpl->assign('SPECIALGROUP', array(
                'index' => $index,
                'value' => $value['title'],
                'count' => sizeof($array_config['specialgroup_config'][$index])
            ));

            if (!isset($array_config['specialgroup_config'][$index]) or empty($array_config['specialgroup_config'][$index])) {
                $array_config['specialgroup_config'][$index][0] = array(
                    'time' => '',
                    'price' => ''
                );
            }

            $number = 0;
            foreach ($array_config['specialgroup_config'][$index] as $specialgroup_config) {
                $specialgroup_config['number'] = $number++;
                $xtpl->assign('SPECIALGROUP_CONFIG', $specialgroup_config);
                $xtpl->parse('main.specialgroup.specialgroup_config');
            }

            $xtpl->parse('main.specialgroup');
        }
    }
}

// hình thức thanh toán
$array_payport = array();
if (isset($site_mods['taikhoan'])) {
    $array_payport = array(
        1 => $lang_module['config_payport_taikhoan']
    );
}
if (!empty($array_payport)) {
    foreach ($array_payport as $index => $value) {
        $sl = $index == $array_config['payport'] ? 'selected="selected"' : '';
        $xtpl->assign('PAYPORT', array(
            'index' => $index,
            'value' => $value,
            'selected' => $sl
        ));
        $xtpl->parse('main.payport');
    }
}

$array_price_format = array(
    0 => '15 ' . $lang_module['million'],
    1 => '15 ' . $lang_module['million'] . ' / ' . $lang_global['month'],
    2 => '15.000.000',
    3 => '15,000,000'
);
foreach ($array_price_format as $index => $value) {
    $sl = $index == $array_config['priceformat'] ? 'selected="selected"' : '';
    $xtpl->assign('PFORMAT', array(
        'index' => $index,
        'value' => $value,
        'selected' => $sl
    ));
    $xtpl->parse('main.priceformat');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';