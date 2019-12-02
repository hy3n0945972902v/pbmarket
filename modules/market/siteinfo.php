<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (! defined('NV_IS_FILE_SITEINFO'))
    die('Stop!!!');

$lang_siteinfo = nv_get_lang_module($mod);

// Tong so tin rao vat dang hieu luc
$number = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ')')->fetchColumn();
if ($number > 0) {
    $siteinfo[] = array(
        'key' => $lang_siteinfo['siteinfo_countrows'],
        'value' => $number
    );
}

// tong so tin cho duyet
$number = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE is_queue=1 OR is_queue_edit=1')->fetchColumn();
if ($number > 0) {
    $pendinginfo[] = array(
        'key' => $lang_siteinfo['siteinfo_countqueue'],
        'value' => $number,
        'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $mod . '&amp;queue=1'
    );
}

// Nhac nho cac tu khoa chua co mo ta
if (! empty($module_config[$mod]['tags_remind'])) {
    $count = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_tags WHERE description = \'\'')->fetchColumn();
    if ($count > 0) {
        $pendinginfo[] = array(
            'key' => $lang_siteinfo['siteinfo_tags_incomplete'],
            'value' => number_format($count),
            'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $mod . '&amp;' . NV_OP_VARIABLE . '=tags&amp;incomplete=1'
        );
    }
}