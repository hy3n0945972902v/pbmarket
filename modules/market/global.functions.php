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

$array_config = $module_config[$module_name];

$array_pricetype = array(
    0 => $lang_module['pricetype_0'],
    1 => $lang_module['pricetype_1'],
    2 => $lang_module['pricetype_2']
);

$array_pricetype_cat = array(
    1 => $lang_module['pricetype_cat_title_1'],
    2 => $lang_module['pricetype_cat_title_2']
);

$_sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_cat WHERE status=1 ORDER BY sort';
$array_market_cat = $nv_Cache->db($_sql, 'id', $module_name);

$_sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block_cat ORDER BY weight';
$array_market_groups = $nv_Cache->db($_sql, 'bid', $module_name);

$_sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_type WHERE status=1 ORDER BY weight';
$array_type = $nv_Cache->db($_sql, 'id', $module_name);

$_sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_unit WHERE status=1 ORDER BY weight';
$array_unit = $nv_Cache->db($_sql, 'id', $module_name);

require_once NV_ROOTDIR . '/modules/location/location.class.php';

/**
 * nv_delete_rows()
 *
 * @param mixed $id
 * @return
 *
 */
function nv_delete_rows($id, $userid = 0)
{
    global $db, $module_data;

    $where = '';
    if ($userid > 0) {
        $where .= ' AND userid=' . $userid;
    }

    $count = $db->exec('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows  WHERE id = ' . $id . $where);
    if ($count) {
        // Xoa tin khoi nhom tin
        $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block WHERE id=' . $id);

        // Xoa tin da luu
        $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_saved WHERE rowsid=' . $id);

        // Xoa lich su duyet
        $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_queue_logs WHERE rowsid=' . $id);

        // Xoa hinh anh
        nv_delete_images($id);

        // xóa trường tùy biến
        $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_info WHERE rowid=' . $id);

        // Xoa khoi dang facebook
        $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_fb_queue WHERE rowsid=' . $id);

        // Cập nhật thu nhập Cộng tác viên
        nv_freelance_update('-');
    }
}

/**
 * nv_delete_images()
 *
 * @param mixed $rowsid
 * @param mixed $path
 * @return
 *
 */
function nv_delete_images($rowsid, $_path = '')
{
    global $db, $module_data, $module_upload;

    $where = 'rowsid=' . $rowsid;
    $where .= !empty($_path) ? ' AND path=' . $db->quote($_path) : '';

    $result = $db->query('SELECT rowsid, path FROM ' . NV_PREFIXLANG . '_' . $module_data . '_images WHERE ' . $where);
    while (list ($rowsid, $path) = $result->fetch(3)) {
        $count = $db->exec('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_images WHERE rowsid=' . $rowsid . ' AND path=' . $db->quote($path));
        if ($count) {
            nv_deletefile(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_upload . '/' . $path);
            nv_deletefile(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $path);
            return true;
        }
    }
    return false;
}

/**
 * nv_GetCatidInParent()
 *
 * @param mixed $catid
 * @return
 *
 */
function nv_GetCatidInParent($catid)
{
    global $array_market_cat;

    $_array_cat = array();
    $_array_cat[] = $catid;
    $subcatid = explode(',', $array_market_cat[$catid]['subid']);

    if (!empty($subcatid)) {
        foreach ($subcatid as $id) {
            if ($id > 0) {
                if ($array_market_cat[$id]['numsub'] == 0) {
                    $_array_cat[] = $id;
                } else {
                    $array_cat_temp = nv_GetCatidInParent($id);
                    foreach ($array_cat_temp as $catid_i) {
                        $_array_cat[] = $catid_i;
                    }
                }
            }
        }
    }
    return array_unique($_array_cat);
}

/**
 * nv_market_viewImage()
 *
 * @param mixed $fileName
 * @return
 *
 */
