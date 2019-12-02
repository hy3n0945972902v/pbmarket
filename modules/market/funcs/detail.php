<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (!defined('NV_IS_MOD_MARKET')) die('Stop!!!');

if ($nv_Request->isset_request('delete', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $checkss = $nv_Request->get_title('checkss', 'post', '');

    if (empty($id) or $checkss != md5($id . NV_CACHE_PREFIX . $client_info['session_id'])) {
        die('NO');
    }
    nv_delete_rows($id);
    die('OK');
}

$rows = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ') AND id=' . $id)->fetch();

if (empty($rows) or !nv_user_in_groups($array_market_cat[$catid]['groups_view']) or !nv_user_in_groups($rows['groupview'])) {
    $redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true) . '" />';
    nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect);
}

$base_url_rewrite = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_market_cat[$rows['catid']]['alias'] . '/' . $rows['alias'] . '-' . $rows['id'] . $global_config['rewrite_exturl'], true);
if ($_SERVER['REQUEST_URI'] == $base_url_rewrite) {
    $canonicalUrl = NV_MAIN_DOMAIN . $base_url_rewrite;
} elseif (NV_MAIN_DOMAIN . $_SERVER['REQUEST_URI'] != $base_url_rewrite) {
    // chuyen huong neu doi alias
    header('HTTP/1.1 301 Moved Permanently');
    Header('Location: ' . $base_url_rewrite);
    die();
} else {
    $canonicalUrl = $base_url_rewrite;
}

$detail = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_detail WHERE id=' . $rows['id'])->fetch();
$rows = array_merge($rows, $detail);
unset($detail);

require_once NV_ROOTDIR . '/modules/location/location.class.php';
$location = new Location();
$module_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;

$rows['addtimef'] = nv_date('H:i d/m/Y', $rows['addtime']);
$rows['cat'] = $array_market_cat[$rows['catid']]['title'];
$rows['cat_link'] = $array_market_cat[$rows['catid']]['link'];
$rows['location'] = $location->locationString($rows['area_p'], $rows['area_d'], 0, ' » ');
$rows['location_link'] = nv_market_build_search_url($module_name, $rows['typeid'], $rows['catid'], $rows['area_p'], $rows['area_d']);
$rows['type'] = !empty($rows['typeid']) ? $array_type[$rows['typeid']]['title'] : '';
$rows['price'] = nv_market_get_price($rows['price'], $rows['price1'], $rows['catid'], $rows['pricetype'], $rows['unitid']);

