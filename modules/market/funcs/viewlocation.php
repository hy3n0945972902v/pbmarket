<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (! defined('NV_IS_MOD_MARKET'))
    die('Stop!!!');

require_once NV_ROOTDIR . '/modules/location/location.class.php';
$location = new Location();
$where = '';

switch ($array_op[0]) {
    case 'p':
        $location_info = $location->getProvinceInfo($id);
        $array_local_id = array_keys($location->getArrayDistrict('', $id));
        $where .= ' AND (area_p=' . $id . (! empty($array_local_id) ? ' OR area_d IN (' . implode(',', $array_local_id) . ')' : '') . ')';
        break;
    case 'd':
        $location_info = $location->getDistricInfo($id);
        $array_local_id = array_keys($location->getArrayWard('', $id));
        $where .= ' AND area_d=' . $id;
        break;
    default:
        $location_info = $location->getWardInfo($id);
        break;
}

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '/' . $array_op[0] . '/' . $array_op[1];
$array_data = array();

$db->sqlreset()
    ->select('COUNT(*)')
    ->from(NV_PREFIXLANG . '_' . $module_data . '_rows')
    ->where('status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ')' . $where);

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

$page_title = $location_info['type'] . ' ' . $location_info['title'];

if ($page > 1) {
    $page_title = $page_title . ' - ' . $lang_global['page'] . ' ' . $page;
}
$page = nv_alias_page($page_title, $base_url, $num_items, $per_page, $page);

$contents = nv_theme_market_viewlocation($location_info, $array_data, $array_config['style_default'], $page);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';