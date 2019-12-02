<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (!defined('NV_IS_MOD_MARKET')) die('Stop!!!');

if (!defined('NV_IS_AJAX')) die('Wrong URL');

if ($nv_Request->isset_request('save', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $mod = $nv_Request->get_title('mod', 'post', '');

    if (empty($id) or !defined('NV_IS_USER')) {
        die('NO_' . $lang_module['error_save_rows']);
    }

    $count = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $id)->fetchColumn();
    if ($count) {
        try {
            if ($mod == 'add') {
                $db->query('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_saved (userid, rowsid) VALUES(' . $user_info['userid'] . ', ' . $id . ')');
                die('OK_' . $lang_module['save_success']);
            } elseif ($mod == 'remove') {
                $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_saved WHERE userid=' . $user_info['userid'] . ' AND rowsid=' . $id);
                die('OK_' . $lang_module['save_remove_success']);
            }
        } catch (Exception $e) {
            //
        }
    }
    die('NO_' . $lang_module['error_save_rows']);
}

if ($nv_Request->isset_request('saved_delete', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $checkss = $nv_Request->get_title('checkss', 'post', '');

    if (!empty($id) and $checkss == md5($global_config['sitekey'] . '-' . $id)) {
        nv_delete_saved($id);
        die('OK');
    } else {
        die('NO_' . $lang_module['saved_delete_error']);
    }
} elseif ($nv_Request->isset_request('saved_delete_list', 'post')) {
    $listall = $nv_Request->get_title('listall', 'post', '');
    $array_id = explode(',', $listall);
    $checkss = $nv_Request->get_title('checkss', 'post', '');

    if (!empty($array_id) and $checkss == md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . NV_CACHE_PREFIX)) {
        foreach ($array_id as $id) {
            nv_delete_saved($id);
        }
        die('OK');
    }
    die('NO');
}

