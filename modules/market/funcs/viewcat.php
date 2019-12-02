<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (! defined('NV_IS_MOD_MARKET'))
    die('Stop!!!');

if (! defined('NV_IS_MODADMIN') and $page < 5) {
    $cache_file = NV_LANG_DATA . '_' . $module_info['template'] . '_' . $op . '_' . $catid . '_' . $page . '_' . NV_CACHE_PREFIX . '.cache';
    if (($cache = $nv_Cache->getItem($module_name, $cache_file)) != false) {
        $contents = $cache;
    }
}

$page_title = ! empty($array_market_cat[$catid]['custom_title']) ? $array_market_cat[$catid]['custom_title'] : $array_market_cat[$catid]['title'];
$key_words = $array_market_cat[$catid]['keywords'];
$description = $array_market_cat[$catid]['description'];

if (empty($contents)) {
    $viewtype = $array_market_cat[$catid]['viewtype'];

    if (! empty($array_market_cat[$catid]['image']) and file_exists(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $array_market_cat[$catid]['image'])) {
        $meta_property['og:image'] = NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $array_market_cat[$catid]['image'];
    }

    $base_url = $array_market_cat[$catid]['link'];
    $array_data = $array_subcat_data = array();
    $array_catid = nv_GetCatidInParent($catid);

    if(!empty($array_market_cat[$catid]['subid'])){
        $subid = explode(',', $array_market_cat[$catid]['subid']);
        foreach($subid as $_catid){
            $array_subcat_data[$_catid] = $array_market_cat[$_catid];
        }
    }

    $db->sqlreset()
    ->select('COUNT(*)')
    ->from(NV_PREFIXLANG . '_' . $module_data . '_rows')
    ->where('status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ') AND catid IN (' . implode(',', $array_catid) . ')');

    $sth = $db->prepare($db->sql());

    $sth->execute();
    $num_items = $sth->fetchColumn();

    $db->select('id, title, alias, catid, area_p, area_d, typeid, pricetype, price, price1, unitid, homeimgfile, homeimgalt, homeimgthumb, countview, countcomment, groupview, addtime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, groups_config')
    ->order('prior DESC, ordertime DESC')
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

    if ($page > 1) {
        $page_title = $page_title . ' - ' . $lang_global['page'] . ' ' . $page;
    }
    $page = nv_alias_page($page_title, $base_url, $num_items, $per_page, $page);

    $contents = nv_theme_market_viewcat($array_data, $array_subcat_data, $viewtype, $page);

    if (! defined('NV_IS_MODADMIN') and $contents != '' and $cache_file != '') {
        $nv_Cache->setItem($module_name, $cache_file, $contents);
    }
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';