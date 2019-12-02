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

// change status
if ($nv_Request->isset_request('change_status', 'post, get')) {
    $id = $nv_Request->get_int('id', 'post, get', 0);
    $content = 'NO_' . $id;

    $query = 'SELECT status_admin FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $id;
    $row = $db->query($query)->fetch();
    if (isset($row['status_admin'])) {
        $status = ($row['status_admin']) ? 0 : 1;
        $query = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET status_admin=' . intval($status) . ' WHERE id=' . $id;
        $db->query($query);
        $content = 'OK_' . $id;
    }
    $nv_Cache->delMod($module_name);
    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
    exit();
}

if ($nv_Request->isset_request('delete_id', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $id = $nv_Request->get_int('delete_id', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($id > 0 and $delete_checkss == md5($id . NV_CACHE_PREFIX . $client_info['session_id'])) {
        nv_delete_rows($id);
        $nv_Cache->delMod($module_name);
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
} elseif ($nv_Request->isset_request('delete_list', 'post')) {
    $listall = $nv_Request->get_title('listall', 'post', '');
    $array_id = explode(',', $listall);

    if (! empty($array_id)) {
        foreach ($array_id as $id) {
            nv_delete_rows($id);
        }
        $nv_Cache->delMod($module_name);
        die('OK');
    }
    die('NO');
}

$row = array();
$error = array();
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
$per_page = 20;
$page = $nv_Request->get_int('page', 'post,get', 1);
$queue = $nv_Request->get_int('queue', 'post,get', 0);
$where = '';
$join = '';

require_once NV_ROOTDIR . '/modules/location/location.class.php';
$location = new Location();

$array_search = array(
    'q' => $nv_Request->get_title('q', 'get'),
    'catid' => $nv_Request->get_int('catid', 'get', 0),
    'from' => $nv_Request->get_title('from', 'get', ''),
    'to' => $nv_Request->get_title('to', 'get', ''),
    'typeid' => $nv_Request->get_int('typeid', 'get', - 1),
    'status' => $nv_Request->get_int('status', 'get', - 1),
    'userid' => $nv_Request->get_int('userid', 'get', 0)
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
    $join = ' INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_detail t2 on t1.id=t2.id';
}

if (! empty($array_search['catid'])) {
    $base_url .= '&catid=' . $array_search['catid'];
    $where .= ' AND catid IN (' . implode(',', nv_GetCatidInParent($array_search['catid'])) . ')';
}

if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $array_search['from'], $m)) {
    $base_url .= '&from=' . $array_search['from'];
    $from = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
    $where .= ' AND addtime >= ' . $from;
}

if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $array_search['to'], $m)) {
    $base_url .= '&to=' . $array_search['to'];
    $to = mktime(23, 59, 59, $m[2], $m[1], $m[3]);
    $where .= ' AND addtime <= ' . $to;
}

if ($array_search['typeid'] >= 0) {
    $base_url .= '&typeid=' . $array_search['typeid'];
    $where .= ' AND typeid=' . $array_search['typeid'];
}

if ($array_search['status'] >= 0) {
    $base_url .= '&status=' . $array_search['status'];
    $where .= ' AND status=' . $array_search['status'];
}

if (! empty($array_search['userid'])) {
    $base_url .= '&userid=' . $array_search['userid'];
    $where .= ' AND userid=' . $array_search['userid'];
}

if ($queue) {
    $base_url .= ' &amp;queue=1';
    $where .= ' AND (is_queue=1 OR is_queue_edit=1)';
} else {
    $where .= ' AND is_queue=0';
}

// nếu chưa autoload thì include thư viện
if (!class_exists('PHPExcel')) {
    if (file_exists(NV_ROOTDIR . '/includes/class/PHPExcel.php')) {
        include_once NV_ROOTDIR . '/includes/class/PHPExcel.php';
    }
}

