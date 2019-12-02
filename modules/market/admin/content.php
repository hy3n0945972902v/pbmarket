<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2016 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 21 Nov 2016 01:26:01 GMT
 */
if (!defined('NV_IS_FILE_ADMIN')) die('Stop!!!');

if ($nv_Request->isset_request('get_alias_title', 'post')) {
    $alias = $nv_Request->get_title('get_alias_title', 'post', '');
    $alias = change_alias($alias);
    if ($array_config['tags_alias_lower']) {
        $alias = strtolower($alias);
    }
    die($alias);
}

if ($nv_Request->isset_request('get_keywords', 'post,get')) {
    $q = $nv_Request->get_title('q', 'post, get', '');

    if (empty($q)) {
        return;
    }

    $db_slave->sqlreset()
        ->select('tid, keywords')
        ->from(NV_PREFIXLANG . '_' . $module_data . '_tags')
        ->where('alias LIKE :alias OR keywords LIKE :keywords')
        ->order('alias ASC')
        ->limit(50);

    $sth = $db_slave->prepare($db_slave->sql());
    $sth->bindValue(':alias', '%' . $q . '%', PDO::PARAM_STR);
    $sth->bindValue(':keywords', '%' . $q . '%', PDO::PARAM_STR);
    $sth->execute();

    $array_data = array();
    while (list ($id, $keywords) = $sth->fetch(3)) {
        $array_data[] = array(
            'id' => $keywords,
            'text' => $keywords
        );
    }

    header('Cache-Control: no-cache, must-revalidate');
    header('Content-type: application/json');

    ob_start('ob_gzhandler');
    echo json_encode($array_data);
    exit();
}

$row = array();
$error = array();
$array_keywords_old = array();
$redirect = $nv_Request->get_title('redirect', 'get', '');
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);
$groups_list = nv_groups_list();
$id_block_content = array();

$username_alias = change_alias($admin_info['username']);
$currentpath = nv_upload_user_path($username_alias);

$array_block_cat_module = array();
$sql = 'SELECT bid, adddefault, title, prior FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block_cat ORDER BY weight ASC';
$result = $db->query($sql);
while (list ($bid_i, $adddefault_i, $title_i, $prior_i) = $result->fetch(3)) {
    $array_block_cat_module[$bid_i] = array(
        'title' => $title_i,
        'prior' => $prior_i
    );
    if ($adddefault_i) {
        $id_block_content[] = $bid_i;
    }
}