if ($nv_Request->isset_request('auction_register', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $mod = $nv_Request->get_title('mod', 'post', 'register');

    if (empty($id) or !defined('NV_IS_USER') or empty($mod)) {
        die('NO_' . $lang_module['error_auction_register_error']);
    }

    try {
        if ($mod == 'register') {
            $result = $db->query('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_auction_register (userid, rowsid, addtime) VALUES(' . $user_info['userid'] . ', ' . $id . ', ' . NV_CURRENTTIME . ')');
            if ($result) {
                nv_insert_notification($module_name, 'auction_register', '', $id, 0, $user_info['userid']);
                die('OK');
            }
        } elseif ($mod == 'cancel') {
            $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_auction_register WHERE userid=' . $user_info['userid'] . ' AND rowsid=' . $id);
        }
    } catch (Exception $e) {
        //
    }

    die('NO_' . $lang_module['error_auction_register_error']);
}

if ($nv_Request->isset_request('auction_send', 'post')) {
    $id = $nv_Request->get_int('rowsid', 'post', 0);
    $_price = $nv_Request->get_title('price', 'post', '');
    $price = preg_replace('/[^0-9]/', '', $_price);

    if (empty($id) or !defined('NV_IS_USER')) {
        die(json_encode(array(
            'status' => 'error',
            'message' => $lang_module['error_auction_send_value']
        )));
    } elseif (empty($price)) {
        die(json_encode(array(
            'status' => 'error',
            'message' => $lang_module['error_auction_empty_value']
        )));
    }

    $count = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_auction_register WHERE userid=' . $user_info['userid'] . ' AND rowsid=' . $id . ' AND status=1')->fetchColumn();
    if ($count == 0) {
        die(json_encode(array(
            'status' => 'error',
            'message' => $lang_module['error_auction_userid']
        )));
    }

    $rows = $db->query('SELECT auction_begin, auction_end, auction_price_begin, auction_price_step FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $id)->fetch();
    if (!$rows) {
        die(json_encode(array(
            'status' => 'error',
            'message' => $lang_module['error_auction_send_value']
        )));
    }

    if (nv_auction_status($rows['auction_begin'], $rows['auction_end']) == 0) {
        die(json_encode(array(
            'status' => 'error',
            'message' => $lang_module['error_auction_begintime']
        )));
    } elseif (nv_auction_status($rows['auction_begin'], $rows['auction_end']) == 2) {
        die(json_encode(array(
            'status' => 'error',
            'message' => $lang_module['error_auction_endtime']
        )));
    }

    if ($price < $rows['auction_price_begin']) {
        die(json_encode(array(
            'status' => 'error',
            'message' => $lang_module['error_auction_price_begin']
        )));
    } elseif ($price > $rows['auction_price_begin'] and $price < ($rows['auction_price_begin'] + $rows['auction_price_step'])) {
        die(json_encode(array(
            'status' => 'error',
            'message' => sprintf($lang_module['error_auction_price_begin_s'], $rows['auction_price_step'], $array_config['money_unit'])
        )));
    }

    $max = $db->query('SELECT MAX(price) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_auction WHERE userid=' . $user_info['userid'] . ' AND rowsid=' . $id)->fetchColumn();
    if ($price < ($max + $rows['auction_price_step'])) {
        die(json_encode(array(
            'status' => 'error',
            'message' => sprintf($lang_module['error_auction_max'], $rows['auction_price_step'], $array_config['money_unit'])
        )));
    }

    try {
        $stmt = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_auction (userid, rowsid, price, addtime) VALUES(:userid, :rowsid, :price, ' . NV_CURRENTTIME . ')');
        $stmt->bindParam(':userid', $user_info['userid'], PDO::PARAM_INT);
        $stmt->bindParam(':rowsid', $id, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        if ($stmt->execute()) {
            die(json_encode(array(
                'status' => 'success',
                'userid' => $user_info['userid'],
                'name' => nv_show_name_user($user_info['first_name'], $user_info['last_name'], $user_info['username']),
                'price' => $_price,
                'addtime' => nv_date('H:i:s d/m/Y', NV_CURRENTTIME)
            )));
        }
    } catch (Exception $e) {
        // die($e->getMessage());
    }

    die(json_encode(array(
        'status' => 'error',
        'message' => $lang_module['error_auction_send_value']
    )));
}

if ($nv_Request->isset_request('refresh', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $checkss = $nv_Request->get_title('checkss', 'post', '');

    if (empty($id) or empty($checkss) or !defined('NV_IS_USER')) {
        die('NO_' . $lang_module['error_unknow']);
    }

    list ($refresh_time, $title, $code) = $db->query('SELECT refresh_time, title, code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $id)->fetch(3);
    $refresh_timelimit_sec = $array_config['refresh_timelimit'] * 60;
    if (((NV_CURRENTTIME - $refresh_time)) <= $refresh_timelimit_sec) {
        $count = nv_convertfromSec($refresh_timelimit_sec - NV_CURRENTTIME + $refresh_time);
        die('NO_' . sprintf($lang_module['refresh_timelimit'], $count));
    }

    if ($checkss == md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . $id) and $array_config['refresh_allow']) {
        $count_refresh = nv_count_refresh($module_name);
        $count_refresh_free = nv_count_refresh_free($module_name);

        if ($count_refresh_free > 0) {
            $currentdate = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
            try {
                $db->query('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_refresh(userid, count, free, free_time) VALUES(' . $user_info['userid'] . ', ' . $count_refresh . ', ' . ($array_config['refresh_free'] - 1) . ', ' . $currentdate . ')');
            } catch (Exception $e) {
                $refresh = $db->query('SELECT free, free_time FROM ' . NV_PREFIXLANG . '_' . $module_data . '_refresh WHERE userid=' . $user_info['userid'])->fetch();
                if ($refresh['free_time'] == $currentdate) {
                    $free = $refresh['free'] - 1;
                } else {
                    $free = $array_config['refresh_free'] - 1;
                }
                $db->exec('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_refresh SET free=' . $free . ', free_time=' . $currentdate . ' WHERE userid=' . $user_info['userid']);
            }

            // Cập nhật thời gian sắp xếp
            $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET ordertime=' . NV_CURRENTTIME . ', refresh_time = ' . NV_CURRENTTIME . ' WHERE id=' . $id);

            // Đăng lại facebook
            nv_add_fb_queue($id);

            // ghi lịch sử
            nv_user_logs('[' . $user_info['username'] . '] ' . $lang_module['refresh'] . ' [' . $code . '] ' . $title);

            die('OK');
        } elseif ($count_refresh > 0) {
            try {
                $db->query('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_refresh(userid, count) VALUES(' . $user_info['userid'] . ', ' . ($count_refresh - 1) . ')');
            } catch (Exception $e) {
                $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_refresh SET count=count-1 WHERE userid=' . $user_info['userid']);
            }

            // Cập nhật thời gian sắp xếp
            $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET ordertime=' . NV_CURRENTTIME . ', refresh_time = ' . NV_CURRENTTIME . ' WHERE id=' . $id);

            // Đăng lại facebook
            nv_add_fb_queue($id);

            // ghi lịch sử
            nv_user_logs('[' . $user_info['username'] . '] ' . $lang_module['refresh'] . ' [' . $code . '] ' . $title);

            die('OK');
        } else {
            die('NO_' . $lang_module['refresh_error']);
        }
    }

    die('NO_' . $lang_module['error_unknow']);
}

if ($nv_Request->isset_request('buy_refresh', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $number = $nv_Request->get_int('number', 'post', 0);
    $checksum = $nv_Request->get_title('checksum', 'post', '');

    if (!defined('NV_IS_USER') or empty($checksum) or empty($number)) {
        die('NO_' . $lang_module['error_unknow']);
    }

    if ($checksum == md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . $id . '-' . $number)) {
        $count = nv_count_refresh($module_name);
        try {
            // thêm thành viên vào bảng _refresh, nếu có rồi thì cập nhật
            $db->query('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_refresh(userid, count) VALUES(' . $user_info['userid'] . ', ' . $number . ')');
        } catch (Exception $e) {
            $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_refresh SET count=count + ' . $number . ' WHERE userid=' . $user_info['userid']);
        }
        $nv_Cache->delMod($module_name);
        die('OK_' . sprintf($lang_module['refresh_success'], intval($count) + $number));
    }
    die('NO_' . $lang_module['error_unknow']);
}

if ($nv_Request->isset_request('buy_group', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $time = $nv_Request->get_int('time', 'post', 0);
    $groupid = $nv_Request->get_int('groupid', 'post', 0);
    $checksum = $nv_Request->get_title('checksum', 'post', '');

    if (empty($id) or empty($checksum) or empty($groupid) or empty($time)) {
        die('NO_' . $lang_module['error_unknow']);
    }

    if ($checksum == md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . $id . '-' . $time)) {
        // taikhoan
        require (NV_ROOTDIR . "/modules/taikhoan/check.transaction.class.php");
        $module_send = $module_name;
        $postid = intval($id);
        $userid = $user_info['userid'];

        $tk_check = new TK_check_transaction($module_send, $postid, $userid);
        if (!$tk_check->check_tracsaction()) {
            die($lang_module['payment_error']);
        }

        try {
            $exptime = NV_CURRENTTIME + ($time * 86400);
            if (nv_rows_update_group($groupid, $id, 0, $exptime)) {
                die('OK');
            }
        } catch (Exception $e) {
            $exptime = $db->query('SELECT exptime FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block WHERE bid=' . $groupid)->fetchColumn();
            if ($exptime == 0 or $exptime < NV_CURRENTTIME) {
                $exptime = NV_CURRENTTIME + ($time * 86400);
            } else {
                $exptime = $exptime + ($time * 86400);
            }
            $exptime = $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_block SET exptime=' . $exptime . ' WHERE bid=' . $groupid);
            die('OK');
        }
    }

    die('NO_' . $lang_module['error_unknow']);
}

if ($nv_Request->isset_request('re_queue', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);

    if (empty($id)) {
        die('NO_' . $lang_module['error_unknow']);
    }

    $result = $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET is_queue=1 WHERE id=' . $id);
    if ($result) {
        die('OK');
    }

    die('NO_' . $lang_module['error_unknow']);
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

if ($nv_Request->isset_request('tags_save', 'post,get')) {
    $array_keywords_old = $row = array();
    $row['keywords_old'] = array();
    $row['id'] = $nv_Request->get_int('id', 'post', 0);

    if (empty($row['id'])) {
        die('NO');
    }

    if ($row['id'] > 0) {

        $_query = $db->query('SELECT tid, keyword FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE id=' . $row['id'] . ' ORDER BY keyword ASC');
        while ($_row = $_query->fetch()) {
            $array_keywords_old[$_row['tid']] = $_row['keyword'];
        }
        $row['keywords_old'] = $array_keywords_old;
    }

    $row['keywords'] = $nv_Request->get_title('keywords', 'post', '');

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
                    $sth = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id (id, tid, keyword) VALUES (' . $row['id'] . ', ' . intval($tid) . ', :keyword)');
                    $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                    $sth->execute();
                } catch (PDOException $e) {
                    $sth = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id SET keyword = :keyword WHERE id = ' . $row['id'] . ' AND tid=' . intval($tid));
                    $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                    $sth->execute();
                }
                unset($array_keywords_old[$tid]);
            }
        }
        foreach ($array_keywords_old as $tid => $keyword) {
            if (!in_array($keyword, $keywords)) {
                $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tags SET numnews = numnews-1 WHERE tid = ' . $tid);
                $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE id = ' . $row['id'] . ' AND tid=' . $tid);
            }
        }
    }
    die('OK');
}

if ($nv_Request->isset_request('check_similar_content', 'post,get')) {
    $content = $nv_Request->get_editor('html', '', NV_ALLOWED_HTML_TAGS);
    if (!nv_check_similar_content($content)) {
        die('NO');
    } else {
        die('OK');
    }
}

if ($nv_Request->isset_request('refresh_popup', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);

    $rows = $db->query('SELECT userid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $id)->fetch();
    $content = $db->query('SELECT econtent FROM ' . NV_PREFIXLANG . '_' . $module_data . '_econtent WHERE action="refresh"')->fetchColumn();

    $refresh_timelimit = '';
    $is_owner = 0;
    if (defined('NV_IS_USER')) {
        if ($rows['userid'] == $user_info['userid']) {
            $is_owner = 1;
        }

        $refresh_time = $db->query('SELECT refresh_time FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $id)->fetchColumn();
        $refresh_timelimit_sec = $array_config['refresh_timelimit'] * 60;
        if (((NV_CURRENTTIME - $refresh_time)) <= $refresh_timelimit_sec) {
            $count = nv_convertfromSec($refresh_timelimit_sec - NV_CURRENTTIME + $refresh_time);
            $refresh_timelimit = sprintf($lang_module['refresh_timelimit'], $count);
        }
    }

    die(nv_theme_market_refresh($id, $content, $is_owner, $refresh_timelimit));
}

if ($nv_Request->isset_request('load_pricetype', 'post')) {
    $row = array();
    $row['catid'] = $nv_Request->get_int('catid', 'post', 0);
    $row['pricetype'] = $nv_Request->get_int('pricetype', 'post', 0);
    $row['price'] = $nv_Request->get_title('price', 'post', '');
    $row['price'] = preg_replace('/[^0-9]/', '', $row['price']);
    $row['price1'] = $nv_Request->get_title('price1', 'post', '');
    $row['price1'] = preg_replace('/[^0-9]/', '', $row['price1']);
    $row['unitid'] = $nv_Request->get_title('unitid', 'post', 0);
    $template = NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file;
    die(nv_load_pricetype($row, $template));
}

if ($nv_Request->isset_request('show_terms', 'post')) {
    $ispopp = $nv_Request->get_int('ispopup', 'post', 0);

    $sql = 'SELECT econtent FROM ' . NV_PREFIXLANG . '_' . $module_data . '_econtent WHERE action="terms"';
    $html = $nv_Cache->db($sql, '', $module_name);
    $html = $html[0]['econtent'];

    if ($ispopp) {
        $html .= '<p style="text-align: center"><br /><a style="font-size: 18px; font-weight: bold; text-transform: uppercase; color: red" href="javascript:void(0);" onclick="nv_popup_content(\'' . $module_info['alias']['content'] . '\');">' . $lang_module['content_continue'] . '</a></p>';
    }

    die($html);
}

if ($nv_Request->isset_request('get_custom_field', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $catid = $nv_Request->get_int('catid', 'post', 0);
    $cat_form = $array_market_cat[$catid]['form'];

    if (!empty($cat_form)) {
        $custom = array();
        $idtemplate = $db->query('SELECT id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_template where alias = "' . preg_replace("/[\_]/", "-", $cat_form) . '"')->fetchColumn();
        if ($idtemplate) {
            $result = $db->query("SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_info WHERE rowid=" . $id);
            while ($row = $result->fetch()) {
                $custom[$row['rowid']] = $row;
            }
        }

        nv_htmlOutput(nv_show_custom_form($id, $cat_form, $custom));
    }
    die();
}

if ($nv_Request->isset_request('load_maps', 'post')) {
    $html = nv_market_initializeMap();
    nv_htmlOutput($html);
}