function nv_market_viewImage($fileName)
{
    global $db;

    $array_thumb_config = array();

    $sql = 'SELECT * FROM ' . NV_UPLOAD_GLOBALTABLE . '_dir ORDER BY dirname ASC';
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $array_dirname[$row['dirname']] = $row['did'];
        if ($row['thumb_type']) {
            $array_thumb_config[$row['dirname']] = $row;
        }
    }
    unset($array_dirname['']);

    if (preg_match('/^' . nv_preg_quote(NV_UPLOADS_DIR) . '\/(([a-z0-9\-\_\/]+\/)*([a-z0-9\-\_\.]+)(\.(gif|jpg|jpeg|png|bmp|ico)))$/i', $fileName, $m)) {
        $viewFile = NV_FILES_DIR . '/' . $m[1];

        if (file_exists(NV_ROOTDIR . '/' . $viewFile)) {
            $size = @getimagesize(NV_ROOTDIR . '/' . $viewFile);
            return array(
                $viewFile,
                $size[0],
                $size[1]
            );
        } else {
            $m[2] = rtrim($m[2], '/');

            if (isset($array_thumb_config[NV_UPLOADS_DIR . '/' . $m[2]])) {
                $thumb_config = $array_thumb_config[NV_UPLOADS_DIR . '/' . $m[2]];
            } else {
                $thumb_config = $array_thumb_config[''];
                $_arr_path = explode('/', NV_UPLOADS_DIR . '/' . $m[2]);
                while (sizeof($_arr_path) > 1) {
                    array_pop($_arr_path);
                    $_path = implode('/', $_arr_path);
                    if (isset($array_thumb_config[$_path])) {
                        $thumb_config = $array_thumb_config[$_path];
                        break;
                    }
                }
            }

            $viewDir = NV_FILES_DIR;
            if (!empty($m[2])) {
                if (!is_dir(NV_ROOTDIR . '/' . $m[2])) {
                    $e = explode('/', $m[2]);
                    $cp = NV_FILES_DIR;
                    foreach ($e as $p) {
                        if (is_dir(NV_ROOTDIR . '/' . $cp . '/' . $p)) {
                            $viewDir .= '/' . $p;
                        } else {
                            $mk = nv_mkdir(NV_ROOTDIR . '/' . $cp, $p);
                            if ($mk[0] > 0) {
                                $viewDir .= '/' . $p;
                            }
                        }
                        $cp .= '/' . $p;
                    }
                }
            }
            $image = new NukeViet\Files\Image(NV_ROOTDIR . '/' . $fileName, NV_MAX_WIDTH, NV_MAX_HEIGHT);
            if ($thumb_config['thumb_type'] == 4) {
                $thumb_width = $thumb_config['thumb_width'];
                $thumb_height = $thumb_config['thumb_height'];
                $maxwh = max($thumb_width, $thumb_height);
                if ($image->fileinfo['width'] > $image->fileinfo['height']) {
                    $thumb_config['thumb_width'] = 0;
                    $thumb_config['thumb_height'] = $maxwh;
                } else {
                    $thumb_config['thumb_width'] = $maxwh;
                    $thumb_config['thumb_height'] = 0;
                }
            }
            if ($image->fileinfo['width'] > $thumb_config['thumb_width'] or $image->fileinfo['height'] > $thumb_config['thumb_height']) {
                $image->resizeXY($thumb_config['thumb_width'], $thumb_config['thumb_height']);
                if ($thumb_config['thumb_type'] == 4) {
                    $image->cropFromCenter($thumb_width, $thumb_height);
                }
                $image->save(NV_ROOTDIR . '/' . $viewDir, $m[3] . $m[4], $thumb_config['thumb_quality']);
                $create_Image_info = $image->create_Image_info;
                $error = $image->error;
                $image->close();
                if (empty($error)) {
                    return array(
                        $viewDir . '/' . basename($create_Image_info['src']),
                        $create_Image_info['width'],
                        $create_Image_info['height']
                    );
                }
            } elseif (copy(NV_ROOTDIR . '/' . $fileName, NV_ROOTDIR . '/' . $viewDir . '/' . $m[3] . $m[4])) {
                $return = array(
                    $viewDir . '/' . $m[3] . $m[4],
                    $image->fileinfo['width'],
                    $image->fileinfo['height']
                );
                $image->close();
                return $return;
            } else {
                return false;
            }
        }
    } else {
        $size = @getimagesize(NV_ROOTDIR . '/' . $fileName);
        return array(
            $fileName,
            $size[0],
            $size[1]
        );
    }
    return false;
}

function nv_auction_status($auction_begin, $auction_end)
{
    if (NV_CURRENTTIME >= $auction_begin and NV_CURRENTTIME <= $auction_end) {
        return 1;
    } elseif (NV_CURRENTTIME > $auction_end) {
        return 2;
    } else {
        return 0;
    }
}

/**
 * nv_fix_block()
 *
 * @param mixed $bid
 * @param bool $repairtable
 * @return
 *
 */
