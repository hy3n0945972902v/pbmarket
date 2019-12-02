<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2016 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 21 Nov 2016 01:25:39 GMT
 */
if (! defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

if (! $array_config['auction']) {
    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
    die();
}

// change status
if ($nv_Request->isset_request('change_status', 'post, get')) {
    $id = $nv_Request->get_int('id', 'post, get', 0);
    $content = 'NO_' . $id;

    $query = 'SELECT status FROM ' . NV_PREFIXLANG . '_' . $module_data . '_auction_register WHERE id=' . $id;
    $row = $db->query($query)->fetch();
    if (isset($row['status'])) {
        $status = ($row['status']) ? 0 : 1;
        $query = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_auction_register SET status=' . intval($status) . ' WHERE id=' . $id;
        $db->query($query);
        $content = 'OK_' . $id;
    }
    $nv_Cache->delMod($module_name);
    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
    exit();
}

$row = array();
$error = array();
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
$per_page = 20;
$page = $nv_Request->get_int('page', 'post,get', 1);
$id = $nv_Request->get_int('id', 'get', 0);
$where = '';
$is_result = 0;

if ($id) {
    $rows = $db->query('SELECT title, auction_begin, auction_end FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $id)->fetch();
    if (! $rows) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=auction');
        die();
    }

    $base_url += '&id=' . $id;
    $page_title = sprintf($lang_module['auction_register_list'], $rows['title']);

    $array_field_config = array();
    $result_field = $db->query('SELECT * FROM ' . NV_USERS_GLOBALTABLE . '_field WHERE user_editable = 1 ORDER BY weight ASC');
    while ($row_field = $result_field->fetch()) {
        $language = unserialize($row_field['language']);
        $row_field['title'] = (isset($language[NV_LANG_DATA])) ? $language[NV_LANG_DATA][0] : '';
        $row_field['description'] = (isset($language[NV_LANG_DATA])) ? nv_htmlspecialchars($language[NV_LANG_DATA][1]) : '';

        if (! empty($row_field['field_choices'])) {
            $row_field['field_choices'] = unserialize($row_field['field_choices']);
        } elseif (! empty($row_field['sql_choices'])) {
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

    $number = 1;
    $result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_auction_register WHERE rowsid=' . $id);
    while ($_row = $result->fetch()) {
        $_row['number'] = $number ++;

        $userinfo = $db->query('SELECT username, first_name, last_name, email FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $_row['userid'])->fetch();
        if ($userinfo) {
            $userinfo['fullname'] = nv_show_name_user($userinfo['first_name'], $userinfo['last_name'], $userinfo['username']);

            $_result = $db->query('SELECT * FROM ' . NV_USERS_GLOBALTABLE . '_info WHERE userid=' . $_row['userid']);
            $custom_fields = $_result->fetch();

            if (! empty($array_field_config)) {
                foreach ($array_field_config as $config) {
                    $question_type = $config['field_type'];
                    if ($question_type == 'checkbox') {
                        $_result = explode(',', $custom_fields[$config['field']]);
                        $value = '';
                        foreach ($_result as $item) {
                            $value .= $config['field_choices'][$item] . '<br />';
                        }
                    } elseif ($question_type == 'multiselect' or $question_type == 'select' or $question_type == 'radio') {
                        $value = isset($config['field_choices'][$custom_fields[$config['field']]]) ? $config['field_choices'][$custom_fields[$config['field']]] : '';
                    } else {
                        $value = $custom_fields[$config['field']];
                    }
                    $userinfo[$config['field']] = $value;
                }
            }

            $_row += $userinfo;
        }

        $_row['auction_register_time'] = nv_date('H:i d/m/Y', $_row['addtime']);
        $row[$_row['userid']] = $_row;
    }

    $max = $db->query('SELECT MAX(price) price, userid, addtime FROM ' . NV_PREFIXLANG . '_' . $module_data . '_auction WHERE rowsid=' . $id)->fetch();
    if ($max and $max['price'] != null) {
        $is_result = 1;
        $max['addtime'] = nv_date('H:i d/m/Y', $max['addtime']);
        $lang_module['auction_price_max'] = sprintf($lang_module['auction_price_max'], $max['price'], $row[$max['userid']]['fullname'], $max['addtime']);
    }
} else {
    $page_title = $lang_module['auction_list'];

    $array_search = array(
        'q' => $nv_Request->get_title('q', 'get'),
        'catid' => $nv_Request->get_int('catid', 'get', 0),
        'typeid' => $nv_Request->get_int('typeid', 'get', - 1),
        'status' => $nv_Request->get_int('status', 'get', - 1)
    );

    if (! empty($array_search['q'])) {
        $base_url .= '&q=' . $array_search['q'];
        $where .= ' AND (title LIKE "%' . $array_search['q'] . '%" OR
        code LIKE "%' . $array_search['q'] . '%" OR
        alias LIKE "%' . $array_search['q'] . '%" OR
        content LIKE "%' . $array_search['q'] . '%" OR
        note LIKE "%' . $array_search['q'] . '%" OR
        contact_fullname LIKE "%' . $array_search['q'] . '%" OR
        contact_email LIKE "%' . $array_search['q'] . '%" OR
        contact_phone LIKE "%' . $array_search['q'] . '%" OR
        contact_address LIKE "%' . $array_search['q'] . '%"
    )';
    }

    if (! empty($array_search['catid'])) {
        $base_url .= '&catid=' . $array_search['catid'];
        $where .= ' AND catid IN (' . implode(',', nv_GetCatidInParent($array_search['catid'])) . ')';
    }

    if ($array_search['typeid'] >= 0) {
        $base_url .= '&typeid=' . $array_search['typeid'];
        $where .= ' AND typeid=' . $array_search['typeid'];
    }

    if ($array_search['status'] >= 0) {
        $base_url .= '&status=' . $array_search['status'];
        $where .= ' AND status=' . $array_search['status'];
    }

    $db->sqlreset()
        ->select('COUNT(*)')
        ->from('' . NV_PREFIXLANG . '_' . $module_data . '_rows')
        ->where('auction=1 AND auction_begin > 0 AND auction_end > 0 AND auction_price_begin > 0 AND auction_price_step > 0' . $where);

    $sth = $db->prepare($db->sql());

    $sth->execute();
    $num_items = $sth->fetchColumn();

    $db->select('*')
        ->order('id DESC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $sth = $db->prepare($db->sql());

    $sth->execute();
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('OP', $op);

if ($id) {
    if (! empty($row)) {
        foreach ($row as $_row) {
            $_row['checked'] = $_row['status'] ? 'checked="checked"' : '';

            $xtpl->assign('ROW', $_row);

            if (nv_auction_status($rows['auction_begin'], $rows['auction_end']) != 0) {
                $xtpl->parse('main.view.loop.stauts_disabled');
            }

            $xtpl->parse('main.view.loop');
        }
    }

    if ($is_result) {
        $xtpl->parse('main.view.result');
    }

    $xtpl->parse('main.view');
} else {
    $xtpl->assign('ROW', $row);
    $xtpl->assign('SEARCH', $array_search);
    $xtpl->assign('BASE_URL', $base_url);
    $xtpl->assign('MONEY_UNIT', $array_config['money_unit']);

    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    if (! empty($generate_page)) {
        $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.list.generate_page');
    }

    while ($view = $sth->fetch()) {

        $view['status'] = nv_auction_status($view['auction_begin'], $view['auction_end']);
        $view['status'] = $lang_module['auction_status_' . $view['status']];

        $view['cat'] = $array_market_cat[$view['catid']]['title'];
        $view['addtime'] = nv_date('H:i d/m/Y', $view['addtime']);
        $view['auction_begin'] = nv_date('H:i d/m/Y', $view['auction_begin']);
        $view['auction_end'] = nv_date('H:i d/m/Y', $view['auction_end']);
        $view['auction_price_begin'] = nv_market_number_format($view['auction_price_begin']);
        $view['auction_price_step'] = nv_market_number_format($view['auction_price_step']);

        $view['adduser'] = $lang_global['guests'];
        $userinfo = $db->query('SELECT first_name, last_name, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $view['userid'])->fetch();
        if ($userinfo) {
            $view['adduser'] = nv_show_name_user($userinfo['first_name'], $userinfo['last_name'], $userinfo['username']);
        }

        $view['link_view'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=auction&amp;id=' . $view['id'];
        $xtpl->assign('VIEW', $view);

        $xtpl->parse('main.list.loop');
    }

    if (! empty($array_market_cat)) {
        foreach ($array_market_cat as $catid => $value) {
            $value['space'] = '';
            if ($value['lev'] > 0) {
                for ($i = 1; $i <= $value['lev']; $i ++) {
                    $value['space'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }
            }
            $value['selected'] = $catid == $array_search['catid'] ? ' selected="selected"' : '';

            $xtpl->assign('CAT', $value);
            $xtpl->parse('main.list.cat');
        }
    }

    if (! empty($array_type)) {
        foreach ($array_type as $type) {
            $type['selected'] = $type['id'] == $array_search['typeid'] ? 'selected="selected"' : '';
            $xtpl->assign('TYPE', $type);
            $xtpl->parse('main.list.type');
        }
    }

    $array_status = array(
        1 => $lang_module['status_1'],
        0 => $lang_module['status_0'],
        2 => $lang_module['status_2']
    );
    foreach ($array_status as $index => $value) {
        $sl = $array_search['status'] == $index ? 'selected="selected"' : '';
        $xtpl->assign('STATUS', array(
            'index' => $index,
            'value' => $value,
            'selected' => $sl
        ));
        $xtpl->parse('main.list.status');
    }

    $array_action = array(
        'delete_list_id' => $lang_global['delete']
    );
    foreach ($array_action as $key => $value) {
        $xtpl->assign('ACTION', array(
            'key' => $key,
            'value' => $value
        ));
        $xtpl->parse('main.list.action');
    }

    $xtpl->parse('main.list');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';