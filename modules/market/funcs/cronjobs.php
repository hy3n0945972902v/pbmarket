<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (! defined('NV_IS_MOD_MARKET'))
    die('Stop!!!');
    
    // Dang tin len facebook
if (class_exists('Facebook\Facebook') and $array_config['fb_enable'] and ! empty($array_config['fb_appid']) and ! empty($array_config['fb_secret']) and ! empty($array_config['fb_pagetoken'])) {
    $result = $db_slave->query('SELECT rowsid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_fb_queue');
    while (list ($rowsid) = $result->fetch(3)) {
        $rows = $db_slave->query('SELECT id, catid, alias, content FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE id=' . $rowsid)->fetch();
        if ($rows) {
            $link = NV_MY_DOMAIN . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_market_cat[$rows['catid']]['alias'] . '/' . $rows['alias'] . '-' . $rows['id'] . $global_config['rewrite_exturl'], true);
            $message = strip_tags(nv_unhtmlspecialchars(html_entity_decode($rows['content'])), 'br');
            if (nv_post_facebook($link, $message)) {
                $db_slave->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_fb_queue WHERE rowsid=' . $rowsid);
            }
        }
    }
}

// Gui mail trong hang doi
$result = $db_slave->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_mail_queue');
while ($row = $result->fetch()) {
    $from = array(
        $global_config['site_name'],
        $global_config['site_email']
    );
    
    if (nv_sendmail($from, $row['tomail'], $row['subject'], $row['message'])) {
        $db_slave->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_mail_queue WHERE id=' . $row['id']);
    }
}