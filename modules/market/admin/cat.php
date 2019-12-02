<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2015 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 02 Jun 2015 07:53:31 GMT
 */
if (!defined('NV_IS_FILE_ADMIN')) die('Stop!!!');

$table_name = NV_PREFIXLANG . '_' . $module_data . '_cat';
$currentpath = NV_UPLOADS_DIR . '/' . $module_upload;
$groups_list = nv_groups_list();

$cat_form_exit = [];
if (is_dir(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_upload . '/files_tpl')) {
    $_form_exit = scandir(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_upload . '/files_tpl');
    foreach ($_form_exit as $_form) {
        if (preg_match('/^cat\_form\_([a-zA-Z0-9\-\_]+)\.tpl$/', $_form, $m)) {
            $cat_form_exit[] = $m[1];
        }
    }
}

if ($nv_Request->isset_request('get_alias_title', 'post')) {
    $alias = $nv_Request->get_title('get_alias_title', 'post', '');
    $alias = change_alias($alias);
    if ($array_config['tags_alias_lower']) {
        $alias = strtolower($alias);
    }
    die($alias);
}

// change status
if ($nv_Request->isset_request('change_status', 'post, get')) {
    $id = $nv_Request->get_int('id', 'post, get', 0);
    $content = 'NO_' . $id;

    $query = 'SELECT status FROM ' . $table_name . ' WHERE id=' . $id;
    $row = $db->query($query)->fetch();
    if (isset($row['status'])) {
        $status = ($row['status']) ? 0 : 1;
        $query = 'UPDATE ' . $table_name . ' SET status=' . intval($status) . ' WHERE id=' . $id;
        $db->query($query);
        $content = 'OK_' . $id;
    }
    $nv_Cache->delMod($module_name);
    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
    exit();
}

if ($nv_Request->isset_request('ajax_action', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $new_vid = $nv_Request->get_int('new_vid', 'post', 0);
    $content = 'NO_' . $id;

    list ($id, $parentid) = $db->query('SELECT id, parentid FROM ' . $table_name . ' WHERE id=' . $id)->fetch(3);

    if ($new_vid > 0) {
        $sql = 'SELECT id FROM ' . $table_name . ' WHERE id!=' . $id . ' AND parentid=' . $parentid . ' ORDER BY weight ASC';
        $result = $db->query($sql);
        $weight = 0;
        while ($row = $result->fetch()) {
            ++$weight;
            if ($weight == $new_vid) ++$weight;
            $sql = 'UPDATE ' . $table_name . ' SET weight=' . $weight . ' WHERE id=' . $row['id'];
            $db->query($sql);
        }
        $sql = 'UPDATE ' . $table_name . ' SET weight=' . $new_vid . ' WHERE id=' . $id;
        $db->query($sql);

        nv_fix_order($table_name);

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
        list ($id, $parentid) = $db->query('SELECT id, parentid FROM ' . $table_name . ' WHERE id=' . $id)->fetch(3);

        $weight = 0;
        $sql = 'SELECT weight FROM ' . $table_name . ' WHERE id =' . $db->quote($id);
        $result = $db->query($sql);
        list ($weight) = $result->fetch(3);

        $db->query('DELETE FROM ' . $table_name . '  WHERE id = ' . $db->quote($id) . ' OR parentid=' . $id);
        if ($weight > 0) {
            $sql = 'SELECT id, weight FROM ' . $table_name . ' WHERE weight >' . $weight;
            $result = $db->query($sql);
            while (list ($id, $weight) = $result->fetch(3)) {
                $weight--;
                $db->query('UPDATE ' . $table_name . ' SET weight=' . $weight . ' WHERE id=' . intval($id));
            }
        }

        nv_fix_order($table_name);

        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&parentid=' . $parentid);
        die();
    }
}

$row = array();
$error = array();
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);
$row['parentid'] = $nv_Request->get_int('parentid', 'get,post', 0);

