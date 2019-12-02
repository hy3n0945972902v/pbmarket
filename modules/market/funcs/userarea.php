<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (! defined('NV_IS_MOD_MARKET'))
    die('Stop!!!');

if (! defined('NV_IS_USER')) {
    $url_redirect = $client_info['selfurl'];
    $url_back = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt($url_redirect);
    $contents = nv_theme_alert($lang_module['is_user_title'], $lang_module['is_user_content'], 'info', $url_back, $lang_module['login']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
} elseif (! nv_user_in_groups($array_config['grouppost'])) {
    $redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true) . '" />';
    nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect);
}

$premission = nv_group_premission();
if (empty($premission)) {
    $contents = nv_theme_alert($lang_module['is_user_title'], $lang_module['userarea_not_premission_content'], 'info', NV_BASE_SITEURL, $lang_module['backhome']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

// change status
if ($nv_Request->isset_request('change_status', 'post, get')) {
    $id = $nv_Request->get_int('id', 'post, get', 0);
    $checkss = $nv_Request->get_title('checkss', 'post, get', '');
    $content = 'NO_' . $id;

    if ($checkss == md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . $id)) {
        $query = 'SELECT status FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $id;
        $row = $db->query($query)->fetch();
        if (isset($row['status'])) {
            $status = ($row['status']) ? 0 : 1;
            $query = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET status=' . intval($status) . ' WHERE id=' . $id;
            $db->query($query);
            $content = 'OK_' . $id;
            $nv_Cache->delMod($module_name);
        }
    }

    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
    exit();
}

if ($nv_Request->isset_request('delete_id', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $id = $nv_Request->get_int('delete_id', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($id > 0 and $delete_checkss == md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . $id)) {
        nv_delete_rows($id, $user_info['userid']);
        $nv_Cache->delMod($module_name);
        Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
} elseif ($nv_Request->isset_request('delete_list', 'post')) {
    $listall = $nv_Request->get_title('listall', 'post', '');
    $array_id = explode(',', $listall);

    if (! empty($array_id)) {
        foreach ($array_id as $id) {
            nv_delete_rows($id, $user_info['userid']);
        }
        $nv_Cache->delMod($module_name);
        die('OK');
    }
    die('NO');
}

$row = array();
$error = array();
$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
$per_page = 20;
$page = $nv_Request->get_int('page', 'post,get', 1);
$where = '';

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
    ->where('userid=' . $user_info['userid'] . $where);

$sth = $db->prepare($db->sql());

$sth->execute();
$num_items = $sth->fetchColumn();

$db->select('*')
    ->order('ordertime DESC')
    ->limit($per_page)
    ->offset(($page - 1) * $per_page);
$sth = $db->prepare($db->sql());

$sth->execute();

$array_groups = array();
$result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block_cat WHERE useradd=1 ORDER BY weight ASC');
while ($_row = $result->fetch()) {
    $array_groups[$_row['bid']] = $_row;
}

if (! empty($premission['maxpost']) and $num_items >= $premission['maxpost']) {
    $lang_module['maxpostlimit'] = sprintf($lang_module['maxpostlimit'], $premission['maxpost']);
}

$xtpl = new XTemplate('userarea.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('SEARCH', $array_search);
$xtpl->assign('BASE_URL', $base_url);
$xtpl->assign('URL_CONTENT', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['content']);

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
if (! empty($generate_page)) {
    $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}

while ($view = $sth->fetch()) {
    $view['ck_status'] = $view['status'] == 1 ? 'checked="checked"' : '';
    $view['type'] = $array_type[$view['typeid']]['title'];
    $view['cat'] = $array_market_cat[$view['catid']]['title'];
    $view['addtime'] = nv_date('H:i d/m/Y', $view['addtime']);
    $view['checkss'] = md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . $view['id']);
    $view['link_edit'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['content'] . '&amp;id=' . $view['id'] . '&amp;redirect=' . nv_redirect_encrypt($client_info['selfurl']);
    $view['link_delete'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_id=' . $view['id'] . '&amp;delete_checkss=' . $view['checkss'];
    $view['link_view'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_market_cat[$view['catid']]['alias'] . '/' . $view['alias'] . '-' . $view['id'] . $global_config['rewrite_exturl'];

    if ($view['is_queue'] == 2) {
        $queue_info = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_queue_logs WHERE rowsid=' . $view['id'] . ' ORDER BY id DESC LIMIT 1')->fetch();
        if ($queue_info) {
            $user = $db->query('SELECT userid, first_name, last_name, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $queue_info['userid'])->fetch();
            $user['name'] = nv_show_name_user($user['first_name'], $user['last_name'], $user['username']);
            $queue_info['time'] = nv_date('H:i d/m/Y', $queue_info['addtime']);
            if (! empty($queue_info['reason'])) {
                $view['queue_info'] = sprintf($lang_module['queue_decline_info_reason'], $user['name'], $queue_info['time'], $queue_info['reason']);
            } else {
                $view['queue_info'] = sprintf($lang_module['queue_decline_info'], $user['name'], $queue_info['time']);
            }
        }
    }

    if (! empty($view['groupid'])) {
        $view['groupid'] = explode(',', $view['groupid']);
        foreach ($view['groupid'] as $groupid) {
            if (isset($array_groups[$groupid])) {
                $exptime = $db->query('SELECT exptime FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block WHERE bid=' . $groupid . ' AND id=' . $view['id'])->fetchColumn();
                $view['group_info'][$groupid] = array(
                    'title' => $array_groups[$groupid]['title'],
                    'color' => $array_groups[$groupid]['color'],
                    'exptime' => (! empty($exptime) and $exptime >= NV_CURRENTTIME) ? sprintf($lang_module['exptime_info'], nv_date('H:i d/m/Y', $exptime)) : ''
                );
            }
        }
    }

    $xtpl->assign('VIEW', $view);

    if (isset($view['group_info'])) {
        foreach ($view['group_info'] as $group_info) {
            $xtpl->assign('GROUP_INFO', $group_info);
            if (! empty($group_info['exptime'])) {
                $xtpl->parse('main.loop.group_info.exptime');
            }
            $xtpl->parse('main.loop.group_info');
        }
    }

    if ($view['is_queue'] == 1) {
        $xtpl->parse('main.loop.queue');
        $xtpl->parse('main.loop.queue_title');
    } elseif ($view['is_queue'] == 2) {
        if ($view['queue_info']) {
            $xtpl->parse('main.loop.queue_decline.queue_info');
        }
        $xtpl->parse('main.loop.queue_decline');
        $xtpl->parse('main.loop.re_queue');
    } elseif ($view['status_admin']) {
        $xtpl->parse('main.loop.checkbox');
        $xtpl->parse('main.loop.queued_title');
    } else {
        $xtpl->parse('main.loop.label');
    }

    if ($view['status'] == 1 and $view['status_admin'] == 1 and $view['is_queue'] == 0) {
        if ($array_config['refresh_allow']) {
            $count_refresh = nv_count_refresh($module_name);
            $count_refresh_free = nv_count_refresh_free($module_name);
            if ($count_refresh + $count_refresh_free > 0) {
                $xtpl->parse('main.loop.refresh_allow.refresh');
            } else {
                $xtpl->parse('main.loop.refresh_allow.refresh_label');
            }
            $xtpl->parse('main.loop.refresh_allow');
        }

        if (! empty($array_groups) and $array_config['payport'] > 0) {
            foreach ($array_groups as $group) {
                $xtpl->assign('GROUP', $group);
                $xtpl->parse('main.loop.group_buy');
            }
        }
    }

    $xtpl->parse('main.loop');
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
        $xtpl->parse('main.cat');
    }
}

if (! empty($array_type)) {
    foreach ($array_type as $type) {
        $type['selected'] = $type['id'] == $array_search['typeid'] ? 'selected="selected"' : '';
        $xtpl->assign('TYPE', $type);
        $xtpl->parse('main.type');
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
    $xtpl->parse('main.status');
}

if (empty($premission['maxpost']) or $num_items < $premission['maxpost']) {
    $xtpl->parse('main.userpost');
} else {
    $xtpl->parse('main.maxpostlimit');
}

$array_action = array(
    'delete_list_id' => $lang_global['delete']
);
foreach ($array_action as $key => $value) {
    $xtpl->assign('ACTION', array(
        'key' => $key,
        'value' => $value
    ));
    $xtpl->parse('main.action');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['manage'];

$array_mod_title[] = array(
    'title' => $page_title,
    'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op
);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';