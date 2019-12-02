<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2015 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 02 Jun 2015 07:53:31 GMT
 */
if (!defined('NV_IS_FILE_ADMIN')) die('Stop!!!');

if ($nv_Request->isset_request('facebook', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);

    $rows = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $id)->fetch();
    if ($rows) {
        $link = NV_MY_DOMAIN . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_market_cat[$rows['catid']]['alias'] . '/' . $rows['alias'] . '-' . $rows['id'] . $global_config['rewrite_exturl'], true);
        $message = strip_tags(nv_unhtmlspecialchars(html_entity_decode($rows['content'])), 'br');

        if (nv_post_facebook($link, $message)) {
            $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET count_fb_post = count_fb_post + 1 WHERE id=' . $id);

            // Neu post thu cong thi xoa trong bang fb_queue di
            $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_fb_queue WHERE rowsid=' . $id);

            die('OK_' . $lang_module['post_facebook_success']);
        } else {
            die('NO_' . $lang_module['post_facebook_error']);
        }
    } else {
        die('NO_' . $lang_module['post_facebook_error']);
    }
}

if ($nv_Request->isset_request('freelance_set_fees', 'post')) {
    $userid = $nv_Request->get_int('userid', 'post', 0);
    $fees = $nv_Request->get_title('fees', 'post', 0);

    if (empty($userid)) {
        die('NO');
    }

    $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_freelance SET salary = ' . $fees . ' WHERE userid=' . $userid);
    $nv_Cache->delMod($module_name);
    die('OK');
}

if ($nv_Request->isset_request('get_user_json', 'post, get')) {
    $q = $nv_Request->get_title('q', 'post, get', '');

    $db->sqlreset()
        ->select('userid, username, email, first_name, last_name')
        ->from(NV_USERS_GLOBALTABLE)
        ->where('( username LIKE :username OR email LIKE :email OR first_name like :first_name OR last_name like :last_name )')
        ->order('username ASC')
        ->limit(20);

    $sth = $db->prepare($db->sql());
    $sth->bindValue(':username', '%' . $q . '%', PDO::PARAM_STR);
    $sth->bindValue(':email', '%' . $q . '%', PDO::PARAM_STR);
    $sth->bindValue(':first_name', '%' . $q . '%', PDO::PARAM_STR);
    $sth->bindValue(':last_name', '%' . $q . '%', PDO::PARAM_STR);
    $sth->execute();

    $array_data = array();
    while (list ($userid, $username, $email, $first_name, $last_name) = $sth->fetch(3)) {
        $array_data[] = array(
            'id' => $userid,
            'username' => $username,
            'fullname' => nv_show_name_user($first_name, $last_name)
        );
    }

    header('Cache-Control: no-cache, must-revalidate');
    header('Content-type: application/json');

    ob_start('ob_gzhandler');
    echo json_encode($array_data);
    exit();
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
    $template = NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file;
    die(nv_load_pricetype($row, $template));
}

if ($nv_Request->isset_request('get_alias_title', 'post')) {
    $alias = $nv_Request->get_title('get_alias_title', 'post', '');
    $alias = change_alias($alias);
    if ($array_config['tags_alias_lower']) {
        $alias = strtolower($alias);
    }
    die($alias);
}