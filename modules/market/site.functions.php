<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 12/31/2009 0:51
 */
if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

function nv_market_get_thumb($homeimgfile, $homeimgthumb, $module_upload)
{
    global $array_config, $module_info;

    if ($homeimgthumb == 1 and file_exists(NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module_upload . '/' . $homeimgfile)) {
        $thumb = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $homeimgfile;
    } elseif ($homeimgthumb == 2 and file_exists(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile)) {
        $thumb = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
    } elseif ($homeimgthumb == 3) {
        $thumb = $homeimgfile;
    } elseif (!empty($array_config['no_image'])) {
        $thumb = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $array_config['no_image'];
    } else {
        $thumb = NV_BASE_SITEURL . 'themes/' . $module_info['template'] . '/images/market/nopicture.jpg';
    }
    return $thumb;
}

/**
 * nv_market_number_format()
 *
 * @param mixed $number
 * @param integer $decimals
 * @return
 *
 */
function nv_market_number_format($number, $unitid = 0, $decimals = 0)
{
    global $array_config;

    if (in_array($array_config['priceformat'], array(
        0,
        1
    ))) {
        $str = nv_market_price_tostring($number, $unitid);
    } else {
        $str = number_format($number, $decimals, $array_config['thousands_sep'], $array_config['des_point']);
    }

    return $str;
}

function nv_market_data($row, $mod)
{
    global $global_config, $array_market_cat, $array_type, $array_market_groups, $site_mods;

    $mod_data = $site_mods[$mod]['module_data'];
    $mod_upload = $site_mods[$mod]['module_upload'];

    $array_data = array();

    if (nv_user_in_groups($row['groupview'])) {
        $row['cat'] = $array_market_cat[$row['catid']]['title'];
        $row['cat_link'] = $array_market_cat[$row['catid']]['link'];
        $row['type'] = !empty($row['typeid']) ? $array_type[$row['typeid']]['title'] : '';
        $row['addtime_f'] = nv_date('H:i d/m/Y', $row['addtime']);
        $row['addtime'] = nv_get_timeago($row['addtime']);
        $row['price'] = nv_market_get_price($row['price'], $row['price1'], $row['catid'], $row['pricetype'], $row['unitid']);
        $row['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $mod . '&amp;' . NV_OP_VARIABLE . '=' . $array_market_cat[$row['catid']]['alias'] . '/' . $row['alias'] . '-' . $row['id'] . $global_config['rewrite_exturl'];
        $row['thumb'] = nv_market_get_thumb($row['homeimgfile'], $row['homeimgthumb'], $mod_upload);
        $row['thumbalt'] = $row['homeimgalt'];
        $row['color'] = '';

        if (!empty($row['groups_config'])) {
            $groups_config = unserialize($row['groups_config']);
            foreach ($groups_config as $groupid => $exptime) {
                if (isset($array_market_groups[$groupid])) {
                    if (($exptime == 0 or $exptime > NV_CURRENTTIME) and $array_market_groups[$groupid]['useradd']) {
                        $row['color'] = $array_market_groups[$groupid]['color'];
                        break;
                    }
                }
            }
        }

        $array_data = $row;
    }

    return $array_data;
}

function nv_get_timeago($ptime)
{
    global $lang_module, $lang_global;

    $estimate_time = time() - $ptime;

    if ($estimate_time < 1) {
        return $lang_module['timeago_less_than_1_second_ago'];
    }

    $condition = array(
        12 * 30 * 24 * 60 * 60 => $lang_global['year'],
        30 * 24 * 60 * 60 => $lang_global['month'],
        24 * 60 * 60 => $lang_global['day'],
        60 * 60 => $lang_global['hour'],
        60 => $lang_global['min'],
        1 => $lang_global['sec']
    );

    foreach ($condition as $secs => $str) {
        $d = $estimate_time / $secs;

        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? '' : '') . ' ' . $lang_module['timeago_ago'];
        }
    }
}

function nv_market_get_price($price, $price1, $catid, $pricetype, $unitid, $unitspace = ' / ')
{
    global $lang_module, $array_market_cat;

    if ($pricetype == 0) {
        $str = '';
        if ($price > 0) {
            $str .= nv_market_number_format($price, $unitid);
        }

        if ($price1 > 0) {
            $str .= ' - ' . nv_market_number_format($price1, $unitid);
        }
        return $str;
    } elseif ($pricetype == 1) {
        return $lang_module['pricetype_cat_contact_' . $array_market_cat[$catid]['pricetype']];
    }
    return '';
}

function nv_market_build_search_url($module, $typeid = 0, $catid = 0, $provinceid = 0, $districtid = 0, $wardid = 0)
{
    global $array_market_cat, $array_type;

    $data = array();
    $location = new Location();

    if (!empty($typeid)) {
        $data[] = $array_type[$typeid]['alias'];
    }

    if (!empty($catid)) {
        $data[] = $array_market_cat[$catid]['alias'];
    }

    if (!empty($wardid)) {
        $ward = $location->getWardInfo($wardid);
        $data[] = change_alias($ward['type']) . '-' . $ward['alias'];
    }

    if (!empty($districtid)) {
        $district = $location->getDistricInfo($districtid);
        $data[] = change_alias($district['type']) . '-' . $district['alias'];
    }

    if (!empty($provinceid)) {
        $province = $location->getProvinceInfo($provinceid);
        $data[] = change_alias($province['type']) . '-' . $province['alias'];
    }

    return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . (!empty($data) ? implode('-', array_map('strtolower', $data)) : '');
}

