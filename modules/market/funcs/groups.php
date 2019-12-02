<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (! defined('NV_IS_MOD_MARKET'))
    die('Stop!!!');

if (isset($array_op[1])) {
    $alias = trim($array_op[1]);
    $page = (isset($array_op[2]) and substr($array_op[2], 0, 5) == 'page-') ? intval(substr($array_op[2], 5)) : 1;

    $stmt = $db->prepare('SELECT bid, title, alias, image, description, keywords FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block_cat WHERE alias= :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    list ($bid, $page_title, $alias, $image_group, $description, $key_words) = $stmt->fetch(3);
    if ($bid > 0) {
        $base_url_rewrite = $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['groups'] . '/' . $alias;
        $array_data = array();

        if ($page > 1) {
            $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
            $base_url_rewrite .= '/page-' . $page;
        }
        $base_url_rewrite = nv_url_rewrite($base_url_rewrite, true);
        if ($_SERVER['REQUEST_URI'] != $base_url_rewrite and NV_MAIN_DOMAIN . $_SERVER['REQUEST_URI'] != $base_url_rewrite) {
            Header('Location: ' . $base_url_rewrite);
            die();
        }

        $array_mod_title[] = array(
            'catid' => 0,
            'title' => $page_title,
            'link' => $base_url
        );

        $db->sqlreset()
            ->select('COUNT(*)')
            ->from(NV_PREFIXLANG . '_' . $module_data . '_rows t1')
            ->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_block t2 ON t1.id = t2.id')
            ->where('t2.bid= ' . $bid . ' AND (t2.exptime = 0 OR t2.exptime >= ' . NV_CURRENTTIME . ') AND status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ')')
            ->where('status=1 AND t2.bid= ' . $bid);

        $all_page = $db->query($db->sql())
            ->fetchColumn();

        $db->select('t1.id, title, alias, catid, area_p, area_d, typeid, pricetype, price, price1, unitid, homeimgfile, homeimgalt, homeimgthumb, countview, countcomment, groupview, addtime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, groups_config')
            ->order('t1.prior DESC, t1.ordertime DESC')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page);

        $sth = $db->prepare($db->sql());
        $sth->execute();

        while ($row = $sth->fetch()) {
            if (nv_user_in_groups($row['groupview'])) {
                if (! empty($data = nv_market_data($row, $module_name))) {
                    $array_data[$row['id']] = $data;
                }
            }
        }

        if (! empty($image_group) and file_exists(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $image_group)) {
            $image_group = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $image_group;
            $meta_property['og:image'] = NV_MY_DOMAIN . $image_group;
        }

        $groups_data = array(
            'title' => $page_title,
            'image' => $image_group,
            'description' => $description
        );

        $generate_page = nv_alias_page($page_title, $base_url, $all_page, $per_page, $page);

        $contents = nv_theme_market_viewgroup($groups_data, $array_data, $array_config['style_default'], $generate_page);
    }
} else {
    Header('Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true));
    die();
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';