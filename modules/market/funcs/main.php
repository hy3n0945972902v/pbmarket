<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (!defined('NV_IS_MOD_MARKET')) die('Stop!!!');

$page_title = $module_info['site_title'];
$key_words = $module_info['keywords'];

$array_data = array();
$contents = '';
$cache_file = '';

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
$base_url_rewrite = nv_url_rewrite($base_url, true);
$page_url_rewrite = ($page > 1) ? nv_url_rewrite($base_url . '/page-' . $page, true) : $base_url_rewrite;
$request_uri = $_SERVER['REQUEST_URI'];
if (!($home or $request_uri == $base_url_rewrite or $request_uri == $page_url_rewrite or NV_MAIN_DOMAIN . $request_uri == $base_url_rewrite or NV_MAIN_DOMAIN . $request_uri == $page_url_rewrite)) {
    $redirect = '<meta http-equiv="Refresh" content="3;URL=' . $base_url_rewrite . '" />';
    nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect, 404);
}

if (!defined('NV_IS_MODADMIN') and $page < 5) {
    $cache_file = NV_LANG_DATA . '_' . $module_info['template'] . '-' . $op . '-' . $page . '-' . NV_CACHE_PREFIX . '.cache';
    if (($cache = $nv_Cache->getItem($module_name, $cache_file)) != false) {
        $contents = $cache;
    }
}

if (empty($contents)) {
    // hien thi tat ca
    if ($array_config['homedata'] == 1) {
        $db->sqlreset()
            ->select('COUNT(*)')
            ->from(NV_PREFIXLANG . '_' . $module_data . '_rows t1')
            ->join(' INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_detail t2 ON t1.id = t2.id')
            ->where('status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ')');

        $sth = $db->prepare($db->sql());

        $sth->execute();
        $num_items = $sth->fetchColumn();

        $db->select('t1.id, title, alias, catid, area_p, area_d, typeid, pricetype, price, price1, unitid, homeimgfile, homeimgalt, homeimgthumb, countview, countcomment, groupview, addtime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, groups_config, t2.contact_fullname, t2.contact_phone, t2.contact_email, t2.contact_address')
            ->order('prior DESC, ordertime DESC')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page);

        $sth = $db->prepare($db->sql());
        $sth->execute();

        while ($row = $sth->fetch()) {
            if (nv_user_in_groups($row['groupview'])) {
                if (!empty($data = nv_market_data($row, $module_name))) {
                    $array_data[$row['id']] = $data;
                }
            }
        }

        if ($page > 1) {
            $page_title = $page_title . ' - ' . $lang_global['page'] . ' ' . $page;
        }
        $page = nv_alias_page($page_title, $base_url, $num_items, $per_page, $page);

        $contents = nv_theme_market_main($array_data, $array_config['hometype'], $page);

        if (!defined('NV_IS_MODADMIN') and $contents != '' and $cache_file != '') {
            $nv_Cache->setItem($module_name, $cache_file, $contents);
        }
    } elseif ($array_config['homedata'] == 2) {
        if (!empty($array_market_cat)) {
            foreach ($array_market_cat as $catid_i => $array_info_i) {
                if ($array_info_i['parentid'] == 0 and $array_info_i['inhome']) {
                    $array_market_cat_id = nv_GetCatidInParent($catid_i);

                    $db->sqlreset()
                        ->select('COUNT(*)')
                        ->from(NV_PREFIXLANG . '_' . $module_data . '_rows')
                        ->where('status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ') AND catid IN (' . implode(',', $array_market_cat_id) . ')');

                    $sth = $db->prepare($db->sql());

                    $sth->execute();
                    $num_items = $sth->fetchColumn();

                    $db->select('id,contact_phone, contact_fullname, title, alias, catid, area_p, area_d, typeid, pricetype, price, price1, unitid, homeimgfile, homeimgalt, homeimgthumb, countview, countcomment, groupview, addtime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, groups_config')
                        ->order('prior DESC, ordertime DESC')
                        ->limit($per_page)
                        ->offset(($page - 1) * $per_page);

                    $sth = $db->prepare($db->sql());
                    $sth->execute();

                    $data = array();
                    while ($row = $sth->fetch()) {
                        if (nv_user_in_groups($row['groupview'])) {
                            if (!empty($row = nv_market_data($row, $module_name))) {
                                $data[$row['id']] = $row;
                            }
                        }
                    }
                    $array_data[$catid_i] = array(
                        'title' => $array_info_i['title'],
                        'link' => $array_info_i['link'],
                        'viewtype' => $array_info_i['viewtype'],
                        'numlinks' => $array_info_i['numlinks'],
                        'count' => $num_items,
                        'subid' => $array_info_i['subid'],
                        'data' => $data
                    );
                }
            }
        }

        $contents = nv_theme_market_main_cat($array_data, $array_config['hometype']);

        if (!defined('NV_IS_MODADMIN') and $contents != '' and $cache_file != '') {
            $nv_Cache->setItem($module_name, $cache_file, $contents);
        }
    }
}


include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';