/**
 * nv_market_price_tostring()
 *
 * @param mixed $num
 * @return
 *
 */
function nv_market_price_tostring($num = false, $unitid = 0)
{
    global $lang_module, $array_config, $array_unit;

    // first strip any formatting;
    $n = (0 + str_replace(",", "", $num));

    // is this a number?
    if (!is_numeric($n)) return false;

    // now filter it;
    if ($n > 1000000000000)
        $n = round(($n / 1000000000000), 2) . ' ' . $lang_module['trillion'];
    elseif ($n > 1000000000)
        $n = round(($n / 1000000000), 2) . ' ' . $lang_module['billion'];
    elseif ($n > 1000000)
        $n = round(($n / 1000000), 2) . ' ' . $lang_module['million'];
    elseif ($n > 1000)
        $n = round(($n / 1000), 2) . ' ' . $lang_module['thousand'];
    else
        $n = number_format($n);

    if ($array_config['priceformat'] == 1 and $unitid > 0) {
        $n .= ' / ' . $array_unit[$unitid]['title'];
    }

    return $n;
}

function nv_upload_user_path($username_alias)
{
    global $db, $module_upload, $array_config;

    $array_structure_image = array();
    $array_structure_image[''] = $module_upload;
    $array_structure_image['Y'] = $module_upload . '/' . date('Y');
    $array_structure_image['Ym'] = $module_upload . '/' . date('Y_m');
    $array_structure_image['Y_m'] = $module_upload . '/' . date('Y/m');
    $array_structure_image['Ym_d'] = $module_upload . '/' . date('Y_m/d');
    $array_structure_image['Y_m_d'] = $module_upload . '/' . date('Y/m/d');
    $array_structure_image['username'] = $module_upload . '/' . $username_alias;
    $array_structure_image['username_Y'] = $module_upload . '/' . $username_alias . '/' . date('Y');
    $array_structure_image['username_Ym'] = $module_upload . '/' . $username_alias . '/' . date('Y_m');
    $array_structure_image['username_Y_m'] = $module_upload . '/' . $username_alias . '/' . date('Y/m');
    $array_structure_image['username_Ym_d'] = $module_upload . '/' . $username_alias . '/' . date('Y_m/d');
    $array_structure_image['username_Y_m_d'] = $module_upload . '/' . $username_alias . '/' . date('Y/m/d');
    $structure_upload = isset($array_config['structure_upload']) ? $array_config['structure_upload'] : 'Ym';

    $currentpath = isset($array_structure_image[$structure_upload]) ? $array_structure_image[$structure_upload] : '';

    if (file_exists(NV_UPLOADS_REAL_DIR . '/' . $currentpath)) {
        $upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $currentpath;
    } else {
        $upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $module_upload;
        $e = explode('/', $currentpath);
        if (!empty($e)) {
            $cp = '';
            foreach ($e as $p) {
                if (!empty($p) and !is_dir(NV_UPLOADS_REAL_DIR . '/' . $cp . $p)) {
                    $mk = nv_mkdir(NV_UPLOADS_REAL_DIR . '/' . $cp, $p);
                    if ($mk[0] > 0) {
                        $upload_real_dir_page = $mk[2];
                        try {
                            $db->query("INSERT INTO " . NV_UPLOAD_GLOBALTABLE . "_dir (dirname, time) VALUES ('" . NV_UPLOADS_DIR . "/" . $cp . $p . "', 0)");
                        } catch (PDOException $e) {
                            // trigger_error($e->getMessage());
                        }
                    }
                } elseif (!empty($p)) {
                    $upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $cp . $p;
                }
                $cp .= $p . '/';
            }
        }
        $upload_real_dir_page = str_replace('\\', '/', $upload_real_dir_page);
    }

    $currentpath = str_replace(NV_ROOTDIR . '/', '', $upload_real_dir_page);
    $uploads_dir_user = NV_UPLOADS_DIR . '/' . $module_upload;
    if (!defined('NV_IS_SPADMIN') and strpos($structure_upload, 'username') !== false) {
        $array_currentpath = explode('/', $currentpath);
        if ($array_currentpath[2] == $username_alias) {
            $uploads_dir_user = NV_UPLOADS_DIR . '/' . $module_upload . '/' . $username_alias;
        }
    }

    return $currentpath;
}

function nv_market_money_string_to_number($string)
{
    $string = nv_EncString($string);
    if (preg_match('/([0-9\.\,]+) ([(ti|ty|trieu)]+)/', $string, $m)) {
        $m[1] = preg_replace('/[^0-9]/', '', $m[1]);
        if ($m[2] == 'ti' || $m[2] == 'ty') {
            $m[2] = substr('0000000000', strlen($m[1]));
        } else {
            $m[2] = substr('0000000', strlen($m[1]));
        }
        return $m[1] . $m[2];
    }
    return 0;
}