if ($nv_Request->isset_request('submit', 'post')) {
    $row['parentid'] = $nv_Request->get_int('parentid', 'post', 0);
    $data['parentid_old'] = $nv_Request->get_int('parentid_old', 'post', 0);
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['custom_title'] = $nv_Request->get_title('custom_title', 'post', '');
    $row['keywords'] = $nv_Request->get_title('keywords', 'post', '');
    $row['pricetype'] = $nv_Request->get_int('pricetype', 'post', 0);
    $row['form'] = $nv_Request->get_array('cat_form', 'post', array());
    $row['form'] = array_intersect($row['form'], $cat_form_exit);

    // xu ly alias
    $row['alias'] = $nv_Request->get_title('alias', 'post', '', 1);
    $row['alias'] = empty($row['alias']) ? change_alias($row['title']) : $row['alias'];
    $stmt = $db->prepare('SELECT COUNT(*) FROM ' . $table_name . ' WHERE id !=' . $row['id'] . ' AND alias = :alias');
    $stmt->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn()) {
        $weight = $db->query('SELECT MAX(id) FROM ' . $table_name)->fetchColumn();
        $weight = intval($weight) + 1;
        $row['alias'] = $row['alias'] . '-' . $weight;
    }

    $row['description'] = $nv_Request->get_string('description', 'post', '');
    $row['description_html'] = $nv_Request->get_editor('description_html', '', NV_ALLOWED_HTML_TAGS);
    $row['image'] = $nv_Request->get_title('image', 'post', '');
    if (is_file(NV_DOCUMENT_ROOT . $row['image'])) {
        $row['image'] = substr($row['image'], strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/'));
    } else {
        $row['image'] = '';
    }
    $_groups_post = $nv_Request->get_array('groups_view', 'post', array());
    $row['groups_view'] = !empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    if (empty($row['title'])) {
        $error[] = $lang_module['error_required_title'];
    }

    if (empty($error)) {
        try {
            $new_id = 0;
            $form = $row['form'] ? implode(',', $row['form']) : '';
            if (empty($row['id'])) {
                $data_insert = array();
                $_sql = 'INSERT INTO ' . $table_name . ' (parentid, title, alias, custom_title, keywords, description, description_html, groups_view, image, pricetype, form, weight) VALUES (:parentid, :title, :alias, :custom_title, :keywords, :description, :description_html, :groups_view, :image, :pricetype, :form, :weight)';
                $weight = $db->query('SELECT max(weight) FROM ' . $table_name . '')->fetchColumn();
                $weight = intval($weight) + 1;

                $data_insert['parentid'] = $row['parentid'];
                $data_insert['title'] = $row['title'];
                $data_insert['alias'] = $row['alias'];
                $data_insert['custom_title'] = $row['custom_title'];
                $data_insert['keywords'] = $row['keywords'];
                $data_insert['description'] = $row['description'];
                $data_insert['description_html'] = $row['description_html'];
                $data_insert['groups_view'] = $row['groups_view'];
                $data_insert['image'] = $row['image'];
                $data_insert['pricetype'] = $row['pricetype'];
                $data_insert['form'] = $form;
                $data_insert['weight'] = $weight;
                $new_id = $db->insert_id($_sql, 'id', $data_insert);
            } else {
                $stmt = $db->prepare('UPDATE ' . $table_name . ' SET parentid=:parentid, title = :title, alias = :alias, custom_title = :custom_title, keywords = :keywords, description = :description, description_html = :description_html, groups_view = :groups_view, image = :image, pricetype = :pricetype, form = :form WHERE id=' . $row['id']);
                $stmt->bindParam(':parentid', $row['parentid'], PDO::PARAM_INT);
                $stmt->bindParam(':title', $row['title'], PDO::PARAM_STR);
                $stmt->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
                $stmt->bindParam(':custom_title', $row['custom_title'], PDO::PARAM_STR);
                $stmt->bindParam(':keywords', $row['keywords'], PDO::PARAM_STR);
                $stmt->bindParam(':description', $row['description'], PDO::PARAM_STR, strlen($row['description']));
                $stmt->bindParam(':description_html', $row['description_html'], PDO::PARAM_STR, strlen($row['description_html']));
                $stmt->bindParam(':groups_view', $row['groups_view'], PDO::PARAM_STR);
                $stmt->bindParam(':image', $row['image'], PDO::PARAM_STR);
                $stmt->bindParam(':pricetype', $row['pricetype'], PDO::PARAM_INT);
                $stmt->bindParam(':form', $form, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    $new_id = $row['id'];
                }
            }

            if ($new_id > 0) {
                nv_fix_order($table_name);
                $nv_Cache->delMod($module_name);
                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&parentid=' . $row['parentid']);
                die();
            }
        } catch (PDOException $e) {
            trigger_error($e->getMessage());
        }
    }
} elseif ($row['id'] > 0) {
    $lang_module['cat_add'] = $lang_module['cat_edit'];
    $row = $db->query('SELECT * FROM ' . $table_name . ' WHERE id=' . $row['id'])->fetch();
    if (empty($row)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }

    if (!empty($row['image']) and file_exists(NV_UPLOADS_REAL_DIR)) {
        $currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . dirname($row['image']);
    }
} else {
    $row['id'] = 0;
    $row['title'] = '';
    $row['custom_title'] = '';
    $row['alias'] = '';
    $row['keywords'] = '';
    $row['description'] = '';
    $row['description_html'] = '';
    $row['groups_view'] = !empty($row['parentid']) ? $array_market_cat[$row['parentid']]['groups_view'] : 6;
    $row['image'] = '';
    $row['pricetype'] = !empty($row['parentid']) ? $array_market_cat[$row['parentid']]['pricetype'] : 1;
    $row['form'] = array();
    if ($row['parentid']) {
        $row['form'] = $db->query('SELECT form FROM ' . $table_name . ' WHERE id=' . $row['parentid'])->fetchColumn();
        $row['form'] = explode(',', $row['form']);
    }
}

