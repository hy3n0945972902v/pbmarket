<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (!defined('NV_IS_MOD_MARKET')) die('Stop!!!');

$page = $nv_Request->get_int('page', 'get', 1);
$array_title = $array_alias = $array_data = array();
$where = '';
$is_search = 0;

$array_data = array();
$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '/';
$base_url_rewrite = $request_uri = urldecode($_SERVER['REQUEST_URI']);

$array_search = array(
    'type' => $nv_Request->get_int('type', 'post,get', $array_search_params['typeid']),
    'q' => $nv_Request->get_title('q', 'post,get', ''),
    'catid' => $nv_Request->get_int('catid', 'post,get', $array_search_params['catid']),
    'area_p' => $nv_Request->get_int('area_p', 'post,get', $array_search_params['provinceid']),
    'area_d' => $nv_Request->get_int('area_d', 'post,get', $array_search_params['districtid'])
);

if (!empty($array_search['type'])) {
    $where .= ' AND typeid=' . $array_search['type'];
    $array_alias[] = $array_type[$array_search['type']]['alias'];
    $array_title['type'] = $array_type[$array_search['type']]['title'];
}

if (!empty($array_search['catid'])) {
    $_array_cat = nv_GetCatidInParent($array_search['catid']);
    $where .= ' AND catid IN (' . implode(',', $_array_cat) . ')';
    $array_title['typeid'] = $array_title['typeid'] . ' ' . $array_market_cat[$array_search['catid']]['title'];
    $array_alias[] = $array_market_cat[$array_search['catid']]['alias'];
}

if (!empty($array_search['area_d'])) {
    $where .= ' AND area_d = ' . $array_search['area_d'];
    $search_district = $db->query('SELECT title, alias, type FROM ' . $db_config['prefix'] . '_location_district WHERE districtid=' . $db->quote($array_search['area_d']))
        ->fetch();
    $array_title[] = $search_district['type'] . ' ' . $search_district['title'];
    $array_alias[] = change_alias($search_district['type']) . '-' . $search_district['alias'];
}

if (!empty($array_search['area_p'])) {
    $where .= ' AND area_p=' . $array_search['area_p'];
    $search_province = $db->query('SELECT title, alias, type FROM ' . $db_config['prefix'] . '_location_province WHERE provinceid=' . $db->quote($array_search['area_p']))
        ->fetch();
    $array_alias[] = change_alias($search_province['type']) . '-' . $search_province['alias'];
    $array_title[] = $search_province['type'] . ' ' . $search_province['title'];
}

if (!empty($array_search['q'])) {
    $where .= ' AND (code LIKE ' . $db->quote("%" . $array_search['q'] . "%") . '
    	OR title LIKE ' . $db->quote("%" . $array_search['q'] . "%") . '
    	OR content LIKE ' . $db->quote("%" . $array_search['q'] . "%") . '
    	OR note LIKE ' . $db->quote("%" . $array_search['q'] . "%") . ')';
}

if (!empty($array_search['q'])) {
    $array_title[] = $array_search['q'];
}

if ($nv_Request->isset_request('is_search', 'get')) {
    $base_url .= !empty($array_alias) ? implode('-', $array_alias) : '';
    if (!empty($array_search['q'])) {
        $base_url .= '&q=' . $array_search['q'];
    }
    nv_redirect_location(strtolower($base_url));
}

if (!empty($where)) {
    $is_search = 1;
}

$viewtype = $array_config['hometype'];

$db->sqlreset()
    ->select('COUNT(*)')
    ->from(NV_PREFIXLANG . '_' . $module_data . '_rows t1')
    ->join(' INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_detail t2 ON t1.id=t2.id')
    ->where('status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ')' . $where);
$sth = $db->prepare($db->sql());

$sth->execute();
$num_items = $sth->fetchColumn();

$db->select('t1.id, title, alias, catid, area_p, area_d, typeid, pricetype, price, price1, unitid, homeimgfile, homeimgalt, homeimgthumb, countview, countcomment, groupview, addtime, groups_config, t2.maps')
    ->order('ordertime DESC')
    ->limit($per_page)
    ->offset(($page - 1) * $per_page);
$sth = $db->prepare($db->sql());
$sth->execute();

$array_json = array();
while ($row = $sth->fetch()) {
    if (nv_user_in_groups($row['groupview'])) {
        if (!empty($data = nv_market_data($row, $module_name))) {
            $array_data[$row['id']] = $data;
        }
    }

    if ($row['maps']) {
        $row['maps'] = unserialize($row['maps']);
        $json['lat'] = $row['maps']['maps_mapcenterlat'];
        $json['lng'] = $row['maps']['maps_mapcenterlng'];
        $json['title'] = $row['title'];
        $array_json[] = $json;
    }
}

$lang_module['search_result_number'] = sprintf($lang_module['search_result_number'], $num_items);

if ($page > 1) {
    $page_title = $page_title . ' - ' . $lang_global['page'] . ' ' . $page;
}
$generate_page = '';
if ($num_items > $per_page) {
    $url_link = $_SERVER['REQUEST_URI'];
    if (strpos($url_link, '&page=') > 0) {
        $url_link = substr($url_link, 0, strpos($url_link, '&page='));
    } elseif (strpos($url_link, '?page=') > 0) {
        $url_link = substr($url_link, 0, strpos($url_link, '?page='));
    }
    $_array_url = array(
        'link' => $url_link,
        'amp' => '&page='
    );
    $generate_page = nv_generate_page($_array_url, $num_items, $per_page, $page);
}

$page_title = implode(', ', $array_title);
$array_mod_title[] = array(
    'title' => $page_title
);

$contents = nv_theme_market_search($array_data, $is_search, $array_config['style_default'], $generate_page, $array_json);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';