if ($row['id'] > 0) {
    $row = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $row['id'])->fetch();
    if (empty($row)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
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

    $id_block_content = array();
    $sql = 'SELECT bid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block where id=' . $row['id'];
    $result = $db->query($sql);
    while (list ($bid_i) = $result->fetch(3)) {
        $id_block_content[] = $bid_i;
    }

    // Lich su kiem duyet
    $array_users = $row['queue_logs'] = array();
    if ($row['is_queue'] != 0) {
        $result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_queue_logs WHERE rowsid=' . $row['id'] . ' ORDER BY addtime DESC');
        while ($_row = $result->fetch()) {
            if (!isset($array_users[$_row['userid']])) {
                $user_info = $db->query('SELECT userid, first_name, last_name, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $_row['userid'])->fetch();
                $array_users[$user_info['userid']] = $user_info;
            } else {
                $user_info = $array_users[$_row['userid']];
            }
            $_row['name'] = nv_show_name_user($user_info['first_name'], $user_info['last_name'], $user_info['username']);
            $row['queue_logs'][] = $_row;
        }
    }

    // Từ khóa
    $_query = $db->query('SELECT tid, keyword FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE id=' . $row['id'] . ' ORDER BY keyword ASC');
    while ($_row = $_query->fetch()) {
        $array_keywords_old[$_row['tid']] = $_row['keyword'];
    }
    $row['keywords'] = implode(', ', $array_keywords_old);
    $row['keywords_old'] = $row['keywords'];

    // lấy giá trị mới nếu sửa bài cần kiểm duyệt
    if ($row['is_queue_edit']) {
        $array_tmp = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_queue_edit WHERE rowsid=' . $row['id'])->fetch();
        if ($array_tmp) {
            $row = array_replace($row, $array_tmp);
        }
    }

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
    $row['catid'] = 0;
    $row['groupid'] = '';
    $row['area_p'] = $array_config['province_default'];
    $row['area_d'] = 0;
    $row['address'] = '';
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
    $row['contact_fullname'] = '';
    $row['contact_email'] = '';
    $row['contact_phone'] = '';
    $row['contact_address'] = '';
    $row['is_queue'] = 0;
    $row['is_queue_edit'] = 0;
    $row['queue_reason'] = '';
    $row['queue_reasonid'] = 0;
    $row['status_admin'] = 1;
    $row['status'] = 1;
    $row['userid'] = $admin_info['userid'];
    $row['custom_field'] = $row['maps'] = $row['custom_field'] = $row['images'] = $row['images_old'] = array();
    $row['display_maps'] = 0;
    $row['keywords'] = '';
    $row['keywords_old'] = '';
    $id_block_content = array();
}

$row['remove_link'] = 1;
$row['queue'] = 1;
if ($row['is_queue'] or $row['is_queue_edit']) {
    $_sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_queue_reason WHERE status=1';
    $array_reason = $nv_Cache->db($_sql, 'id', $module_name);
}

if ($nv_Request->isset_request('submit', 'post')) {
    $row['code'] = $nv_Request->get_title('code', 'post', '');
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['alias'] = $nv_Request->get_title('alias', 'post', '');
    $row['catid'] = $nv_Request->get_int('catid', 'post', 0);
    $row['area_p'] = $nv_Request->get_int('area_p', 'post', 0);
    $row['area_d'] = $nv_Request->get_int('area_d', 'post', 0);
    $row['address'] = $nv_Request->get_title('address', 'post', '');
    $row['typeid'] = $nv_Request->get_int('typeid', 'post', 0);
    $row['description'] = $nv_Request->get_textarea('description', '');
    $row['content'] = $nv_Request->get_editor('content', '', NV_ALLOWED_HTML_TAGS);
    $row['pricetype'] = $nv_Request->get_int('pricetype', 'post', 0);
    $row['price'] = $nv_Request->get_title('price', 'post', 0);
    $row['price'] = preg_replace('/[^0-9]/', '', $row['price']);
    $row['price1'] = $nv_Request->get_title('price1', 'post', 0);
    $row['price1'] = preg_replace('/[^0-9]/', '', $row['price1']);
    $row['unitid'] = $nv_Request->get_title('unitid', 'post', 0);
    $row['note'] = $nv_Request->get_textarea('note', '');
    $row['queue'] = $nv_Request->get_int('queue', 'post', 0);
    $row['queue_reason'] = $nv_Request->get_textarea('queue_reason', '');
    $row['queue_reasonid'] = $nv_Request->get_int('queue_reasonid', 'post', 0);
    $row['userid'] = $nv_Request->get_int('userid', 'post', 0);
    $row['images'] = $nv_Request->get_array('images', 'post');
    $row['remove_link'] = $nv_Request->get_int('remove_link', 'post', 0);
    $row['custom_field'] = $nv_Request->get_array('custom', 'post');
    $row['maps'] = $nv_Request->get_array('maps', 'post', array());
    $row['display_maps'] = $nv_Request->get_int('display_maps', 'post', 0);

    $_groups_post = $nv_Request->get_array('groupview', 'post', array());
    $row['groupview'] = !empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $_groups_post = $nv_Request->get_array('groupcomment', 'post', array());
    $row['groupcomment'] = !empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $row['contact_fullname'] = $nv_Request->get_title('contact_fullname', 'post', '');
    $row['contact_email'] = $nv_Request->get_title('contact_email', 'post', '');
    $row['contact_phone'] = $nv_Request->get_title('contact_phone', 'post', '');
    $row['contact_address'] = $nv_Request->get_title('contact_address', 'post', '');

    $row['id_block_content_post'] = array_unique($nv_Request->get_typed_array('bids', 'post', 'int', array()));
    $row['groupid'] = implode(',', $row['id_block_content_post']);

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

    $row['alias'] = empty($row['alias']) ? change_alias($row['title']) : $row['alias'];
    if ($array_config['tags_alias_lower']) {
        $row['alias'] = strtolower($row['alias']);
    }

    $row['keywords'] = $nv_Request->get_array('keywords', 'post', '');
    $row['keywords'] = implode(', ', $row['keywords']);

    if (!$array_config['allow_auto_code'] and empty($row['code'])) {
        $error[] = $lang_module['error_required_code'];
    } elseif (!preg_match('/^[a-zA-Z0-9]*$/', $row['code'])) {
        $error[] = $lang_module['error_vaild_code'];
    } elseif (empty($row['id']) and $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE code=' . $db->quote($row['code']))
        ->fetchColumn() > 0) {
        $error[] = $lang_module['error_duplicate_code'];
    } elseif (empty($row['title'])) {
        $error[] = $lang_module['error_required_title'];
    } elseif (empty($row['catid'])) {
        $error[] = $lang_module['error_required_catid'];
    } elseif ($row['pricetype'] == 0 and empty($row['price'])) {
        $error[] = $lang_module['error_required_price'];
    } elseif (empty($row['content'])) {
        $error[] = $lang_module['error_required_content'];
    } elseif (empty($row['id']) and !nv_check_similar_content($row['content'])) {
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
    }

    $query_field = array();
    if (isset($array_market_cat[$row['catid']]) and $array_market_cat[$row['catid']]['form'] != '') {
        require NV_ROOTDIR . '/modules/' . $module_file . '/fields.check.php';
    }

    if (empty($error)) {
        // loại bỏ link trong nội dung
        if ($array_config['remove_link'] && $row['remove_link']) {
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
            $new_id = 0;
            $maps = !empty($row['maps']) ? serialize($row['maps']) : '';
            if (empty($row['id'])) {
                $_sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_rows (code, title, alias, catid, groupid, area_p, area_d, address, typeid, description, pricetype, price, price1, unitid, addtime, exptime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, groupview, userid, ordertime) VALUES (:code, :title, :alias, :catid, :groupid, :area_p, :area_d, :address, :typeid, :description, :pricetype, :price, :price1, :unitid, ' . NV_CURRENTTIME . ', :exptime, :auction, :auction_begin, :auction_end, :auction_price_begin, :auction_price_step, :groupview, :userid, ' . NV_CURRENTTIME . ')';
                $data_insert = array();
                $data_insert['code'] = $row['code'];
                $data_insert['title'] = $row['title'];
                $data_insert['alias'] = $row['alias'];
                $data_insert['catid'] = $row['catid'];
                $data_insert['groupid'] = $row['groupid'];
                $data_insert['area_p'] = $row['area_p'];
                $data_insert['area_d'] = $row['area_d'];
                $data_insert['address'] = $row['address'];
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
                $data_insert['userid'] = $row['userid'];
                $new_id = $db->insert_id($_sql, 'id', $data_insert);
            } else {
                if ($row['queue'] == 1) {
                    $row['is_queue'] = 0;
                    $row['ordertime'] = NV_CURRENTTIME;
                } elseif ($row['queue'] == 2) {
                    $row['is_queue'] = 2;
                }

                $stmt = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET title = :title, alias = :alias, catid = :catid, groupid = :groupid, area_p = :area_p, area_d = :area_d, address = :address, typeid = :typeid, description = :description, pricetype = :pricetype, price = :price, price1 = :price1, unitid = :unitid, edittime = ' . NV_CURRENTTIME . ', exptime = :exptime, auction = :auction, auction_begin = :auction_begin, auction_end = :auction_end, auction_price_begin = :auction_price_begin, auction_price_step = :auction_price_step, groupview = :groupview, userid = :userid, is_queue = :is_queue, ordertime = :ordertime WHERE id=' . $row['id']);
                $stmt->bindParam(':title', $row['title'], PDO::PARAM_STR);
                $stmt->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
                $stmt->bindParam(':catid', $row['catid'], PDO::PARAM_INT);
                $stmt->bindParam(':groupid', $row['groupid'], PDO::PARAM_STR);
                $stmt->bindParam(':area_p', $row['area_p'], PDO::PARAM_INT);
                $stmt->bindParam(':area_d', $row['area_d'], PDO::PARAM_INT);
                $stmt->bindParam(':address', $row['address'], PDO::PARAM_STR);
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
                $stmt->bindParam(':userid', $row['userid'], PDO::PARAM_INT);
                $stmt->bindParam(':is_queue', $row['is_queue'], PDO::PARAM_INT);
                $stmt->bindParam(':ordertime', $row['ordertime'], PDO::PARAM_INT);
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
                    $stmt->bindParam(':display_maps', $row['display_maps'], PDO::PARAM_INT);
                    $stmt->bindParam(':note', $row['note'], PDO::PARAM_STR);
                    $stmt->bindParam(':groupcomment', $row['groupcomment'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_fullname', $row['contact_fullname'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_email', $row['contact_email'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_phone', $row['contact_phone'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_address', $row['contact_address'], PDO::PARAM_STR);
                    $stmt->execute();

                    // Them vao hang doi dang facebook neu da duyet
                    if (!$row['is_queue']) {
                        nv_add_fb_queue($new_id);
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

                if ($array_config['allow_auto_code']) {
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
                }

                $id_block_content_new = array_diff($row['id_block_content_post'], $id_block_content);
                $id_block_content_del = array_diff($id_block_content, $row['id_block_content_post']);

                $array_block_fix = array();

                foreach ($id_block_content_new as $bid_i) {
                    nv_rows_update_group($bid_i, $new_id);
                    $array_block_fix[] = $bid_i;
                }
                foreach ($id_block_content_del as $bid_i) {
                    nv_rows_update_group($bid_i, $new_id, 1);
                    $array_block_fix[] = $bid_i;
                }

                $array_block_fix = array_unique($array_block_fix);
                foreach ($array_block_fix as $bid_i) {
                    nv_fix_block($bid_i, false);
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
                }

                // Cap nhat lich su duyet tin
                if ($row['queue'] > 0) {
                    if ($row['queue'] == 1) {
                        $queue = 1;

                        // thêm tự động đăng fb
                        nv_add_fb_queue($new_id);

                        // cập nhật trạng thái duyệt tin
                        nv_queue_edit_accept($new_id);
                    } elseif ($row['queue'] == 2) {
                        $queue = 2;
                    }

                    $contact_email = $row['contact_email'];
                    if ($row['userid'] > 0) {
                        $contact_email = $db->query('SELECT email FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $row['userid'])->fetchColumn();
                    }
                    if (!empty($contact_email)) {
                        $queue_info = array();
                        $queue_info['site_name'] = $global_config['site_name'];
                        $queue_info['site_description'] = $global_config['site_description'];
                        $queue_info['title'] = $row['title'];
                        $queue_info['link'] = NV_MY_DOMAIN . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_market_cat[$row['catid']]['alias'] . '/' . $row['alias'] . '-' . $row['id'] . $global_config['rewrite_exturl'], true);
                        $queue_info['fullname'] = !empty($row['contact_fullname']) ? $row['contact_fullname'] : $contact_email;
                        $queue_info['reason'] = !empty($row['queue_reasonid']) ? $array_reason[$row['queue_reasonid']]['title'] : '';
                        $queue_info['reason_note'] = !empty($row['queue_reason']) ? $row['queue_reason'] : '';
                        $queue_info['queue_status'] = $lang_module['queue_type_' . $row['queue']];
                        $queue_info['queue'] = $queue;

                        $message = nv_sendmail_queue($queue_info);

                        $subject = $global_config['site_name'] . ' - ' . $lang_module['queue_mail_subject'];
                        nv_add_mail_queue($contact_email, $subject, $message);
                    }

                    /*
                     * 1: chờ kiểm duyệt
                     * 0: đã duyệt
                     * 2: từ chối duyệt
                     */
                    $sth = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_queue_logs(rowsid, queue, reason, reasonid, addtime, userid) VALUES (' . $new_id . ', ' . $queue . ', :reason, :reasonid, ' . NV_CURRENTTIME . ', ' . $admin_info['userid'] . ')');
                    $sth->bindParam(':reason', $row['queue_reason'], PDO::PARAM_STR);
                    $sth->bindParam(':reasonid', $row['queue_reasonid'], PDO::PARAM_INT);
                    $sth->execute();
                } else {
                    $count = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_queue_edit WHERE rowsid=' . $row['id'])->fetchColumn();
                    if ($count > 0) {
                        $stmt = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_queue_edit SET title = :title, catid = :catid, area_p = :area_p, area_d = :area_d, address = :address, typeid = :typeid, description = :description, content = :content, pricetype = :pricetype, price = :price, price1 = :price1, unitid = :unitid, note = :note, exptime = :exptime, auction = :auction, auction_begin = :auction_begin, auction_end = :auction_end, auction_price_begin = :auction_price_begin, auction_price_step = :auction_price_step, contact_fullname = :contact_fullname, contact_email = :contact_email, contact_phone = :contact_phone, contact_address = :contact_address WHERE rowsid = :rowsid');
                        $stmt->bindParam(':rowsid', $row['id'], PDO::PARAM_INT);
                        $stmt->bindParam(':title', $row['title'], PDO::PARAM_STR);
                        $stmt->bindParam(':catid', $row['catid'], PDO::PARAM_INT);
                        $stmt->bindParam(':area_p', $row['area_p'], PDO::PARAM_INT);
                        $stmt->bindParam(':area_d', $row['area_d'], PDO::PARAM_INT);
                        $stmt->bindParam(':address', $row['address'], PDO::PARAM_STR);
                        $stmt->bindParam(':typeid', $row['typeid'], PDO::PARAM_INT);
                        $stmt->bindParam(':description', $row['description'], PDO::PARAM_STR, strlen($row['description']));
                        $stmt->bindParam(':content', $row['content'], PDO::PARAM_STR, strlen($row['content']));
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
                        $stmt->execute();
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
                }

                // Cập nhật độ ưu tiên
                $array_prior = array();
                if (!empty($row['id_block_content_post'])) {
                    foreach ($row['id_block_content_post'] as $bid_i) {
                        $array_prior[] = $array_block_cat_module[$bid_i]['prior'];
                    }
                }
                $prior = !empty($array_prior) ? max($array_prior) : 0;
                $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET prior=' . $prior . ' WHERE id=' . $new_id);

                $nv_Cache->delMod($module_name);

                if (!empty($redirect)) {
                    Header('Location: ' . nv_redirect_decrypt($redirect));
                    die();
                }

                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
                die();
            }
        } catch (PDOException $e) {
            trigger_error($e->getMessage());
        }
    }
    $id_block_content = $row['id_block_content_post'];
}

if (defined('NV_EDITOR')) {
    require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}

if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor')) {
    $row['content'] = htmlspecialchars(nv_editor_br2nl($row['content']));
    $row['content'] = nv_aleditor('content', '100%', '300px', $row['content']);
} else {
    $row['content'] = htmlspecialchars(nv_nl2br($row['content']));
    $row['content'] = '<textarea style="width:100%;height:300px" name="content">' . $row['content'] . '</textarea>';
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
$row['queue_reason_style'] = $row['queue'] != 2 ? 'style="display: none"' : '';
$row['ck_remove_link'] = $row['remove_link'] ? 'checked="checked"' : '';

if (!empty($array_config['maps_appid']) && $row['display_maps']) {
    $row['maps'] = !empty($row['maps']) ? unserialize($row['maps']) : array();
    $row['ck_display_maps'] = $row['display_maps'] ? 'checked="checked"' : '';
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('MONEY_UNIT', $array_config['money_unit']);
$xtpl->assign('REDIRECT', $redirect);
$xtpl->assign('UPLOAD_URL', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=upload&token=' . md5($nv_Request->session_id . $global_config['sitekey']));

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
$xtpl->assign('LOCATION', $location->buildInput());

if (!empty($array_type)) {
    $row['typeid'] = !empty($row['typeid']) ? $row['typeid'] : array_keys($array_type)[0];
    foreach ($array_type as $type) {
        $type['checked'] = $type['id'] == $row['typeid'] ? 'checked="checked"' : '';
        $xtpl->assign('TYPE', $type);
        $xtpl->parse('main.type');
    }
}

// thoi gian het han
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

$groups_view = explode(',', $row['groupview']);
foreach ($groups_list as $group_id => $grtl) {
    $_groups_view = array(
        'value' => $group_id,
        'checked' => in_array($group_id, $groups_view) ? ' checked="checked"' : '',
        'title' => $grtl
    );
    $xtpl->assign('GROUPS_VIEW', $_groups_view);
    $xtpl->parse('main.groups_view');
}

$groups_comm = explode(',', $row['groupcomment']);
foreach ($groups_list as $group_id => $grtl) {
    $_groups_comm = array(
        'value' => $group_id,
        'checked' => in_array($group_id, $groups_comm) ? ' checked="checked"' : '',
        'title' => $grtl
    );
    $xtpl->assign('GROUPS_COMM', $_groups_comm);
    $xtpl->parse('main.groups_comment');
}

if ($row['is_queue'] or $row['is_queue_edit']) {
    $array_queue_action = array(
        1 => $lang_module['queue_action_1'],
        2 => $lang_module['queue_action_2'],
        0 => $lang_module['queue_action_0']
    );
    foreach ($array_queue_action as $index => $value) {
        $sl = $index == $row['queue'] ? 'checked="checked"' : '';
        $xtpl->assign('QUEUE_ACTION', array(
            'index' => $index,
            'value' => $value,
            'checked' => $sl
        ));
        $xtpl->parse('main.queue.queue_action');
    }

    if (!empty($array_reason)) {
        foreach ($array_reason as $reason) {
            $xtpl->assign('REASON', $reason);
            $xtpl->parse('main.queue.reason');
        }
    }

    if (!empty($row['queue_logs'])) {
        foreach ($row['queue_logs'] as $queue_logs) {
            $queue_logs['type'] = $lang_module['queue_type_' . $queue_logs['queue']];
            $queue_logs['addtime'] = nv_date('H:i d/m/Y', $queue_logs['addtime']);
            $queue_logs['reasonid'] = !empty($queue_logs['reasonid']) ? $array_reason[$queue_logs['reasonid']]['title'] : '-';
            $xtpl->assign('QUEUE_LOGS', $queue_logs);
            $xtpl->parse('main.queue.queue_logs.loop');
        }
        $xtpl->parse('main.queue.queue_logs');
    }

    $xtpl->parse('main.queue');
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
                $xtpl->parse('main.images.is_main');
            }

            $xtpl->parse('main.images');
        }
    }
}
$xtpl->assign('COUNT', count($row['images']));

if (empty($row['id'])) {
    $xtpl->parse('main.auto_get_alias');
    if (!empty($array_config['similar_content'])) {
        $xtpl->parse('main.check_similar_content');
    }
} else {
    $xtpl->parse('main.disabled_code');

    $template = NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file;
    $xtpl->assign('PRICETYPE', nv_load_pricetype($row, $template));
}

if (!$array_config['allow_auto_code']) {
    $xtpl->parse('main.required_code');
    $xtpl->parse('main.required_code1');
}

if (sizeof($array_block_cat_module)) {
    foreach ($array_block_cat_module as $bid_i => $block_cat) {
        $xtpl->assign('BLOCKS', array(
            'title' => $block_cat['title'],
            'bid' => $bid_i,
            'checked' => in_array($bid_i, $id_block_content) ? 'checked="checked"' : ''
        ));
        $xtpl->parse('main.block_cat.loop');
    }
    $xtpl->parse('main.block_cat');
}

if (!empty($row['keywords'])) {
    $keywords_array = explode(',', $row['keywords']);
    foreach ($keywords_array as $keywords) {
        $xtpl->assign('KEYWORDS', $keywords);
        $xtpl->parse('main.keywords');
    }
}

if ($row['userid'] > 0) {
    $user = $db->query('SELECT first_name, last_name, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $row['userid'])->fetch();
    if ($user) {
        $xtpl->assign('USERNAME', nv_show_name_user($user['first_name'], $user['last_name'], $user['username']));
        $xtpl->parse('main.username');
    }
}

if ($array_config['remove_link']) {
    $xtpl->parse('main.remove_link');
}

if ($row['catid'] and !empty($array_market_cat[$row['catid']]['form'])) {
    $form = $array_market_cat[$row['catid']]['form'];
    if (nv_is_file(NV_BASE_SITEURL . NV_ASSETS_DIR . '/' . $module_name . '/files_tpl/cat_form_' . $form . '.tpl', NV_ASSETS_DIR . '/' . $module_name)) {
        $xtpl->assign('DATACUSTOM_FORM', nv_show_custom_form($row['id'], $form, $row['custom_field']));
    }
}

if (!empty($array_config['maps_appid'])) {
    if ($row['display_maps']) {
        $xtpl->assign('MAPS', nv_market_initializeMap($row['maps']));
        $xtpl->parse('main.maps');
    }
} else {
    $xtpl->parse('main.required_maps_appid');
}

if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['content_add'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