if ($nv_Request->isset_request('export', 'get')) {
    if (!class_exists('PHPExcel')) {
        if (file_exists(NV_ROOTDIR . '/includes/class/PHPExcel.php')) {
            include_once NV_ROOTDIR . '/includes/class/PHPExcel.php';
        }else{
            die($lang_module['error_required_phpexcel']);
        }
    }

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);

    // Set properties
    $objPHPExcel->getProperties()
        ->setCreator($admin_info['username'])
        ->setCategory($module_name);

    $columnIndex = 1; // Cot bat dau ghi du lieu
    $rowIndex = 3; // Dong bat dau ghi du lieu

    $array_field = array(
        'id' => 'ID',
        'code' => $lang_module['code'],
        'title' => $lang_module['title'],
        'cat' => $lang_module['cat'],
        'location' => $lang_module['location'],
        'type' => $lang_module['type'],
        'content' => $lang_module['content'],
        'price' => $lang_module['price'],
        'countview' => $lang_module['countview'],
        'countcomment' => $lang_module['countcomment'],
        'addtime' => $lang_module['addtime'],
        'exptime' => $lang_module['exptime'],
        'contact_fullname' => $lang_module['contact_fullname'],
        'contact_email' => $lang_module['contact_email'],
        'contact_phone' => $lang_module['contact_phone'],
        'contact_address' => $lang_module['contact_address'],
        'poster' => $lang_module['poster'],
        'status' => $lang_module['status']
    );

    $db->sqlreset()
        ->select('*')
        ->from(NV_PREFIXLANG . '_' . $module_data . '_rows t1')
        ->join($join)
        ->where('1=1' . $where);

    $sth = $db->prepare($db->sql());
    $sth->execute();

    $array_data = array();

    while ($row = $sth->fetch()) {
        $row['type'] = $array_type[$row['typeid']]['title'];
        $row['location'] = $location->locationString($row['area_p'], $row['area_d']);
        $row['cat'] = $array_market_cat[$row['catid']]['title'];
        $row['addtime'] = nv_date('H:i d/m/Y', $row['addtime']);
        $row['status'] = $lang_module['status_' . $row['status']];

        $row['poster'] = $lang_global['guests'];
        $userinfo = $db->query('SELECT first_name, last_name, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $row['userid'])->fetch();
        if ($userinfo) {
            $row['poster'] = nv_show_name_user($userinfo['first_name'], $userinfo['last_name'], $userinfo['username']);
        }
        $array_data[] = $row;
    }

    $col = $columnIndex;
    foreach ($array_field as $title) {
        $objPHPExcel->getActiveSheet()->setCellValue(PHPExcel_Cell::stringFromColumnIndex($col) . $rowIndex, $title);
        $col ++;
    }

    $i = $rowIndex + 1;
    $number = 1;
    foreach ($array_data as $data) {
        $j = $columnIndex;
        foreach ($array_field as $field => $title) {
            $col = PHPExcel_Cell::stringFromColumnIndex($j);
            $CellValue = $data[$field];
            $objPHPExcel->getActiveSheet()->setCellValue($col . $i, $CellValue);
            $j ++;
        }
        $i ++;
    }

    $highestRow = $i - 1;
    $highestColumn = PHPExcel_Cell::stringFromColumnIndex($j - 1);

    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Sheet 1');

    // Set page orientation and size
    $objPHPExcel->getActiveSheet()
        ->getPageSetup()
        ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
    $objPHPExcel->getActiveSheet()
        ->getPageSetup()
        ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

    // Excel title
    $objPHPExcel->getActiveSheet()->mergeCells(PHPExcel_Cell::stringFromColumnIndex($columnIndex) . '2:' . $highestColumn . '2');
    $objPHPExcel->getActiveSheet()->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex) . '2', strtoupper($lang_module['list']));
    $objPHPExcel->getActiveSheet()
        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($columnIndex) . '2')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()
        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($columnIndex) . '2')
        ->getAlignment()
        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    // Set color
    $styleArray = array(
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'argb' => 'FF000000'
                )
            )
        )
    );
    $objPHPExcel->getActiveSheet()
        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($columnIndex) . $rowIndex . ':' . $highestColumn . $highestRow)
        ->applyFromArray($styleArray);

    // Set font size
    $objPHPExcel->getActiveSheet()
        ->getStyle("A1:" . $highestColumn . $highestRow)
        ->getFont()
        ->setSize(13);

    // Set auto column width
    foreach (range(PHPExcel_Cell::stringFromColumnIndex($columnIndex), $highestColumn) as $columnID) {
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension($columnID)
            ->setAutoSize(true);
    }

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $file_src = NV_ROOTDIR . NV_BASE_SITEURL . NV_TEMP_DIR . '/' . change_alias($lang_module['list']) . '.xlsx';
    $objWriter->save($file_src);

    $download = new NukeViet\Files\Download($file_src, NV_ROOTDIR . NV_BASE_SITEURL . NV_TEMP_DIR);
    $download->download_file();
    die();
}