// Hinh anh
$rows['images'] = array();
$result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_images WHERE rowsid=' . $rows['id']);
while ($_row = $result->fetch()) {
    if (file_exists(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_upload . '/' . $_row['path'])) {
        $_row['thumb'] = NV_BASE_SITEURL . NV_ASSETS_DIR . '/' . $module_upload . '/' . $_row['path'];
    } else {
        $_row['thumb'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $_row['path'];
    }

    if ($_row['is_main']) {
        $meta_property['og:image'] = NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $_row['path'];
    }
    $_row['full'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $_row['path'];
    $_row['path'] = nv_resize_crop_images(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $_row['path'], 530, 384, $module_name, $rows['id']);

    $rows['images'][] = $_row;
}

// So luot xem tin
$time_set = $nv_Request->get_int($module_data . '_' . $op . '_' . $rows['id'], 'session');
if (empty($time_set)) {
    $nv_Request->set_Session($module_data . '_' . $op . '_' . $rows['id'], NV_CURRENTTIME);
    $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET countview=countview+1 WHERE id=' . $rows['id']);
}

// Kiem tra luu tin
$rows['is_user'] = 0;
$rows['style_save'] = $rows['style_saved'] = '';
if (defined('NV_IS_USER')) {
    $rows['is_user'] = 1;
    $count = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_saved WHERE rowsid=' . $rows['id'] . ' AND userid=' . $user_info['userid'])->fetchColumn();
    if ($count) {
        $rows['style_save'] = 'style="display: none"';
    } else {
        $rows['style_saved'] = 'style="display: none"';
    }
} else {
    $rows['style_saved'] = 'style="display: none"';
}

// Tin cung chu de
$rows_other = array();
$array_catid = nv_GetCatidInParent($rows['catid']);
$result = $db->query('SELECT t1.id, title, alias, catid, area_p, area_d, typeid, pricetype, price, price1, unitid, homeimgfile, homeimgalt, homeimgthumb, countview, countcomment, groupview, addtime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, groups_config, t2.contact_fullname, t2.contact_phone, t2.contact_email, t2.contact_address FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows t1 INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_detail t2 ON t1.id = t2.id WHERE catid IN (' . implode(',', $array_catid) . ') AND t1.id!=' . $rows['id'] . ' AND status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ') ORDER BY ordertime DESC LIMIT ' . $array_config['numother']);
while ($_row = $result->fetch()) {
    if (nv_user_in_groups($_row['groupview'])) {
        if (!empty($data = nv_market_data($_row, $module_name))) {
            $rows_other[$_row['id']] = $data;
        }
    }
}

// thong tin dau gia
if (nv_check_auction($rows['auction'], $rows['auction_begin'], $rows['auction_end'], $rows['auction_price_begin'], $rows['auction_price_step'])) {

    $rows['countdown_begin'] = nv_date('Y/m/d H:i:s', $rows['auction_begin']);
    $rows['countdown_end'] = nv_date('Y/m/d H:i:s', $rows['auction_end']);

    $rows['auction_status'] = nv_auction_status($rows['auction_begin'], $rows['auction_end']);
    if ($rows['auction_status'] == 1) {
        $lang_module['auction_begin_note'] = $lang_module['auction_end_note'];
        $rows['countdown_begin'] = $rows['countdown_end'];
    }

    $rows['auction_begin_str'] = nv_date('H:i d/m/Y', $rows['auction_begin']);
    $rows['auction_end_str'] = nv_date('H:i d/m/Y', $rows['auction_end']);
    $rows['auction_price_begin_str'] = nv_market_number_format($rows['auction_price_begin']);

    $rows['auction_registed'] = 0;
    if (defined('NV_IS_USER')) {
        $count = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_auction_register WHERE userid=' . $user_info['userid'] . ' AND rowsid=' . $id)->fetchColumn();
        if ($count) {
            $rows['auction_registed'] = 1;
        }
    }
} else {
    $rows['auction'] = 0;
}

$meta_property['og:type'] = 'article';
$meta_property['article:published_time'] = date('Y-m-dTH:i:s', $rows['addtime']);
$meta_property['article:modified_time'] = !empty($rows['edittime']) ? date('Y-m-dTH:i:s', $rows['edittime']) : '';
$meta_property['article:section'] = $array_market_cat[$rows['catid']]['title'];

if ($array_config['auto_link']) {
    $reg_post = $array_config['auto_link_casesens'] ? '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))($content)/imsu' : '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))($content)/msu';
    $content = $rows['content'];

    $sql = 'SELECT keywords, alias FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags';
    $array_keyword = $nv_Cache->db($sql, 'tid', $module_name);
    foreach ($array_keyword as $keyword) {
        $url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['tag'] . '/' . $keyword['alias'];
        $regexp = str_replace('$content', $keyword['keywords'], $reg_post);
        $replace = '<a title="$1" href="$$$url$$$" ' . (!empty($keyword['auto_link_target']) ? 'target="' . $keyword['auto_link_target'] . '"' : '') . '>$1</a>';
        $newtext = preg_replace($regexp, $replace, $content, $array_config['auto_link_limit']);

        if ($newtext != $keyword['keywords']) {
            $content = str_replace('$$$url$$$', $url, $newtext);
        }
    }
    $rows['content'] = $content;
    unset($content);
}

$array_keyword = array();
$key_words = array();
$_query = $db_slave->query('SELECT a1.keyword, a2.alias FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id a1 INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_tags a2 ON a1.tid=a2.tid WHERE a1.id=' . $rows['id']);
while ($row = $_query->fetch()) {
    $array_keyword[] = $row;
    $key_words[] = $row['keyword'];
    $meta_property['article:tag'][] = $row['keyword'];
}

$rows['is_admin'] = 0;
if (defined('NV_IS_USER')) {
    if ($rows['userid'] == $user_info['userid']) {
        $rows['is_admin'] = 1;
        $rows['link_edit'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['content'] . '&amp;id=' . $rows['id'] . '&amp;redirect=' . nv_redirect_encrypt($client_info['selfurl']);
        $rows['link_delete'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['userarea'] . '&amp;delete_id=' . $rows['id'] . '&amp;delete_checkss=' . md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . $rows['id']);
    } elseif (defined('NV_IS_MODADMIN')) {
        $rows['is_admin'] = 1;
        $rows['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&amp;id=' . $rows['id'] . '&amp;redirect=' . nv_redirect_encrypt($client_info['selfurl']);
        $rows['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=main&amp;delete_id=' . $rows['id'] . '&amp;delete_checkss=' . md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . $rows['id']);
    }
}

$page_title = !empty($rows['title_custom']) ? $rows['title_custom'] : $rows['title'];
if (!empty($rows['description'])) {
    $description = $rows['description'];
} else {
    $description = $rows['title'] . ' - ' . $global_config['site_description'];
}

$key_words = !empty($key_words) ? implode(',', $key_words) : '';
$catid = $rows['catid'];

$lang_module['price'] = $lang_module['pricetype_cat_title_' . $array_market_cat[$rows['catid']]['pricetype']];

// custom field
$rows['custom_field'] = array();
if ($array_market_cat[$rows['catid']]['form'] != '') {
    $idtemplate = $db->query('SELECT id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_template where alias = "' . preg_replace("/[\_]/", "-", $array_market_cat[$rows['catid']]['form']) . '"')->fetchColumn();
    if ($idtemplate) {
        $listfield = $array_field_config = array();
        $result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_field ORDER BY weight');
        while ($row_field = $result->fetch()) {
            $listtemplate = explode(',', $row_field['listtemplate']);
            if (in_array($idtemplate, $listtemplate)) {
                $language = unserialize($row_field['language']);
                $row_field['title'] = (isset($language[NV_LANG_DATA])) ? $language[NV_LANG_DATA][0] : $row_field['field'];
                $row_field['description'] = (isset($language[NV_LANG_DATA])) ? nv_htmlspecialchars($language[NV_LANG_DATA][1]) : '';
                if (!empty($row_field['field_choices'])) {
                    $row_field['field_choices'] = unserialize($row_field['field_choices']);
                } elseif (!empty($row_field['sql_choices'])) {
                    $row_field['field_choices'] = array();
                    $row_field['sql_choices'] = explode(',', $row_field['sql_choices']);
                    $query = 'SELECT ' . $row_field['sql_choices'][2] . ', ' . $row_field['sql_choices'][3] . ' FROM ' . $row_field['sql_choices'][1];
                    $result = $db->query($query);
                    $weight = 0;
                    while (list ($key, $val) = $result->fetch(3)) {
                        $row_field['field_choices'][$key] = $val;
                    }
                }
                $array_field_config[$row_field['field']] = $row_field;
            }
        }

        $rows['template'] = $array_market_cat[$rows['catid']]['form'];
        $result = $db->query("SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_info WHERE rowid=" . $rows['id']);
        $custom_fields = $result->fetch();

        if (!empty($array_field_config)) {
            foreach ($array_field_config as $row) {
                $row['value'] = (isset($custom_fields[$row['field']])) ? $custom_fields[$row['field']] : $row['default_value'];
                if (empty($display_empty) && empty($row['value'])) continue;
                if ($row['field_type'] == 'date') {
                    if (!preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $row['value'], $m)) {
                        $row['value'] = (empty($row['value'])) ? '' : date('d/m/Y', $row['value']);
                    }
                } elseif ($row['field_type'] == 'textarea') {
                    $row['value'] = nv_htmlspecialchars(nv_br2nl($row['value']));
                } elseif ($row['field_type'] == 'editor') {
                    $row['value'] = htmlspecialchars(nv_editor_br2nl($row['value']));
                } elseif ($row['field_type'] == 'select' || $row['field_type'] == 'radio') {
                    $row['value'] = isset($row['field_choices'][$row['value']]) ? $row['field_choices'][$row['value']] : '';
                } elseif ($row['field_type'] == 'checkbox' || $row['field_type'] == 'multiselect') {
                    $row['value'] = !empty($row['value']) ? explode(',', $row['value']) : array();
                    $str = array();
                    if (!empty($row['value'])) {
                        foreach ($row['value'] as $value) {
                            if (isset($row['field_choices'][$value])) {
                                $str[] = $row['field_choices'][$value];
                            }
                        }
                    }
                    $row['value'] = implode(', ', $str);
                }
                $rows['custom_field'][$row['field']] = $row;
            }
        }
    }
}

if (!empty($array_config['maps_appid']) && $rows['display_maps']) {
    $rows['maps'] = !empty($rows['maps']) ? unserialize($rows['maps']) : array();
} else {
    $rows['maps'] = array();
}

$contents = nv_theme_market_detail($rows, $rows_other, $array_keyword);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';