function nv_fix_block($bid, $repairtable = true)
{
    global $db, $db_config, $module_data;
    $bid = intval($bid);
    if ($bid > 0) {
        $sql = 'SELECT id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block where bid=' . $bid . ' ORDER BY weight ASC';
        $result = $db->query($sql);
        $weight = 0;
        while ($row = $result->fetch()) {
            ++$weight;
            if ($weight <= 100) {
                $sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_block SET weight=' . $weight . ' WHERE bid=' . $bid . ' AND id=' . $row['id'];
            } else {
                $sql = 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block WHERE bid=' . $bid . ' AND id=' . $row['id'];
            }

            $db->query($sql);
        }
        $result->closeCursor();
        if ($repairtable) {
            $db->query('OPTIMIZE TABLE ' . NV_PREFIXLANG . '_' . $module_data . '_block');
        }
    }
}

function nv_post_facebook($link, $message = '')
{
    global $array_config;

    if (!class_exists('Facebook\Facebook') or empty($array_config['fb_appid']) or empty($array_config['fb_secret']) or empty($array_config['fb_accesstoken'])) {
        return false;
    }

    $fb = new Facebook\Facebook([
        'app_id' => $array_config['fb_appid'],
        'app_secret' => $array_config['fb_secret'],
        'default_graph_version' => 'v2.2'
    ]);

    $linkData = [
        'link' => $link,
        'message' => $message
    ];

    if (!empty($array_config['fb_pagetoken'])) {
        $array_config['fb_pagetoken'] = explode('|', $array_config['fb_pagetoken']);
        foreach ($array_config['fb_pagetoken'] as $pageAccessToken) {
            try {
                $response = $fb->post('/me/feed', $linkData, $pageAccessToken);
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                trigger_error($e->getMessage());
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                trigger_error($e->getMessage());
            }
        }
    }

    if (!empty($array_config['fb_groupid'])) {
        $array_config['fb_groupid'] = explode('|', $array_config['fb_groupid']);
        foreach ($array_config['fb_groupid'] as $groupid) {
            try {
                $response = $fb->post($groupid . '/feed', $linkData, $array_config['fb_accesstoken']);
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                trigger_error($e->getMessage());
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                trigger_error($e->getMessage());
            }
        }
    }

    return true;
}

function nv_check_similar_content($content)
{
    global $db_slave, $array_config, $module_data;

    if (!empty($array_config['similar_content'])) {
        $similar_time = !empty($array_config['similar_time']) ? 86400 * $array_config['similar_time'] : 0;
        $result = $db_slave->query('SELECT t2.content FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows t1 INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_detail t2 ON t1.id=t2.id WHERE addtime >= ' . $similar_time . ' ORDER BY addtime DESC');
        while (list ($_content) = $result->fetch(3)) {
            similar_text(strip_tags($content), strip_tags($_content), $percent);
            if ($percent >= $array_config['similar_content']) {
                return false;
            }
        }
    }
    return true;
}

function nv_add_fb_queue($rowsid)
{
    global $db_slave, $module_data, $array_config;

    if (!class_exists('Facebook\Facebook') or !$array_config['fb_enable'] or empty($array_config['fb_appid']) or empty($array_config['fb_secret']) or empty($array_config['fb_pagetoken'])) {
        return false;
    }

    try {
        $stmt = $db_slave->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_fb_queue(rowsid) VALUES(:rowsid)');
        $stmt->bindParam(':rowsid', $rowsid, PDO::PARAM_INT);
        $stmt->execute();
    } catch (Exception $e) {
        // Co roi thi khong lam gi ca
    }
}

function nv_add_mail_queue($tomail, $subject, $message)
{
    global $db_slave, $module_data;

    if (empty($tomail) or nv_check_valid_email($tomail) != '' or empty($subject) or empty($message)) {
        return false;
    }

    try {
        $stmt = $db_slave->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_mail_queue(tomail, subject, message) VALUES(:tomail, :subject, :message)');
        $stmt->bindParam(':tomail', $tomail, PDO::PARAM_STR);
        $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
        trigger_error($e->getMessage());
    }
}