// Fetch Limit
$show_view = false;
if (!$nv_Request->isset_request('id', 'post,get')) {
    $show_view = true;
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'post,get', 1);
    $db->sqlreset()
        ->select('COUNT(*)')
        ->from($table_name)
        ->where('parentid=' . $row['parentid']);

    $sth = $db->prepare($db->sql());

    $sth->execute();
    $num_items = $sth->fetchColumn();

    $db->select('*')
        ->order('weight ASC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $sth = $db->prepare($db->sql());
    $sth->execute();
}

$sql = 'SELECT id, title, lev FROM ' . $table_name . ' WHERE id !=' . $row['id'] . ' AND status=1 ORDER BY sort ASC';
$result = $db->query($sql);
$array_cat_list = array();
$array_cat_list[0] = array(
    '0',
    $lang_module['cat_main']
);

while (list ($id_i, $title_i, $lev_i) = $result->fetch(3)) {
    $xtitle_i = '';
    if ($lev_i > 0) {
        $xtitle_i .= '&nbsp;';
        for ($i = 1; $i <= $lev_i; $i++) {
            $xtitle_i .= '---';
        }
    }
    $xtitle_i .= $title_i;
    $array_cat_list[] = array(
        $id_i,
        $xtitle_i
    );
}

$row['description_html'] = htmlspecialchars(nv_editor_br2nl($row['description_html']));
if (!empty($row['image']) and is_file(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $row['image'])) {
    $row['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $row['image'];
}

if (defined('NV_EDITOR')) {
    require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}

if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor')) {
    $row['description_html'] = nv_aleditor('description_html', '100%', '200px', $row['description_html'], 'Basic');
} else {
    $row['description_html'] = "<textarea style=\"width: 100%\" name=\"description_html\" id=\"description_html\" cols=\"20\" rows=\"15\">" . $row['description_html'] . "</textarea>";
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('CURENTPATH', $currentpath);

$array_inhome = array(
    '1' => $lang_module['cat_inhome_1'],
    '0' => $lang_module['cat_inhome_0']
);

$array_viewtype = array(
    'viewlist' => $lang_module['cat_hometype_viewlist'],
    'viewlist_simple' => $lang_module['cat_hometype_viewlist_simple']
);

if ($show_view) {
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    if (!empty($generate_page)) {
        $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.view.generate_page');
    }
    while ($view = $sth->fetch()) {
        for ($i = 1; $i <= $num_items; ++$i) {
            $xtpl->assign('WEIGHT', array(
                'key' => $i,
                'title' => $i,
                'selected' => ($i == $view['weight']) ? ' selected="selected"' : ''
            ));
            $xtpl->parse('main.view.loop.weight_loop');
        }
        if ($view['status'] == 1) {
            $check = 'checked';
        } else {
            $check = '';
        }
        $xtpl->assign('CHECK', $check);

        foreach ($array_inhome as $key => $value) {
            $sl = $view['inhome'] == $key ? 'selected="selected"' : '';
            $xtpl->assign('INHOME', array(
                'key' => $key,
                'value' => $value,
                'selected' => $sl
            ));
            $xtpl->parse('main.view.loop.inhome');
        }

        foreach ($array_viewtype as $key => $value) {
            $sl = $view['viewtype'] == $key ? 'selected="selected"' : '';
            $xtpl->assign('VIEWTYPE', array(
                'key' => $key,
                'value' => $value,
                'selected' => $sl
            ));
            $xtpl->parse('main.view.loop.viewtype');
        }

        for ($i = 0; $i <= 20; ++$i) {
            $xtpl->assign('NUMLINKS', array(
                'key' => $i,
                'title' => $i,
                'selected' => $i == $view['numlinks'] ? ' selected="selected"' : ''
            ));
            $xtpl->parse('main.view.loop.numlinks');
        }

        $view['link_view'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;parentid=' . $view['id'];
        $view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;id=' . $view['id'];
        $view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_id=' . $view['id'] . '&amp;delete_checkss=' . md5($view['id'] . NV_CACHE_PREFIX . $client_info['session_id']);
        $xtpl->assign('VIEW', $view);
        $xtpl->parse('main.view.loop');
    }
    $xtpl->parse('main.view');
}

foreach ($array_cat_list as $rows_i) {
    $sl = ($rows_i[0] == $row['parentid']) ? ' selected="selected"' : '';
    $xtpl->assign('pid', $rows_i[0]);
    $xtpl->assign('ptitle', $rows_i[1]);
    $xtpl->assign('pselect', $sl);
    $xtpl->parse('main.parent_loop');
}

$groups_view = explode(',', $row['groups_view']);
foreach ($groups_list as $group_id => $grtl) {
    $_groups_view = array(
        'value' => $group_id,
        'checked' => in_array($group_id, $groups_view) ? ' checked="checked"' : '',
        'title' => $grtl
    );
    $xtpl->assign('GROUPS_VIEW', $_groups_view);
    $xtpl->parse('main.groups_view');
}

if ($array_pricetype_cat) {
    foreach ($array_pricetype_cat as $index => $value) {
        $sl = $index == $row['pricetype'] ? 'selected="selected"' : '';
        $xtpl->assign('PRICETYPE', array(
            'index' => $index,
            'value' => $value,
            'selected' => $sl
        ));
        $xtpl->parse('main.pricetype');
    }
}

if (empty($row['id'])) {
    $xtpl->parse('main.auto_get_alias');
}

if (!empty($cat_form_exit)) {
    $row['form'] = !empty($row['form']) ? explode(',', $row['form']) : array();
    foreach ($cat_form_exit as $_form) {
        $xtpl->assign('CAT_FORM', array(
            'value' => $_form,
            'checked' => in_array($_form, $row['form']) ? ' checked="checked"' : '',
            'title' => $_form
        ));
        $xtpl->parse('main.cat_form.loop');
    }
    $xtpl->parse('main.cat_form');
}

if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['cat'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';