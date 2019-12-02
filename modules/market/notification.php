<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Apr 20, 2010 10:47:41 AM
 */
if (! defined('NV_IS_FILE_SITEINFO')) {
    die('Stop!!!');
}

$lang_siteinfo = nv_get_lang_module($mod);

$title = $db->query('SELECT title FROM ' . NV_PREFIXLANG . '_' . $site_mods[$mod]['module_data'] . '_rows WHERE id=' . $data['obid'])->fetchColumn();

$data['title'] = sprintf($lang_siteinfo['notification_auction_register'], $data['send_from'], $title);
$data['link'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $data['module'] . '&amp;' . NV_OP_VARIABLE . '=auction&amp;id=' . $data['obid'];