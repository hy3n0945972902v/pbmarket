<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:14
 */
if (! defined('NV_IS_MOD_MARKET')) {
    die('Stop!!!');
}

$alias = $nv_Request->get_title('alias', 'get');
$array_op = explode('/', $alias);
$alias = $array_op[0];

if (isset($array_op[1])) {
    if (sizeof($array_op) == 2 and preg_match('/^page\-([0-9]+)$/', $array_op[1], $m)) {
        $page = intval($m[1]);
    } else {
        $alias = '';
    }
}

$title = trim(str_replace('-', ' ', $alias));
$page_title = $title;

if (! empty($page_title) and $page_title == strip_punctuation($page_title)) {
    $stmt = $db_slave->prepare('SELECT tid, image, description, keywords FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags WHERE alias= :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    list ($tid, $image_tag, $description, $key_words) = $stmt->fetch(3);

    if ($tid > 0) {
        $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=tag/' . $alias;
        if ($page > 1) {
            $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
        }

        $array_mod_title[] = array(
            'catid' => 0,
            'title' => $page_title,
            'link' => $base_url
        );

        $item_array = array();

        $db->sqlreset()
            ->select('COUNT(*)')
            ->from(NV_PREFIXLANG . '_' . $module_data . '_rows')
            ->where('status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ') AND id IN (SELECT id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE tid=' . $tid . ')');

        $sth = $db->prepare($db->sql());

        $sth->execute();
        $num_items = $sth->fetchColumn();

        $db->select('id, title, alias, catid, area_p, area_d, typeid, pricetype, price, price1, unitid, homeimgfile, homeimgalt, homeimgthumb, countview, countcomment, groupview, addtime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, groups_config')
            ->order('prior DESC, ordertime DESC')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page);
        $sth = $db->prepare($db->sql());
        $sth->execute();

        while ($item = $sth->fetch()) {
            $item_array[] = nv_market_data($item, $module_name);
        }

        $generate_page = nv_alias_page($page_title, $base_url, $num_items, $per_page, $page);

        if (! empty($image_tag)) {
            $image_tag = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $image_tag;
        }
        $contents = nv_theme_market_viewtag($title, $item_array, $array_config['style_default'], $generate_page);

        if ($page > 1) {
            $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
        }
        include NV_ROOTDIR . '/includes/header.php';
        echo nv_site_theme($contents);
        include NV_ROOTDIR . '/includes/footer.php';
    }
}

$redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true) . '" />';
nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect, 404);