function nv_rows_update_group($bid, $rowid, $is_del = 0, $exptime = 0)
{
    global $db, $module_name, $module_data;

    if ($is_del) {
        $result = $db->exec('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block WHERE bid=' . $bid . ' AND id=' . $rowid);
    } else {
        $result = $db->query('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_block (bid, id, exptime, weight) VALUES (' . $bid . ', ' . $rowid . ', ' . $exptime . ', 0)');
    }

    if ($result) {
        $groups_config = array();
        $result = $db->query('SELECT bid, exptime FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block WHERE id=' . $rowid);
        while (list ($bid, $exptime) = $result->fetch(3)) {
            $groups_config[$bid] = $exptime;
        }

        $groups_config = !empty($groups_config) ? serialize($groups_config) : '';

        $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET groups_config=' . $db->quote($groups_config) . ' WHERE id=' . $rowid)
            ->fetchColumn();

        return true;
    }
    return false;
}

function nv_load_pricetype($row, $template)
{
    global $lang_module, $module_data, $global_config, $module_file, $array_pricetype, $array_unit, $array_market_cat, $array_config;

    $lang_module['price'] = $lang_module['pricetype_cat_title_' . $array_market_cat[$row['catid']]['pricetype']];
    $lang_module['contact'] = $lang_module['pricetype_cat_contact_' . $array_market_cat[$row['catid']]['pricetype']];

    $xtpl = new XTemplate('loadpricetype.tpl', $template);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('ROW', $row);
    $xtpl->assign('DES_POINT', $array_config['des_point']);
    $xtpl->assign('THOUSANDS_SEP', $array_config['thousands_sep']);

    foreach ($array_pricetype as $index => $value) {
        $sl = $row['pricetype'] == $index ? 'selected="selected"' : '';
        $xtpl->assign('PRICETYPE', array(
            'index' => $index,
            'value' => $value,
            'selected' => $sl
        ));
        $xtpl->parse('main.pricetype');
    }

    if (!empty($array_unit)) {
        foreach ($array_unit as $unit) {
            $unit['selected'] = $unit['id'] == $row['unitid'] ? 'selected="selected"' : '';
            $xtpl->assign('UNIT', $unit);
            $xtpl->parse('main.unit');
        }
    }

    if ($array_market_cat[$row['catid']]['pricetype'] == 1) {
        $xtpl->parse('main.pricetype_cat_1');
    } elseif ($array_market_cat[$row['catid']]['pricetype'] == 2) {
        $xtpl->parse('main.pricetype_cat_2');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

function nv_freelance_update($operator = '+')
{
    global $db, $user_info, $module_data, $array_config;

    if (defined('NV_IS_USER') and in_array($array_config['freelancegroup'], $user_info['in_groups'])) {
        $result = $db->query('SELECT userid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_freelance WHERE userid=' . $user_info['userid']);
        if ($result) {
            $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_freelance SET total=total' . $operator . 'salary WHERE userid=' . $user_info['userid']);
        }
    }
}

/**
 * nv_show_custom_form()
 *
 * @param mixed $id
 * @param mixed $form
 * @param mixed $array_custom
 * @param mixed $array_custom_lang
 * @return
 */
function nv_show_custom_form($id, $form, $array_custom)
{
    global $db, $lang_module, $lang_global, $module_name, $module_data, $op, $module_file;

    $xtpl = new XTemplate('cat_form_' . $form . '.tpl', NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_name . '/files_tpl');
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('MODULE_NAME', $module_name);
    $xtpl->assign('OP', $op);

    if (preg_match('/^[a-zA-Z0-9\-\_]+$/', $form) and file_exists(NV_ROOTDIR . '/modules/' . $module_file . '/admin/cat_form_' . $form . '.php')) {
        require_once NV_ROOTDIR . '/modules/' . $module_file . '/admin/cat_form_' . $form . '.php';
    }

    if (defined('NV_EDITOR')) {
        require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
    }

    $array_custom_lang = array();
    $idtemplate = $db->query('SELECT id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_template WHERE alias = "' . preg_replace("/[\_]/", "-", $form) . '"')->fetchColumn();
    if ($idtemplate) {
        $array_tmp = array();
        $result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_field');
        while ($row = $result->fetch()) {
            $listtemplate = explode(',', $row['listtemplate']);
            if (in_array($idtemplate, $listtemplate)) {

                if ($row['field_type'] == 'date') {
                    $array_custom[$row['field']] = ($row['field_choices']['current_date']) ? NV_CURRENTTIME : $row['default_value'];
                } elseif ($row['field_type'] == 'number') {
                    $array_custom[$row['field']] = $row['default_value'];
                } else {
                    if (!empty($row['field_choices'])) {
                        $temp = array_keys($row['field_choices']);
                        $tempkey = intval($row['default_value']) - 1;
                        $array_custom[$row['field']] = (isset($temp[$tempkey])) ? $temp[$tempkey] : '';
                    }
                }

                if (!empty($row['field_choices'])) {
                    $row['field_choices'] = unserialize($row['field_choices']);
                } elseif (!empty($row['sql_choices'])) {
                    $row['sql_choices'] = explode(',', $row['sql_choices']);
                    $query = 'SELECT ' . $row['sql_choices'][2] . ', ' . $row['sql_choices'][3] . ' FROM ' . $row['sql_choices'][1];
                    $result_sql = $db->query($query);
                    $row['field_choices'] = array();
                    while (list ($key, $val) = $result_sql->fetch(3)) {
                        $row['field_choices'][$key] = $val;
                    }
                }

                if ($row['field_type'] == 'date') {
                    $array_custom[$row['field']] = (empty($array_custom[$row['field']])) ? '' : date('d/m/Y', $array_custom[$row['field']]);
                } elseif ($row['field_type'] == 'textarea') {
                    $array_custom[$row['field']] = nv_htmlspecialchars(nv_br2nl($array_custom[$row['field']]));
                } elseif ($row['field_type'] == 'editor') {
                    $array_custom[$row['field']] = (empty($array_custom[$row['field']])) ? '' : htmlspecialchars(nv_editor_br2nl($array_custom[$row['field']]));
                    $array_custom[$row['fid']] = !empty($array_custom[$row['fid']]) ? $array_custom[$row['fid']] : '';

                    if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor')) {
                        $row['class'] = explode('@', $row['class']);
                        $edits = nv_aleditor('custom[' . $row['fid'] . ']', $row['class'][0], $row['class'][1], $array_custom[$row['fid']]);
                        $array_custom[$row['field']] = $edits;
                    } else {
                        $row['class'] = '';
                    }
                } elseif ($row['field_type'] == 'select') {
                    foreach ($row['field_choices'] as $key => $value) {
                        $xtpl->assign('OPTION', array(
                            'key' => $key,
                            'selected' => ($key == $array_custom[$row['field']]) ? ' selected="selected"' : '',
                            'title' => $value
                        ));
                        $xtpl->parse('main.select_' . $row['fid']);
                    }
                } elseif ($row['field_type'] == 'radio' or $row['field_type'] == 'checkbox') {
                    $number = 0;
                    foreach ($row['field_choices'] as $key => $value) {
                        $xtpl->assign('OPTION', array(
                            'id' => $row['fid'] . '_' . $number++,
                            'key' => $key,
                            'checked' => ($key == $array_custom[$row['field']]) ? ' checked="checked"' : '',
                            'title' => $value
                        ));
                        $xtpl->parse('main.' . $row['field_type'] . '_' . $row['fid']);
                    }
                } elseif ($row['field_type'] == 'multiselect') {
                    foreach ($row['field_choices'] as $key => $value) {
                        $xtpl->assign('OPTION', array(
                            'key' => $key,
                            'selected' => ($key == $array_custom[$row['field']]) ? ' selected="selected"' : '',
                            'title' => $value
                        ));
                        $xtpl->parse('main.' . $row['fid']);
                    }
                }

                // Du lieu hien thi tieu de
                $array_tmp[$row['fid']] = unserialize($row['language']);
            }
        }

        if (!empty($array_tmp)) {
            foreach ($array_tmp as $f_key => $field) {
                foreach ($field as $key_lang => $lang_data) {
                    if ($key_lang == NV_LANG_INTERFACE) {
                        $array_custom_lang[$f_key] = array(
                            'title' => $lang_data[0],
                            'description' => isset($lang_data[1]) ? $lang_data[1] : ''
                        );
                    }
                }
            }
        }
    }

    $xtpl->assign('ROW', $array_custom);
    $xtpl->assign('CUSTOM_LANG', $array_custom_lang);

    foreach ($array_custom_lang as $k_lang => $custom_lang) {
        if (!empty($custom_lang['description'])) {
            $xtpl->parse('main.' . $k_lang . '_description');
        }
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

function nv_market_initializeMap($data = array())
{
    global $module_info, $module_file, $array_config, $lang_module;

    $xtpl = new XTemplate('maps.tpl', NV_ROOTDIR . '/themes/default/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('ROW', $data);
    $xtpl->assign('MAPS_APPID', $array_config['maps_appid']);

    $xtpl->parse('main');
    return $xtpl->text('main');
}
