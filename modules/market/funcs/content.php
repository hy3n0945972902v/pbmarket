<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2016 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 21 Nov 2016 01:26:01 GMT
 */
if (!defined('NV_IS_MOD_MARKET')) die('Stop!!!');

if (nv_user_in_groups($array_config['grouppost'])) {
    $premission = nv_group_premission();
    if (empty($premission)) {
        $contents = nv_theme_alert($lang_module['is_user_title'], $lang_module['userarea_not_premission_content'], 'info', NV_BASE_SITEURL, $lang_module['backhome']);
        include NV_ROOTDIR . '/includes/header.php';
        echo nv_site_theme($contents);
        include NV_ROOTDIR . '/includes/footer.php';
    }
} elseif (!defined('NV_IS_USER')) {
    $url_redirect = $client_info['selfurl'];
    $url_back = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt($url_redirect);
    $contents = nv_theme_alert($lang_module['is_user_title'], $lang_module['is_user_content'], 'info', $url_back, $lang_module['login']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
} else {
    Header('Location: ' . NV_BASE_SITEURL);
    die();
}

$row = array();
$error = array();
$array_keywords_old = array();
$ispopup = $nv_Request->get_int('ispopup', 'get', 0);
$redirect = $nv_Request->get_title('redirect', 'get', '');
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);
$userid = defined('NV_IS_USER') ? $user_info['userid'] : 0;

if (!defined('NV_IS_USER')) {
    $username_alias = 'guest';
    $user_info = array(
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'address' => ''
    );
} else {
    if (!empty($premission['maxpost'])) {
        $count = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE userid=' . $user_info['userid'])->fetchColumn();
        if ($count >= $premission['maxpost']) {
            Header('Location: ' . NV_BASE_SITEURL);
            die();
        }
    }

    $username_alias = change_alias($user_info['username']);
}

$currentpath = nv_upload_user_path($username_alias);

$is_editor = 1;
if (!defined('NV_IS_USER') and !$array_config['editor_guest']) {
    $is_editor = 0;
} else if (defined('NV_EDITOR')) {
    require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
} elseif (!nv_function_exists('nv_aleditor') and file_exists(NV_ROOTDIR . '/' . NV_EDITORSDIR . '/ckeditor/ckeditor.js')) {
    define('NV_EDITOR', true);
    define('NV_IS_CKEDITOR', true);
    $my_head .= '<script type="text/javascript" src="' . NV_BASE_SITEURL . NV_EDITORSDIR . '/ckeditor/ckeditor.js"></script>';

    function nv_aleditor($textareaname, $width = '100%', $height = '450px', $val = '', $customtoolbar = '')
    {
        global $module_data;
        $return = '<textarea style="width: ' . $width . '; height:' . $height . ';" id="' . $module_data . '_' . $textareaname . '" name="' . $textareaname . '">' . $val . '</textarea>';
        $return .= "<script type=\"text/javascript\">
	CKEDITOR.replace( '" . $module_data . "_" . $textareaname . "', {" . (!empty($customtoolbar) ? 'toolbar : "' . $customtoolbar . '",' : '') . " width: '" . $width . "',height: '" . $height . "',});
	</script>";
        return $return;
    }
}

if ($row['id'] > 0) {
    $lang_module['content_add'] = $lang_module['content_edit'];
    $row = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $row['id'])->fetch();
    if (empty($row)) {
        Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }

    $detail = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_detail WHERE id=' . $row['id'])->fetch();
    $row = array_merge($row, $detail);
    unset($detail);

    $row['images'] = $row['images_old'] = array();
    $result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_images WHERE rowsid=' . $row['id'] . ' ORDER BY weight ASC');
    while ($_row = $result->fetch()) {
        $row['images_old'][] = $_row['path'];
        $row['images'][] = $_row;
    }

    // Từ khóa
    $_query = $db->query('SELECT tid, keyword FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE id=' . $row['id'] . ' ORDER BY keyword ASC');
    while ($_row = $_query->fetch()) {
        $array_keywords_old[$_row['tid']] = $_row['keyword'];
    }
    $row['keywords'] = implode(', ', $array_keywords_old);
    $row['keywords_old'] = $row['keywords'];

    $row['custom_field'] = array();
    $idtemplate = $db->query('SELECT id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_template WHERE alias = "' . preg_replace("/[\_]/", "-", $array_market_cat[$row['catid']]['form']) . '"')->fetchColumn();
    if ($idtemplate) {
        $result = $db->query("SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_info WHERE rowid=" . $row['id']);
        $row['custom_field'] = $result->fetch();
    }
} else {
    $row['id'] = 0;
    $row['code'] = '';
    $row['title'] = '';
    $row['alias'] = '';
    $row['catid'] = $nv_Request->isset_request('catid', 'session') ? $nv_Request->get_int('catid', 'session') : 0;
    $row['groupid'] = '';
    $row['area_p'] = $array_config['province_default'];
    $row['area_d'] = 0;
    $row['typeid'] = 0;
    $row['description'] = '';
    $row['content'] = '';
    $row['homeimgfile'] = '';
    $row['homeimgalt'] = '';
    $row['homeimgthumb'] = 1;
    $row['pricetype'] = 0;
    $row['price'] = 0;
    $row['price1'] = 0;
    $row['unitid'] = 0;
    $row['note'] = '';
    $row['countview'] = 0;
    $row['countcomment'] = 0;
    $row['addtime'] = 0;
    $row['exptime'] = 0;
    $row['auction'] = 0;
    $row['auction_begin'] = 0;
    $row['auction_end'] = 0;
    $row['auction_price_begin'] = 0;
    $row['auction_price_step'] = 0;
    $row['groupview'] = 6;
    $row['groupcomment'] = 6;
    $row['is_queue'] = 0;
    $row['status_admin'] = 1;
    $row['status'] = 1;
    $row['custom_field'] = $row['maps'] = $row['images'] = $row['images_old'] = array();
    $row['display_maps'] = 0;
    $row['requeue'] = 0;
    $row['keywords'] = '';
    $row['keywords_old'] = '';
    $row['contact_fullname'] = '';
    $row['contact_email'] = '';
    $row['contact_phone'] = '';
    $row['contact_address'] = '';

    if ($userid > 0) {
        $row['contact_fullname'] = $user_info['fullname'];
        $row['contact_email'] = $user_info['email'];
        $row['contact_phone'] = $user_info['phone'];
        $row['contact_address'] = $user_info['address'];
    }
}

