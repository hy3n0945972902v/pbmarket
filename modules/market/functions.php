<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (!defined('NV_SYSTEM')) die('Stop!!!');

define('NV_IS_MOD_MARKET', true);
define('NV_USER_LOGS_PATH', NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_name . '/user_logs');

require_once NV_ROOTDIR . '/modules/' . $module_file . '/site.functions.php';
require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';

$page = 1;
$per_page = $array_config['per_page'];
$id = $catid = $parentid = 0;
$alias_cat_url = isset($array_op[0]) ? $array_op[0] : '';
$array_mod_title = array();
$url_string = !empty($array_op) ? $array_op[0] : '';
$array_search_params = array(
    'typeid' => 0,
    'catid' => 0,
    'provinceid' => 0,
    'districtid' => 0
);

foreach ($array_market_cat as $row) {
    $array_market_cat[$row['id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $row['alias'];
    if ($alias_cat_url == $row['alias']) {
        $catid = $row['id'];
        $parentid = $row['parentid'];
    }
}

if (!empty($url_string)) {
    if (preg_match('/-tinh-([a-zA-Z0-9-]*)(\-?)(\-?)/i', $url_string, $m) || preg_match('/-thanh-pho-([a-zA-Z0-9-]*)(\-?)(\-?)/i', $url_string, $m)) {
        $array_search_params['provinceid'] = $db->query('SELECT provinceid FROM ' . $db_config['prefix'] . '_location_province WHERE alias=' . $db->quote($m[1]))
            ->fetchColumn();
        $url_string = str_replace($m[0], '', $url_string);
        $op = 'search';
    }

    if (preg_match('/-huyen-([a-zA-Z0-9-]*)(\-?)(\-?)/i', $url_string, $m) || preg_match('/-quan-([a-zA-Z0-9-]*)(\-?)(\-?)/i', $url_string, $m) || preg_match('/-thi-xa-([a-zA-Z0-9-]*)(\-?)(\-?)/i', $url_string, $m) || preg_match('/-thanh-pho-([a-zA-Z0-9-]*)(\-?)(\-?)/i', $url_string, $m)) {
        $array_search_params['districtid'] = $db->query('SELECT districtid FROM ' . $db_config['prefix'] . '_location_district WHERE alias=' . $db->quote($m[1]))
            ->fetchColumn();
        $url_string = str_replace($m[0], '', $url_string);
        $op = 'search';
    }

    foreach ($array_market_cat as $row) {
        $array_cat_alias[] = $row['alias'];
        $array_market_cat[$row['id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $row['alias'];
        if (preg_match('/' . $row['alias'] . '$/i', $url_string)) {
            $array_search_params['catid'] = $row['id'];
            break;
        }
    }

    foreach ($array_type as $type) {
        if (!empty($url_string) && preg_match('/^' . $type['alias'] . '/i', $url_string)) {
            $op = 'search';
            $array_search_params['typeid'] = $type['id'];
            break;
        }
    }
}

$count_op = sizeof($array_op);
$array_location_code = array(
    'w',
    'p',
    'd'
);

if ($op == 'main') {
    if (empty($catid) and count($array_op) < 2) {
        if (preg_match('/^page\-([0-9]+)$/', (isset($array_op[0]) ? $array_op[0] : ''), $m)) {
            $page = (int) $m[1];
        }
    } else {
        if (in_array($array_op[0], $array_location_code) and preg_match('/^([a-z0-9\-]+)\-([0-9]+)$/i', $array_op[1], $m3)) {
            $op = 'viewlocation';
            $id = $m3[2];
            if (isset($array_op[2]) and substr($array_op[2], 0, 5) == 'page-') {
                $page = intval(substr($array_op[2], 5));
            }
        } elseif (sizeof($array_op) == 2 and preg_match('/^([a-z0-9\-]+)\-([0-9]+)$/i', $array_op[1], $m1) and !preg_match('/^page\-([0-9]+)$/', $array_op[1], $m2)) {
            $op = 'detail';
            $id = $m1[2];
        } else {
            $op = 'viewcat';
            if (isset($array_op[1]) and substr($array_op[1], 0, 5) == 'page-') {
                $page = intval(substr($array_op[1], 5));
            }
        }

        $parentid = $catid;
        while ($parentid > 0) {
            $array_cat_i = $array_market_cat[$parentid];
            $array_mod_title[] = array(
                'catid' => $parentid,
                'title' => $array_cat_i['title'],
                'link' => $array_cat_i['link']
            );
            $parentid = $array_cat_i['parentid'];
        }
        sort($array_mod_title, SORT_NUMERIC);
    }
}

if (defined('NV_IS_USER')) {
    $result = $db->query('SELECT * FROM ' . NV_USERS_GLOBALTABLE . '_info WHERE userid=' . $user_info['userid']);
    $custom_fields = $result->fetch();

    $array_field_config = array();
    $result_field = $db->query('SELECT * FROM ' . NV_USERS_GLOBALTABLE . '_field WHERE user_editable = 1 ORDER BY weight ASC');
    while ($row_field = $result_field->fetch()) {
        $language = unserialize($row_field['language']);
        $row_field['title'] = (isset($language[NV_LANG_DATA])) ? $language[NV_LANG_DATA][0] : '';
        $row_field['description'] = (isset($language[NV_LANG_DATA])) ? nv_htmlspecialchars($language[NV_LANG_DATA][1]) : '';

        if (!empty($row_field['field_choices'])) {
            $row_field['field_choices'] = unserialize($row_field['field_choices']);
        } elseif (!empty($row_field['sql_choices'])) {
            $row_field['sql_choices'] = explode('|', $row_field['sql_choices']);
            $sql = 'SELECT ' . $row_field['sql_choices'][2] . ', ' . $row_field['sql_choices'][3] . ' FROM ' . $row_field['sql_choices'][1];
            $result = $db->query($sql);

            $weight = 0;
            while (list ($key, $val) = $result->fetch(3)) {
                $row_field['field_choices'][$key] = $val;
            }
        }
        $array_field_config[] = $row_field;
    }

    if (!empty($array_field_config)) {
        foreach ($array_field_config as $_row) {
            if ($_row['show_profile']) {
                $question_type = $_row['field_type'];
                if ($question_type == 'checkbox') {
                    $result = explode(',', $custom_fields[$_row['field']]);
                    $value = '';
                    foreach ($result as $item) {
                        $value .= $_row['field_choices'][$item] . '<br />';
                    }
                } elseif ($question_type == 'multiselect' or $question_type == 'select' or $question_type == 'radio') {
                    $value = $_row['field_choices'][$custom_fields[$_row['field']]];
                    $user_info[$_row['field'] . 'id'] = (int) $custom_fields[$_row['field']];
                } else {
                    $value = isset($custom_fields[$_row['field']]) ? $custom_fields[$_row['field']] : '';
                }
                $user_info[$_row['field']] = $value;
            }
        }
    }

    $user_info['fullname'] = nv_show_name_user($user_info['first_name'], $user_info['last_name'], $user_info['username']);
}

function nv_resize_crop_images($img_path, $width, $height, $module_name = '', $id = 0)
{
    $new_img_path = str_replace(NV_ROOTDIR, '', $img_path);
    if (file_exists($img_path)) {
        $imginfo = nv_is_image($img_path);
        $basename = basename($img_path);
        $basename = preg_replace('/^\W+|\W+$/', '', $basename);
        $basename = preg_replace('/[ ]+/', '_', $basename);
        $basename = strtolower(preg_replace('/\W-/', '', $basename));
        if ($imginfo['width'] > $width or $imginfo['height'] > $height) {
            $basename = preg_replace('/(.*)(\.[a-zA-Z]+)$/', $module_name . '_' . $id . '_\1_' . $width . '-' . $height . '\2', $basename);
            if (file_exists(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $basename)) {
                $new_img_path = NV_BASE_SITEURL . NV_TEMP_DIR . '/' . $basename;
            } else {
                $img_path = new NukeViet\Files\Image($img_path, NV_MAX_WIDTH, NV_MAX_HEIGHT);

                $thumb_width = $width;
                $thumb_height = $height;
                $maxwh = max($thumb_width, $thumb_height);
                if ($img_path->fileinfo['width'] > $img_path->fileinfo['height']) {
                    $width = 0;
                    $height = $maxwh;
                } else {
                    $width = $maxwh;
                    $height = 0;
                }

                $img_path->resizeXY($width, $height);
                $img_path->cropFromCenter($thumb_width, $thumb_height);
                $img_path->save(NV_ROOTDIR . '/' . NV_TEMP_DIR, $basename);
                if (file_exists(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $basename)) {
                    $new_img_path = NV_BASE_SITEURL . NV_TEMP_DIR . '/' . $basename;
                }
            }
        }
    }
    return $new_img_path;
}

function nv_user_config($convert = false)
{
    global $global_config, $array_config;

    $config = array();

    if (defined('NV_IS_MODADMIN')) {
        $config['max_width'] = NV_MAX_WIDTH;
        $config['max_height'] = NV_MAX_HEIGHT;
        $config['max_filesize'] = min($global_config['nv_max_size'], nv_converttoBytes(ini_get('upload_max_filesize')), nv_converttoBytes(ini_get('post_max_size')));
    } else {
        list ($config['max_width'], $config['max_height']) = explode('x', $array_config['maxsizeimage']);
        $config['max_filesize'] = $array_config['maxsizeupload'];
    }

    if ($convert) {
        $config['max_filesize'] = nv_convertfromBytes($config['max_filesize']);
    }

    return $config;
}

function nv_group_premission()
{
    global $array_config, $user_info;

    $grouppostconfig = $array_config['grouppostconfig'];

    if (!empty($array_config['grouppost']) and !empty($grouppostconfig)) {
        $grouppostconfig = unserialize($grouppostconfig);
    }

    $premission = array();

    if (!defined('NV_IS_USER')) {
        $premission = $grouppostconfig[5];
    } else {
        // ưu tiên nhóm chính
        if (isset($grouppostconfig[$user_info['group_id']])) {
            $premission = $grouppostconfig[$user_info['group_id']];
        } else {
            $array_tmp = array();

            foreach ($user_info['in_groups'] as $groupid) {
                if (isset($grouppostconfig[$groupid])) {
                    $array_tmp['queue'][] = $grouppostconfig[$groupid]['queue'];
                    $array_tmp['maxpost'][] = $grouppostconfig[$groupid]['maxpost'];
                }
            }

            if (!empty($array_tmp)) {
                $premission = array(
                    'queue' => min($array_tmp['queue']),
                    'maxpost' => max($array_tmp['maxpost'])
                );
                unset($array_tmp);
            }
        }
    }

    return $premission;
}

function nv_delete_saved($id)
{
    global $db, $module_data, $user_info;

    $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_saved WHERE rowsid=' . $id . ' AND userid=' . $user_info['userid']);
}

function nv_check_auction($auction, $auction_begin, $auction_end, $auction_price_begin, $auction_price_step)
{
    global $array_config;

    if ($array_config['auction'] and $auction and !empty($auction_begin) and !empty($auction_end) and !empty($auction_price_begin) and !empty($auction_price_step)) {
        return true;
    }

    return false;
}

function nv_get_mail_admin()
{
    global $db_slave;

    $array_email = array();
    $array_userid = array(
        1
    );
    $array_userid = array_unique($array_userid);

    if (!empty($array_userid)) {
        $result = $db_slave->query('SELECT email FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid IN (' . implode(',', $array_userid) . ')');
        while (list ($email) = $result->fetch(3)) {
            $array_email[] = $email;
        }
    }

    return $array_email;
}

function nv_count_refresh($module)
{
    global $db, $user_info, $site_mods, $array_config;

    $count_refresh = $db->query('SELECT count FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_refresh WHERE userid=' . $user_info['userid'])->fetchColumn();
    if ($count_refresh === false) {
        $count_refresh = $array_config['refresh_default'];
    }

    return $count_refresh;
}

function nv_count_refresh_free($module)
{
    global $db, $user_info, $site_mods, $array_config;

    if (empty($array_config['refresh_free'])) {
        return 0;
    }

    $currentdate = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
    $count_refresh = $db->query('SELECT free FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_refresh WHERE userid=' . $user_info['userid'] . ' AND free_time > 0 AND free_time=' . $currentdate)->fetchColumn();

    if ($count_refresh === false) {
        $count_refresh = $array_config['refresh_free'];
    }

    return $count_refresh;
}

function nv_image_logo($fileupload)
{
    global $global_config, $module_upload;

    if (empty($fileupload) or !file_exists($fileupload)) {
        return;
    }

    $autologomod = explode(',', $global_config['autologomod']);
    if ($global_config['autologomod'] == 'all' or in_array($module_upload, $autologomod)) {
        if (!empty($global_config['upload_logo']) and file_exists(NV_ROOTDIR . '/' . $global_config['upload_logo'])) {
            $logo_size = getimagesize(NV_ROOTDIR . '/' . $global_config['upload_logo']);
            $file_size = getimagesize($fileupload);
            if ($file_size[0] <= 150) {
                $w = ceil($logo_size[0] * $global_config['autologosize1'] / 100);
            } elseif ($file_size[0] < 350) {
                $w = ceil($logo_size[0] * $global_config['autologosize2'] / 100);
            } else {
                if (ceil($file_size[0] * $global_config['autologosize3'] / 100) > $logo_size[0]) {
                    $w = $logo_size[0];
                } else {
                    $w = ceil($file_size[0] * $global_config['autologosize3'] / 100);
                }
            }
            $h = ceil($w * $logo_size[1] / $logo_size[0]);
            $x = $file_size[0] - $w - 5;
            $y = $file_size[1] - $h - 5;
            $config_logo = array();
            $config_logo['w'] = $w;
            $config_logo['h'] = $h;
            $config_logo['x'] = $file_size[0] - $w - 5; // Horizontal: Right
            $config_logo['y'] = $file_size[1] - $h - 5; // Vertical: Bottom
            // Logo vertical
            if (preg_match("/^top/", $global_config['upload_logo_pos'])) {
                $config_logo['y'] = 5;
            } elseif (preg_match("/^center/", $global_config['upload_logo_pos'])) {
                $config_logo['y'] = round(($file_size[1] / 2) - ($h / 2));
            }
            // Logo horizontal
            if (preg_match("/Left$/", $global_config['upload_logo_pos'])) {
                $config_logo['x'] = 5;
            } elseif (preg_match("/Center$/", $global_config['upload_logo_pos'])) {
                $config_logo['x'] = round(($file_size[0] / 2) - ($w / 2));
            }
            $createImage = new NukeViet\Files\Image($fileupload, NV_MAX_WIDTH, NV_MAX_HEIGHT);
            $createImage->addlogo(NV_ROOTDIR . '/' . $global_config['upload_logo'], '', '', $config_logo);
            $createImage->save(dirname($fileupload), basename($fileupload), 90);
        }
    }
}

function nv_block_position($block)
{
    global $themeConfig;

    if (isset($themeConfig['positions']['position']) && !empty($themeConfig['positions']['position'])) {
        foreach ($themeConfig['positions']['position'] as $_pos) {
            $_pos = trim((string) $_pos['tag']);
            if ($block == $_pos) {
                return true;
            }
        }
    }
    return false;
}

function nv_user_logs($msg)
{
    global $module_name;

    if (!file_exists(NV_USER_LOGS_PATH)) {
        nv_mkdir(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_name, 'user_logs');
    }

    try {
        $fd = fopen(NV_USER_LOGS_PATH . '/' . date('d-m-Y', NV_CURRENTTIME) . '.log', "a");
        $str = "[" . date("Y/m/d h:i:s", NV_CURRENTTIME) . "] " . $msg;
        fwrite($fd, $str . "\n");
        fclose($fd);
    } catch (Exception $e) {
        trigger_error($e->getMessage());
    }
}