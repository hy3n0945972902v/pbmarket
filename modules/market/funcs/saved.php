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
}

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
$array_data = array();

$db->sqlreset()
    ->select('COUNT(*)')
    ->from(NV_PREFIXLANG . '_' . $module_data . '_rows t1')
    ->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_saved t2 ON t1.id=t2.rowsid')
    ->where('status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ')');

$sth = $db->prepare($db->sql());

$sth->execute();
$num_items = $sth->fetchColumn();

$db->select('id, title, alias, catid, area_p, area_d, typeid, pricetype, price, price1, unitid, homeimgfile, homeimgalt, homeimgthumb, countview, countcomment, groupview, addtime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, groups_config')
    ->order('ordertime DESC')
    ->limit($per_page)
    ->offset(($page - 1) * $per_page);

$sth = $db->prepare($db->sql());
$sth->execute();

while ($row = $sth->fetch()) {
    if (nv_user_in_groups($row['groupview'])) {
        if (! empty($data = nv_market_data($row, $module_name))) {
            $data['checkss'] = md5($global_config['sitekey'] . '-' . $data['id']);
            $array_data[$row['id']] = $data;
        }
    }
}

if ($page > 1) {
    $page_title = $page_title . ' - ' . $lang_global['page'] . ' ' . $page;
}
$page = nv_alias_page($page_title, $base_url, $num_items, $per_page, $page);

$contents = nv_theme_market_saved($array_data, $page);

$page_title = $lang_module['saved'];

$array_mod_title[] = array(
    'title' => $page_title,
    'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op
);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';