if ($nv_Request->isset_request('submit', 'post')) {
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['catid'] = $nv_Request->get_int('catid', 'post', 0);
    $row['area_p'] = $nv_Request->get_int('area_p', 'post', 0);
    $row['area_d'] = $nv_Request->get_int('area_d', 'post', 0);
    $row['typeid'] = $nv_Request->get_int('typeid', 'post', 0);
    $row['description'] = $nv_Request->get_textarea('description', '');
    $row['pricetype'] = $nv_Request->get_int('pricetype', 'post', $ispopup ? 2 : 0);
    $row['price'] = $nv_Request->get_title('price', 'post', 0);
    $row['price'] = preg_replace('/[^0-9]/', '', $row['price']);
    $row['price1'] = $nv_Request->get_title('price1', 'post', 0);
    $row['price1'] = preg_replace('/[^0-9]/', '', $row['price1']);
    $row['unitid'] = $nv_Request->get_title('unitid', 'post', 0);
    $row['note'] = $nv_Request->get_textarea('note', '');
    $row['queue'] = $nv_Request->get_int('queue', 'post', 0);
    $row['requeue'] = $nv_Request->get_int('requeue', 'post', 0);
    $row['images'] = $nv_Request->get_array('images', 'post');
    $row['keywords'] = $nv_Request->get_array('keywords', 'post', '');
    $row['keywords'] = implode(', ', $row['keywords']);
    $row['custom_field'] = $nv_Request->get_array('custom', 'post');
    $row['maps'] = $nv_Request->get_array('maps', 'post', array());
    $row['display_maps'] = $nv_Request->get_int('display_maps', 'post', 0);

    if (defined('NV_EDITOR') and $is_editor) {
        $row['content'] = $nv_Request->get_editor('content', '', NV_ALLOWED_HTML_TAGS);
    } else {
        $row['content'] = $nv_Request->get_textarea('content', '', 'br');
        $row['content'] = nv_nl2br($row['content']);
    }

    $row['contact_fullname'] = $nv_Request->get_title('contact_fullname', 'post', '');
    $row['contact_email'] = $nv_Request->get_title('contact_email', 'post', '');
    $row['contact_phone'] = $nv_Request->get_title('contact_phone', 'post', '');
    $row['contact_address'] = $nv_Request->get_title('contact_address', 'post', '');

    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('exptime', 'post'), $m)) {
        $_hour = $nv_Request->get_int('begintime_hour', 'post');
        $_min = $nv_Request->get_int('begintime_min', 'post');
        $row['exptime'] = mktime($_hour, $_min, 59, $m[2], $m[1], $m[3]);
    } else {
        $row['exptime'] = 0;
    }

    if ($array_config['auction']) {
        $row['auction'] = $nv_Request->get_int('auction', 'post', 0);
        if ($row['auction']) {
            if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('auction_begin_date', 'post'), $m)) {
                $_hour = $nv_Request->get_int('auction_begin_hour', 'post');
                $_min = $nv_Request->get_int('auction_begin_min', 'post');
                $row['auction_begin'] = mktime($_hour, $_min, 59, $m[2], $m[1], $m[3]);
            } else {
                $row['auction_begin'] = 0;
            }

            if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('auction_end_date', 'post'), $m)) {
                $_hour = $nv_Request->get_int('auction_end_hour', 'post');
                $_min = $nv_Request->get_int('auction_end_min', 'post');
                $row['auction_end'] = mktime($_hour, $_min, 59, $m[2], $m[1], $m[3]);
            } else {
                $row['auction_end'] = 0;
            }

            $row['auction_price_begin'] = $nv_Request->get_title('auction_price_begin', 'post', '');
            $row['auction_price_begin'] = preg_replace('/[^0-9]/', '', $row['auction_price_begin']);

            $row['auction_price_step'] = $nv_Request->get_title('auction_price_step', 'post', '');
            $row['auction_price_step'] = preg_replace('/[^0-9]/', '', $row['auction_price_step']);
        }
    }

    if (empty($row['alias'])) {
        $row['alias'] = change_alias($row['title']);
        if ($array_config['tags_alias_lower']) {
            $row['alias'] = strtolower($row['alias']);
        }

        $stmt = $db->prepare('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id !=' . $row['id'] . ' AND alias = :alias');
        $stmt->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->fetchColumn()) {
            $weight = $db->query('SELECT MAX(id) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows')->fetchColumn();
            $weight = intval($weight) + 1;
            $row['alias'] = $row['alias'] . '-' . $weight;
        }
    }

    if (empty($row['title'])) {
        $error[] = $lang_module['error_required_title'];
    } elseif (empty($row['catid'])) {
        $error[] = $lang_module['error_required_catid'];
    } elseif ($row['pricetype'] == 0 and empty($row['price'])) {
        $error[] = $lang_module['error_required_price'];
    } elseif (empty($row['content'])) {
        $error[] = $lang_module['error_required_content'];
    } elseif (empty($row['id']) and !empty($array_config['similar_content']) and !nv_check_similar_content($row['content'])) {
        $error[] = $lang_module['error_similar_content'];
    } elseif ($array_config['auction'] and $row['auction']) {
        if (empty($row['auction_begin'])) {
            $error[] = $lang_module['error_required_auction_begin'];
        } elseif (empty($row['auction_end'])) {
            $error[] = $lang_module['error_required_auction_end'];
        } elseif (empty($row['auction_price_begin'])) {
            $error[] = $lang_module['error_required_auction_price_begin'];
        } elseif (empty($row['auction_price_step'])) {
            $error[] = $lang_module['error_required_auction_price_step'];
        }
    } elseif (!empty($row['contact_email']) and ($error_email = nv_check_valid_email($row['contact_email'])) != '') {
        $error[] = $error_email;
    } elseif (!nv_capcha_txt(($global_config['captcha_type'] == 2 ? $nv_Request->get_title('g-recaptcha-response', 'post', '') : $nv_Request->get_title('fcode', 'post', '')))) {
        $error[] = ($global_config['captcha_type'] == 2 ? $lang_global['securitycodeincorrect1'] : $lang_global['securitycodeincorrect']);
    }

    $query_field = array();
    if (isset($array_market_cat[$row['catid']]) and $array_market_cat[$row['catid']]['form'] != '') {
        require NV_ROOTDIR . '/modules/' . $module_file . '/fields.check.php';
    }

    if (empty($error)) {
        // loại bỏ link trong nội dung
        if ($array_config['remove_link']) {
            $row['content'] = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $row['content']);
        }

        // kiểm tra nếu đơn vị đăng được thêm mới
        if (!empty($row['unitid']) and !is_numeric($row['unitid'])) {
            $_sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_unit (title, weight) VALUES (:title, :weight)';
            $data_insert = array();
            $data_insert['title'] = $row['unitid'];

            $weight = $db->query('SELECT max(weight) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_unit')->fetchColumn();
            $weight = intval($weight) + 1;
            $data_insert['weight'] = $weight;

            $row['unitid'] = $db->insert_id($_sql, 'id', $data_insert);
        }

        // Tự động xác định từ khóa
        if ($row['keywords'] == '' and !empty($module_config[$module_name]['auto_tags'])) {
            $keywords_return = array();
            $sql = 'SELECT keywords, alias FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags';
            $array_keyword = $nv_Cache->db($sql, 'tid', $module_name);
            foreach ($array_keyword as $keyword) {
                if (preg_match('/' . $keyword['keywords'] . '/imsu', $row['title'] . ' ' . $row['description'] . ' ' . $row['content'])) {
                    $keywords_return[] = $keyword['keywords'];
                }
            }
            $row['keywords'] = implode(',', $keywords_return);
        }

        try {
            $new_id = $is_queue_edit = 0;
            $row['is_queue'] = isset($premission['queue']) ? intval($premission['queue']) : 0;
            if ($row['requeue']) {
                $row['is_queue'] = 1;
            }
            $maps = !empty($row['maps']) ? serialize($row['maps']) : '';
            if (empty($row['id'])) {
                $_sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_rows (title, alias, catid, area_p, area_d, typeid, description, pricetype, price, price1, unitid, addtime, exptime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, groupview, userid, ordertime, is_queue) VALUES (:title, :alias, :catid, :area_p, :area_d, :typeid, :description, :pricetype, :price, :price1, :unitid, ' . NV_CURRENTTIME . ', :exptime, :auction, :auction_begin, :auction_end, :auction_price_begin, :auction_price_step, :groupview, ' . $userid . ', ' . NV_CURRENTTIME . ', :is_queue)';
                $data_insert = array();
                $data_insert['title'] = $row['title'];
                $data_insert['alias'] = $row['alias'];
                $data_insert['catid'] = $row['catid'];
                $data_insert['area_p'] = $row['area_p'];
                $data_insert['area_d'] = $row['area_d'];
                $data_insert['typeid'] = $row['typeid'];
                $data_insert['description'] = $row['description'];
                $data_insert['pricetype'] = $row['pricetype'];
                $data_insert['price'] = $row['price'];
                $data_insert['price1'] = $row['price1'];
                $data_insert['unitid'] = $row['unitid'];
                $data_insert['exptime'] = $row['exptime'];
                $data_insert['auction'] = $row['auction'];
                $data_insert['auction_begin'] = $row['auction_begin'];
                $data_insert['auction_end'] = $row['auction_end'];
                $data_insert['auction_price_begin'] = $row['auction_price_begin'];
                $data_insert['auction_price_step'] = $row['auction_price_step'];
                $data_insert['groupview'] = $row['groupview'];
                $data_insert['is_queue'] = $row['is_queue'];
                $new_id = $db->insert_id($_sql, 'id', $data_insert);
            } else {
                $is_queue_edit = isset($premission['queue_edit']) ? intval($premission['queue_edit']) : 0;
                if ($is_queue_edit) {
                    $count = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_queue_edit WHERE rowsid=' . $row['id'])->fetchColumn();
                    if ($count) {
                        $stmt = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_queue_edit SET title = :title, catid = :catid, area_p = :area_p, area_d = :area_d, typeid = :typeid, description = :description, content = :content, maps = :maps, display_maps = :display_maps, pricetype = :pricetype, price = :price, price1 = :price1, unitid = :unitid, note = :note, exptime = :exptime, auction = :auction, auction_begin = :auction_begin, auction_end = :auction_end, auction_price_begin = :auction_price_begin, auction_price_step = :auction_price_step, contact_fullname = :contact_fullname, contact_email = :contact_email, contact_phone = :contact_phone, contact_address = :contact_address WHERE rowsid = :rowsid');
                    } else {
                        $stmt = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_queue_edit(rowsid, title, catid, area_p, area_d, typeid, description, content, maps, display_maps, pricetype, price, price1, unitid, note, exptime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, contact_fullname, contact_email, contact_phone, contact_address) VALUES(:rowsid, :title, :catid, :area_p, :area_d, :typeid, :description, :content, :maps, :display_maps, :pricetype, :price, :price1, :unitid, :note, :exptime, :auction, :auction_begin, :auction_end, :auction_price_begin, :auction_price_step, :contact_fullname, :contact_email, :contact_phone, :contact_address)');
                    }
                    $stmt->bindParam(':rowsid', $row['id'], PDO::PARAM_INT);
                    $stmt->bindParam(':title', $row['title'], PDO::PARAM_STR);
                    $stmt->bindParam(':catid', $row['catid'], PDO::PARAM_INT);
                    $stmt->bindParam(':area_p', $row['area_p'], PDO::PARAM_INT);
                    $stmt->bindParam(':area_d', $row['area_d'], PDO::PARAM_INT);
                    $stmt->bindParam(':typeid', $row['typeid'], PDO::PARAM_INT);
                    $stmt->bindParam(':description', $row['description'], PDO::PARAM_STR, strlen($row['description']));
                    $stmt->bindParam(':content', $row['content'], PDO::PARAM_STR, strlen($row['content']));
                    $stmt->bindParam(':maps', $maps, PDO::PARAM_STR);
                    $stmt->bindParam(':display_maps', $row['display_maps'], PDO::PARAM_INT);
                    $stmt->bindParam(':pricetype', $row['pricetype'], PDO::PARAM_INT);
                    $stmt->bindParam(':price', $row['price'], PDO::PARAM_STR);
                    $stmt->bindParam(':price1', $row['price1'], PDO::PARAM_STR);
                    $stmt->bindParam(':unitid', $row['unitid'], PDO::PARAM_INT);
                    $stmt->bindParam(':note', $row['note'], PDO::PARAM_STR, strlen($row['note']));
                    $stmt->bindParam(':exptime', $row['exptime'], PDO::PARAM_INT);
                    $stmt->bindParam(':auction', $row['auction'], PDO::PARAM_INT);
                    $stmt->bindParam(':auction_begin', $row['auction_begin'], PDO::PARAM_INT);
                    $stmt->bindParam(':auction_end', $row['auction_end'], PDO::PARAM_INT);
                    $stmt->bindParam(':auction_price_begin', $row['auction_price_begin'], PDO::PARAM_STR);
                    $stmt->bindParam(':auction_price_step', $row['auction_price_step'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_fullname', $row['contact_fullname'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_email', $row['contact_email'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_phone', $row['contact_phone'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_address', $row['contact_address'], PDO::PARAM_STR);
                } else {
                    $stmt = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET title = :title, alias = :alias, catid = :catid, area_p = :area_p, area_d = :area_d, typeid = :typeid, description = :description, pricetype = :pricetype, price = :price, price1 = :price1, unitid = :unitid, edittime = ' . NV_CURRENTTIME . ', exptime = :exptime, auction = :auction, auction_begin = :auction_begin, auction_end = :auction_end, auction_price_begin = :auction_price_begin, auction_price_step = :auction_price_step, groupview = :groupview WHERE id=' . $row['id']);
                    $stmt->bindParam(':title', $row['title'], PDO::PARAM_STR);
                    $stmt->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
                    $stmt->bindParam(':catid', $row['catid'], PDO::PARAM_INT);
                    $stmt->bindParam(':area_p', $row['area_p'], PDO::PARAM_INT);
                    $stmt->bindParam(':area_d', $row['area_d'], PDO::PARAM_INT);
                    $stmt->bindParam(':typeid', $row['typeid'], PDO::PARAM_INT);
                    $stmt->bindParam(':description', $row['description'], PDO::PARAM_STR, strlen($row['description']));
                    $stmt->bindParam(':pricetype', $row['pricetype'], PDO::PARAM_INT);
                    $stmt->bindParam(':price', $row['price'], PDO::PARAM_STR);
                    $stmt->bindParam(':price1', $row['price1'], PDO::PARAM_STR);
                    $stmt->bindParam(':unitid', $row['unitid'], PDO::PARAM_INT);
                    $stmt->bindParam(':exptime', $row['exptime'], PDO::PARAM_INT);
                    $stmt->bindParam(':auction', $row['auction'], PDO::PARAM_INT);
                    $stmt->bindParam(':auction_begin', $row['auction_begin'], PDO::PARAM_INT);
                    $stmt->bindParam(':auction_end', $row['auction_end'], PDO::PARAM_INT);
                    $stmt->bindParam(':auction_price_begin', $row['auction_price_begin'], PDO::PARAM_STR);
                    $stmt->bindParam(':auction_price_step', $row['auction_price_step'], PDO::PARAM_STR);
                    $stmt->bindParam(':groupview', $row['groupview'], PDO::PARAM_STR);
                }
                if ($stmt->execute()) {
                    $new_id = $row['id'];
                }
            }

            if ($new_id > 0) {

                if (empty($row['id'])) {

                    // thêm vào tùy biến dữ liệu
                    if (!empty($row['custom_field'])) {
                        $query_field['rowid'] = $new_id;
                        $db->query('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_info (' . implode(', ', array_keys($query_field)) . ') VALUES (' . implode(', ', array_values($query_field)) . ')');
                    }

                    // thêm vào bảng detail
                    $stmt = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_detail (id, content, maps, display_maps, note, groupcomment, contact_fullname, contact_email, contact_phone, contact_address) VALUES (:id, :content, :maps, :display_maps, :note, :groupcomment, :contact_fullname, :contact_email, :contact_phone, :contact_address)');
                    $stmt->bindParam(':id', $new_id, PDO::PARAM_INT);
                    $stmt->bindParam(':content', $row['content'], PDO::PARAM_STR, strlen($row['content']));
                    $stmt->bindParam(':maps', $maps, PDO::PARAM_STR);
                    $stmt->bindParam(':display_maps', $row['display_maps'], PDO::PARAM_STR);
                    $stmt->bindParam(':note', $row['note'], PDO::PARAM_STR);
                    $stmt->bindParam(':groupcomment', $row['groupcomment'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_fullname', $row['contact_fullname'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_email', $row['contact_email'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_phone', $row['contact_phone'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_address', $row['contact_address'], PDO::PARAM_STR);
                    $stmt->execute();

                    if (!$row['is_queue']) {
                        // Them vao hang doi dang facebook neu da duyet
                        nv_add_fb_queue($new_id);
                    } else {
                        // Them vao hang doi gui mail thong bao
                        $array_tomail = nv_get_mail_admin();
                        if (!empty($array_tomail)) {
                            $subject = $global_config['site_name'] . ' - ' . $lang_module['mail_queue_new'];
                            foreach ($array_tomail as $email) {
                                $message = '';
                                $message .= '<h2><strong>' . $row['title'] . '</strong></h2>';
                                $message .= '<blockquote>' . $row['content'] . '</blockquote>';
                                $url = NV_MY_DOMAIN . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&amp;id=' . $new_id;
                                $message .= '<a href="' . $url . '">' . $url . '</a>';
                                nv_add_mail_queue($email, $subject, $message);
                            }
                        }
                    }

                    // Cập nhật thu nhập Cộng tác viên
                    nv_freelance_update();
                } elseif ($is_queue_edit) {
                    // cập nhật tùy biến dữ liệu
                    if (!empty($row['custom_field'])) {
                        $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_info SET ' . implode(', ', $query_field) . ' WHERE rowid=' . $new_id);
                    }

                    $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET is_queue_edit=1 WHERE id=' . $new_id);
                    // Them vao hang doi gui mail thong bao
                    $array_tomail = nv_get_mail_admin();
                    if (!empty($array_tomail)) {
                        $subject = $global_config['site_name'] . ' - ' . $lang_module['mail_queue_edit_new'];
                        foreach ($array_tomail as $email) {
                            $message = '';
                            $message .= '<h2><strong>' . $row['title'] . '</strong></h2>';
                            $message .= '<blockquote>' . $row['content'] . '</blockquote>';
                            $url = NV_MY_DOMAIN . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&amp;id=' . $new_id;
                            $message .= '<a href="' . $url . '">' . $url . '</a>';
                            nv_add_mail_queue($email, $subject, $message);
                        }
                    }
                } else {
                    // cập nhật tùy biến dữ liệu
                    if (!empty($row['custom_field'])) {
                        $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_info SET ' . implode(', ', $query_field) . ' WHERE rowid=' . $new_id);
                    }

                    // cập nhật bảng detail
                    $stmt = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_detail SET content = :content, maps = :maps, display_maps = :display_maps, note = :note, groupcomment = :groupcomment, contact_fullname = :contact_fullname, contact_email = :contact_email, contact_phone = :contact_phone, contact_address = :contact_address WHERE id=' . $new_id);
                    $stmt->bindParam(':content', $row['content'], PDO::PARAM_STR, strlen($row['content']));
                    $stmt->bindParam(':maps', $maps, PDO::PARAM_STR);
                    $stmt->bindParam(':display_maps', $row['display_maps'], PDO::PARAM_INT);
                    $stmt->bindParam(':note', $row['note'], PDO::PARAM_STR);
                    $stmt->bindParam(':groupcomment', $row['groupcomment'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_fullname', $row['contact_fullname'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_email', $row['contact_email'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_phone', $row['contact_phone'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_address', $row['contact_address'], PDO::PARAM_STR);
                    $stmt->execute();
                }

                $auto_code = '';
                if (empty($row['code'])) {
                    $i = 1;
                    $format_code = !empty($array_config['code_format']) ? $array_config['code_format'] : 'T%06s';
                    $auto_code = vsprintf($format_code, $new_id);

                    $stmt = $db->prepare('SELECT id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE code= :code');
                    $stmt->bindParam(':code', $auto_code, PDO::PARAM_STR);
                    $stmt->execute();
                    while ($stmt->rowCount()) {
                        $i++;
                        $auto_code = vsprintf($format_code, ($new_id + $i));
                    }

                    $stmt = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET code= :code WHERE id=' . $new_id);
                    $stmt->bindParam(':code', $auto_code, PDO::PARAM_STR);
                    $stmt->execute();
                }

                if (empty($row['id']) and !empty($array_confif['usergrouppost'])) {
                    $array_confif['usergrouppost'] = explode(',', $array_confif['usergrouppost']);
                    foreach ($array_confif['usergrouppost'] as $grouppostid) {
                        $db->query('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_block (bid, id, weight) VALUES (' . $grouppostid . ', ' . $row['id'] . ', 0)');
                        nv_fix_block($grouppostid, false);
                    }
                }

                $array_image_path = array();
                if (!empty($row['images'])) {
                    foreach ($row['images'] as $index => $image) {
                        if (file_exists(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $image['path'])) {
                            $new_path = NV_ROOTDIR . '/' . $currentpath . '/' . $image['path'];

                            // Di chuyen vao thu muc upload
                            @nv_copyfile(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $image['path'], $new_path);

                            // Xoa file tmp
                            @nv_deletefile(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $image['path']);

                            // Tao anh thumb cho hinh anh moi
                            nv_market_viewImage($currentpath . '/' . $image['path']);

                            $array_image_path[$index] = str_replace(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/', '', $new_path);
                        } elseif (file_exists(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $image['path'])) {
                            $array_image_path[$index] = $image['path'];
                        }
                    }
                    $row['is_main'] = $nv_Request->get_int('is_main', 'post', 0);
                }

                if ($array_image_path != $row['images_old']) {
                    $sth = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_images (rowsid, path, description, is_main, weight) VALUES(:rowsid, :path, :description, :is_main, :weight)');
                    foreach ($array_image_path as $index => $image_path) {
                        $is_main = $row['is_main'] == $index ? 1 : 0;
                        if (!in_array($image_path, $row['images_old'])) {
                            $sth->bindParam(':rowsid', $new_id, PDO::PARAM_INT);
                            $sth->bindParam(':path', $image_path, PDO::PARAM_STR);
                            $sth->bindParam(':description', $row['images'][$index]['description'], PDO::PARAM_STR);
                            $sth->bindParam(':is_main', $is_main, PDO::PARAM_INT);

                            $weight = $db->query('SELECT max(weight) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_images WHERE rowsid=' . $new_id)->fetchColumn();
                            $weight = intval($weight) + 1;
                            $sth->bindParam(':weight', $weight, PDO::PARAM_INT);

                            $sth->execute();
                        } else {
                            $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_images SET is_main=' . $is_main . ' WHERE rowsid=' . $new_id . ' AND path=' . $db->quote($image_path));
                        }
                    }

                    foreach ($row['images_old'] as $image_path_old) {
                        if (!in_array($image_path_old, $array_image_path)) {
                            if (nv_delete_images($new_id, $image_path_old)) {
                                // Cap nhat lai thu tu
                                $weight = 0;
                                $sql = 'SELECT weight FROM ' . NV_PREFIXLANG . '_' . $module_data . '_images WHERE path = ' . $db->quote($image_path_old);
                                $result = $db->query($sql);
                                list ($weight) = $result->fetch(3);

                                if ($weight > 0) {
                                    $sql = 'SELECT path, weight FROM ' . NV_PREFIXLANG . '_' . $module_data . '_images WHERE weight >' . $weight;
                                    $result = $db->query($sql);
                                    while (list ($path, $weight) = $result->fetch(3)) {
                                        $weight--;
                                        $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_images SET weight=' . $weight . ' WHERE path=' . $db->quote($path));
                                    }
                                }
                            }
                        }
                    }
                    if ($is_queue_edit) {
                        $array_image_path = serialize($array_image_path);
                        $stmt = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_queue_edit SET images = :images WHERE rowsid=' . $new_id);
                        $stmt->bindParam(':images', $array_image_path, PDO::PARAM_STR);
                        $stmt->execute();
                    }
                } else {
                    $sth = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_images SET description = :description, is_main = :is_main WHERE rowsid=:rowsid AND path=:path');
                    foreach ($row['images'] as $index => $image) {
                        $is_main = $row['is_main'] == $index ? 1 : 0;
                        $sth->bindParam(':rowsid', $new_id, PDO::PARAM_INT);
                        $sth->bindParam(':path', $image['path'], PDO::PARAM_STR);
                        $sth->bindParam(':description', $image['description'], PDO::PARAM_STR);
                        $sth->bindParam(':is_main', $is_main, PDO::PARAM_INT);
                        $sth->execute();
                    }
                }

                // Cap nhat anh dai dien
                list ($row['homeimgfile'], $row['homeimgalt']) = $db->query('SELECT path, description FROM ' . NV_PREFIXLANG . '_' . $module_data . '_images WHERE rowsid=' . $new_id . ' AND is_main=1')->fetch(3);
                if ($row['homeimgfile']) {
                    $row['homeimgthumb'] = 0;
                    if (!nv_is_url($row['homeimgfile']) and nv_is_file(NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $row['homeimgfile'], NV_UPLOADS_DIR . '/' . $module_upload) === true) {
                        if (file_exists(NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module_upload . '/' . $row['homeimgfile'])) {
                            $row['homeimgthumb'] = 1;
                        } else {
                            $row['homeimgthumb'] = 2;
                        }
                    } elseif (nv_is_url($row['homeimgfile'])) {
                        $row['homeimgthumb'] = 3;
                    } else {
                        $row['homeimgfile'] = '';
                    }
                    $sth = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET homeimgfile=:homeimgfile, homeimgalt=:homeimgalt, homeimgthumb=:homeimgthumb WHERE id=' . $new_id);
                    $sth->bindParam(':homeimgfile', $row['homeimgfile'], PDO::PARAM_STR);
                    $sth->bindParam(':homeimgalt', $row['homeimgalt'], PDO::PARAM_STR);
                    $sth->bindParam(':homeimgthumb', $row['homeimgthumb'], PDO::PARAM_INT);
                    $sth->execute();

                    if ($is_queue_edit) {
                        $sth = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_queue_edit SET homeimgfile=:homeimgfile, homeimgalt=:homeimgalt, homeimgthumb=:homeimgthumb WHERE rowsid=' . $new_id);
                        $sth->bindParam(':homeimgfile', $row['homeimgfile'], PDO::PARAM_STR);
                        $sth->bindParam(':homeimgalt', $row['homeimgalt'], PDO::PARAM_STR);
                        $sth->bindParam(':homeimgthumb', $row['homeimgthumb'], PDO::PARAM_INT);
                        $sth->execute();
                    }
                }
                // Cap nhat tu khoa
                if ($row['keywords'] != $row['keywords_old']) {
                    $keywords = explode(',', $row['keywords']);
                    $keywords = array_map('strip_punctuation', $keywords);
                    $keywords = array_map('trim', $keywords);
                    $keywords = array_diff($keywords, array(
                        ''
                    ));
                    $keywords = array_unique($keywords);

                    foreach ($keywords as $keyword) {
                        $keyword = str_replace('&', ' ', $keyword);
                        if (!in_array($keyword, $array_keywords_old)) {
                            $alias_i = ($module_config[$module_name]['tags_alias']) ? change_alias($keyword) : str_replace(' ', '-', $keyword);
                            $alias_i = nv_strtolower($alias_i);
                            $sth = $db->prepare('SELECT tid, alias, description, keywords FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags where alias= :alias OR FIND_IN_SET(:keyword, keywords)>0');
                            $sth->bindParam(':alias', $alias_i, PDO::PARAM_STR);
                            $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                            $sth->execute();

                            list ($tid, $alias, $keywords_i) = $sth->fetch(3);
                            if (empty($tid)) {
                                $array_insert = array();
                                $array_insert['alias'] = $alias_i;
                                $array_insert['keyword'] = $keyword;

                                $tid = $db->insert_id("INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_tags (numnews, alias, description, image, keywords) VALUES (1, :alias, '', '', :keyword)", "tid", $array_insert);
                            } else {
                                if ($alias != $alias_i) {
                                    if (!empty($keywords_i)) {
                                        $keyword_arr = explode(',', $keywords_i);
                                        $keyword_arr[] = $keyword;
                                        $keywords_i2 = implode(',', array_unique($keyword_arr));
                                    } else {
                                        $keywords_i2 = $keyword;
                                    }
                                    if ($keywords_i != $keywords_i2) {
                                        $sth = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tags SET keywords= :keywords WHERE tid =' . $tid);
                                        $sth->bindParam(':keywords', $keywords_i2, PDO::PARAM_STR);
                                        $sth->execute();
                                    }
                                }
                                $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tags SET numnews = numnews+1 WHERE tid = ' . $tid);
                            }

                            // insert keyword for table _tags_id
                            try {
                                $sth = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id (id, tid, keyword) VALUES (' . $new_id . ', ' . intval($tid) . ', :keyword)');
                                $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                                $sth->execute();
                            } catch (PDOException $e) {
                                $sth = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id SET keyword = :keyword WHERE id = ' . $new_id . ' AND tid=' . intval($tid));
                                $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                                $sth->execute();
                            }
                            unset($array_keywords_old[$tid]);
                        }
                    }

                    foreach ($array_keywords_old as $tid => $keyword) {
                        if (!in_array($keyword, $keywords)) {
                            $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tags SET numnews = numnews-1 WHERE tid = ' . $tid);
                            $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE id = ' . $new_id . ' AND tid=' . $tid);
                        }
                    }

                    if ($is_queue_edit) {
                        $sth = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_queue_edit SET keywords = :keywords WHERE rowsid=' . $new_id);
                        $sth->bindParam(':keywords', serialize($keywords), PDO::PARAM_STR);
                        $sth->execute();
                    }
                }

                $nv_Request->set_Session('catid', $row['catid']);

                $nv_Cache->delMod($module_name);

                if (!empty($redirect)) {
                    $url_back = nv_redirect_decrypt($redirect);
                } elseif (!empty($userid)) {
                    $url_back = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['userarea'], true);
                } else {
                    $url_back = NV_BASE_SITEURL;
                }

                $title = $lang_module['rows_insert_success_title'];
                $content = $lang_module['rows_insert_success_content'];

                if (!empty($row['id'])) {
                    $title = $lang_module['rows_update_success_title'];
                    if (!$is_queue_edit) {
                        $content = $lang_module['rows_update_success_content'];
                    } else {
                        $content = $lang_module['rows_update_success_content_queue'];
                    }
                } elseif ($row['is_queue']) {
                    $content = $lang_module['rows_insert_success_content_queue'];
                }

                $contents = nv_theme_alert($title, $content, 'info', $url_back, $lang_module['manage']);
                include NV_ROOTDIR . '/includes/header.php';
                echo nv_site_theme($contents);
                include NV_ROOTDIR . '/includes/footer.php';
            }
        } catch (PDOException $e) {
            trigger_error($e->getMessage());
        }
    }
}

if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor') and $is_editor) {
    $row['content'] = htmlspecialchars(nv_editor_br2nl($row['content']));
    $row['content'] = nv_aleditor('content', '100%', $ispopup ? '160px' : '300px', $row['content'], 'Basic');
} else {
    $row['content'] = htmlspecialchars(nv_br2nl($row['content']));
    $row['content'] = '<textarea style="width:100%;height:120px" class="form-control required" required="required" name="content">' . $row['content'] . '</textarea>';
}

$row['price'] = !empty($row['price']) ? $row['price'] : '';
$row['price1'] = !empty($row['price1']) ? $row['price1'] : '';
$row['exptimef'] = !empty($row['exptime']) ? nv_date('d/m/Y', $row['exptime']) : '';
$row['auction_beginf'] = !empty($row['auction_begin']) ? nv_date('d/m/Y', $row['auction_begin']) : '';
$row['auction_endf'] = !empty($row['auction_end']) ? nv_date('d/m/Y', $row['auction_end']) : '';
$row['ck_auction'] = $row['auction'] ? 'checked="checked"' : '';
$row['auction_price_begin'] = !empty($row['auction_price_begin']) ? $row['auction_price_begin'] : '';
$row['auction_price_step'] = !empty($row['auction_price_step']) ? $row['auction_price_step'] : '';
$row['auction_style'] = !$row['auction'] ? 'style="display: none"' : '';

if (empty($userid)) {
    $link_users = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users';
    $lang_module['content_guest_note'] = sprintf($lang_module['content_guest_note'], $link_users);
}

$lang_module['terms_note'] = sprintf($lang_module['terms_note'], $ispopup);

if (!empty($array_config['maps_appid']) && $row['display_maps']) {
    $row['maps'] = !empty($row['maps']) ? unserialize($row['maps']) : array();
    $row['ck_display_maps'] = $row['display_maps'] ? 'checked="checked"' : '';
}

$xtpl = new XTemplate('content.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('ISPOPUP', $ispopup);
$xtpl->assign('MONEY_UNIT', $array_config['money_unit']);
$xtpl->assign('REDIRECT', $redirect);
$xtpl->assign('UPLOAD_URL', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=upload&token=' . md5($nv_Request->session_id . $global_config['sitekey']));
$xtpl->assign('URL_CONTENT', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['content']);

if (!empty($array_market_cat)) {
    foreach ($array_market_cat as $catid => $value) {
        $value['space'] = '';
        if ($value['lev'] > 0) {
            for ($i = 1; $i <= $value['lev']; $i++) {
                $value['space'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
        }
        $value['selected'] = $catid == $row['catid'] ? ' selected="selected"' : '';

        $xtpl->assign('CAT', $value);
        $xtpl->parse('main.cat');
    }
}

require_once NV_ROOTDIR . '/modules/location/location.class.php';
$location = new Location();
$location->set('SelectCountryid', $array_config['countryid']);
$location->set('IsDistrict', 1);
$location->set('BlankTitleProvince', 1);
$location->set('BlankTitleDistrict', 1);
$location->set('NameProvince', 'area_p');
$location->set('NameDistrict', 'area_d');
$location->set('SelectProvinceid', $row['area_p']);
$location->set('SelectDistrictid', $row['area_d']);

if ($ispopup) {
    $location->set('Index', 999);
}
$xtpl->assign('LOCATION', $location->buildInput());

if ($global_config['captcha_type'] == 2) {
    $xtpl->assign('RECAPTCHA_ELEMENT', 'recaptcha' . nv_genpass(8));
    $xtpl->assign('N_CAPTCHA', $lang_global['securitycode1']);
    $xtpl->parse('main.recaptcha');
} else {
    $xtpl->assign('GFX_WIDTH', NV_GFX_WIDTH);
    $xtpl->assign('GFX_HEIGHT', NV_GFX_HEIGHT);
    $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
    $xtpl->assign('CAPTCHA_REFRESH', $lang_global['captcharefresh']);
    $xtpl->assign('NV_GFX_NUM', NV_GFX_NUM);
    $xtpl->parse('main.captcha');
}

if (!$ispopup) {

    if (!empty($array_type)) {
        $row['typeid'] = !empty($row['typeid']) ? $row['typeid'] : array_keys($array_type)[0];
        foreach ($array_type as $type) {
            $type['checked'] = $type['id'] == $row['typeid'] ? 'checked="checked"' : '';
            $xtpl->assign('TYPE', $type);
            $xtpl->parse('main.typeid.loop');
        }
    }
    $xtpl->parse('main.typeid');

    $hour = !empty($row['exptime']) ? date('H', $row['exptime']) : 0;
    for ($i = 0; $i <= 23; $i++) {
        $sl = $i == $hour ? 'selected="selected"' : '';
        $xtpl->assign('HOUR', array(
            'index' => $i,
            'selected' => $sl
        ));
        $xtpl->parse('main.hour');
    }

    $min = !empty($row['exptime']) ? date('i', $row['exptime']) : 0;
    for ($i = 0; $i <= 59; $i++) {
        $sl = $i == $min ? 'selected="selected"' : '';
        $xtpl->assign('MIN', array(
            'index' => $i,
            'selected' => $sl
        ));
        $xtpl->parse('main.min');
    }

    if (!empty($row['images'])) {
        foreach ($row['images'] as $index => $image) {
            if (file_exists(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $image['path'])) {
                $image['index'] = $index;
                $image['basename'] = basename($image['path']);
                $image['homeimgfile'] = $image['path'];

                if (file_exists(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_upload . '/' . $image['path'])) {
                    $image['path'] = NV_BASE_SITEURL . NV_ASSETS_DIR . '/' . $module_upload . '/' . $image['path'];
                } else {
                    $image['path'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $image['path'];
                }

                $image['ch_is_main'] = $image['is_main'] ? 'checked="checked"' : '';

                $xtpl->assign('IMAGE', $image);

                if ($image['is_main']) {
                    $xtpl->parse('main.images.loop.is_main');
                }

                $xtpl->parse('main.images.loop');
            }
        }
    }
    $xtpl->assign('COUNT', count($row['images']));
    $xtpl->assign('USER_CONFIG', nv_user_config(true));
    $xtpl->parse('main.images');
    $xtpl->parse('main.images_js');

    if ($array_config['auction'] and nv_user_in_groups($array_config['auction_group'])) {
        // thoi gian bat dau dau gia
        $hour = !empty($row['auction_begin']) ? date('H', $row['auction_begin']) : 0;
        for ($i = 0; $i <= 23; $i++) {
            $sl = $i == $hour ? 'selected="selected"' : '';
            $xtpl->assign('HOUR', array(
                'index' => $i,
                'selected' => $sl
            ));
            $xtpl->parse('main.auction.auction_begin_hour');
        }

        $min = !empty($row['auction_begin']) ? date('i', $row['auction_begin']) : 0;
        for ($i = 0; $i <= 59; $i++) {
            $sl = $i == $min ? 'selected="selected"' : '';
            $xtpl->assign('MIN', array(
                'index' => $i,
                'selected' => $sl
            ));
            $xtpl->parse('main.auction.auction_begin_min');
        }

        // thoi gian ket thuc dau gia
        $hour = !empty($row['auction_end']) ? date('H', $row['auction_end']) : 0;
        for ($i = 0; $i <= 23; $i++) {
            $sl = $i == $hour ? 'selected="selected"' : '';
            $xtpl->assign('HOUR', array(
                'index' => $i,
                'selected' => $sl
            ));
            $xtpl->parse('main.auction.auction_end_hour');
        }

        $min = !empty($row['auction_end']) ? date('i', $row['auction_end']) : 0;
        for ($i = 0; $i <= 59; $i++) {
            $sl = $i == $min ? 'selected="selected"' : '';
            $xtpl->assign('MIN', array(
                'index' => $i,
                'selected' => $sl
            ));
            $xtpl->parse('main.auction.auction_end_min');
        }
        $xtpl->parse('main.auction');
    }
    $xtpl->parse('main.exptime');
    $xtpl->parse('main.note');
    $xtpl->parse('main.description');

    if (!empty($array_config['maps_appid'])) {
        if ($row['display_maps']) {
            $xtpl->assign('MAPS_APPID', $array_config['maps_appid']);
            $xtpl->assign('MAPS', nv_market_initializeMap($row['maps']));
            $xtpl->parse('main.maps');
        }
    }
} else {
    $xtpl->parse('main.fullform');
    $xtpl->parse('main.popup');
}

if ($row['catid'] and !empty($array_market_cat[$row['catid']]['form'])) {
    $form = $array_market_cat[$row['catid']]['form'];
    if (nv_is_file(NV_BASE_SITEURL . NV_ASSETS_DIR . '/' . $module_name . '/files_tpl/cat_form_' . $form . '.tpl', NV_ASSETS_DIR . '/' . $module_name)) {
        $xtpl->assign('DATACUSTOM_FORM', nv_show_custom_form($row['id'], $form, $row['custom_field']));
    }
}

if (empty($userid)) {
    $xtpl->parse('main.guest_note');
    $xtpl->parse('main.editor_guest_note');
}

if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

if (empty($row['id']) && !empty($array_config['similar_content'])) {
    $xtpl->parse('main.check_similar_content');
}

if (!empty($row['catid'])) {
    $template = NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file;
    $xtpl->assign('PRICETYPE', nv_load_pricetype($row, $template));
}

if ($row['is_queue'] == 2) {
    $xtpl->parse('main.requeue');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['content_add'];

if (!empty($userid)) {
    $array_mod_title[] = array(
        'title' => $lang_module['manage'],
        'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['userarea']
    );
}

$array_mod_title[] = array(
    'title' => $page_title,
    'link' => ''
);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents, ($ispopup and empty($error)) ? 0 : 1);
include NV_ROOTDIR . '/includes/footer.php';