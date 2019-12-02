<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2015 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 11 Aug 2015 04:09:47 GMT
 */
if (! defined('NV_IS_MOD_MARKET'))
    die('Stop!!!');

$id = explode('-', $array_op[1]);
$id = intval(end($id));

$company_info = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_company WHERE id=' . $id)->fetch();
if (! $company_info) {
    Header('Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true));
    die();
}

require_once NV_ROOTDIR . '/modules/location/location.class.php';
$location = new Location();

$company_info['location'] = $location->locationString($company_info['provinceid'], $company_info['districtid'], 0, ' » ');
$company_info['image'] = ! empty($company_info['image']) ? NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $company_info['image'] : '';

$contents = nv_theme_market_company($company_info);

$page_title = $company_info['title'];
$array_mod_title[] = array(
    'title' => $page_title,
    'link' => $client_info['selfurl']
);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';