$db->sqlreset()
    ->select('COUNT(*)')
    ->from(NV_PREFIXLANG . '_' . $module_data . '_rows t1')
    ->join($join)
    ->where('1=1' . $where);

$sth = $db->prepare($db->sql());

$sth->execute();
$num_items = $sth->fetchColumn();

$db->select('*')
    ->order('t1.id DESC')
    ->limit($per_page)
    ->offset(($page - 1) * $per_page);
$sth = $db->prepare($db->sql());

$sth->execute();

$array_search['fullname'] = '';
if (! empty($array_search['userid'])) {
    $userinfo = $db->query('SELECT first_name, last_name, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $array_search['userid'])->fetch();
    if ($userinfo) {
        $array_search['fullname'] = nv_show_name_user($userinfo['first_name'], $userinfo['last_name'], $userinfo['username']);
    }
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('SEARCH', $array_search);
$xtpl->assign('BASE_URL', $base_url);

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
if (! empty($generate_page)) {
    $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}

while ($view = $sth->fetch()) {
    $view['ck_status_admin'] = $view['status_admin'] == 1 ? 'checked="checked"' : '';
    $view['type'] = ! empty($view['typeid']) ? $array_type[$view['typeid']]['title'] : '';
    $view['area'] = $location->locationString($view['area_p'], $view['area_d']);
    $view['cat'] = $array_market_cat[$view['catid']]['title'];
    $view['addtime'] = nv_date('H:i d/m/Y', $view['addtime']);
    $view['checkss'] = md5($global_config['sitekey'] . '-' . $view['userid'] . '-' . $view['id']);

    $view['adduser'] = $lang_global['guests'];
    $userinfo = $db->query('SELECT first_name, last_name, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $view['userid'])->fetch();
    if ($userinfo) {
        $view['adduser'] = nv_show_name_user($userinfo['first_name'], $userinfo['last_name'], $userinfo['username']);
    }

    $view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&amp;id=' . $view['id'] . '&amp;redirect=' . nv_redirect_encrypt($client_info['selfurl']);
    $view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_id=' . $view['id'] . '&amp;delete_checkss=' . md5($view['id'] . NV_CACHE_PREFIX . $client_info['session_id']);
    $view['link_view'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_market_cat[$view['catid']]['alias'] . '/' . $view['alias'] . '-' . $view['id'] . $global_config['rewrite_exturl'];
    $xtpl->assign('VIEW', $view);

    if ($queue) {
        $xtpl->parse('main.loop.queue');
    }

    if (class_exists('Facebook\Facebook') and $array_config['fb_enable'] and ! empty($array_config['fb_appid']) and ! empty($array_config['fb_secret']) and ! empty($array_config['fb_accesstoken'])) {
        $xtpl->parse('main.loop.post_facebook');
    }

    if (! empty($view['type'])) {
        $xtpl->parse('main.loop.type');
    }

    if (! empty($array_config['refresh_allow'])) {
        $xtpl->parse('main.loop.refresh');
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

if (class_exists('PHPExcel') and $num_items > 0) {
    $xtpl->assign('URL_EXPORT', $base_url . '&amp;export=1');
} else {
    $xtpl->parse('main.export_disabled');
}

if (! empty($array_search['userid']) and ! empty($array_search['fullname'])) {
    $xtpl->parse('main.userid');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

if ($queue) {
    $set_active_op = 'queue';
    $lang_module['list'] = $lang_module['queue'];
}

$page_title = $lang_module['list'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';