<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.com)
 * @Copyright (C) 2017 mynukeviet. All rights reserved
 * @Createdate Sat, 04 Feb 2017 10:20:50 GMT
 */
if (! defined('NV_IS_MOD_MARKET'))
    die('Stop!!!');

$url = array();
$cacheFile = NV_LANG_DATA . '_sitemap_' . NV_CACHE_PREFIX . '.cache';
$cacheTTL = 7200;

if (($cache = $nv_Cache->getItem($module_name, $cacheFile, $cacheTTL)) != false) {
    $url = unserialize($cache);
} else {
    $db->sqlreset()
        ->select('id, catid, addtime, alias')
        ->from(NV_PREFIXLANG . '_' . $module_data . '_rows')
        ->where('status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ')')
        ->order('addtime DESC')
        ->limit(1000);
    $result = $db->query($db->sql());
    
    $url = array();
    
    while (list ($id, $catid_i, $publtime, $alias) = $result->fetch(3)) {
        $catalias = $array_market_cat[$catid_i]['alias'];
        $url[] = array(
            'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $catalias . '/' . $alias . '-' . $id . $global_config['rewrite_exturl'],
            'publtime' => $publtime
        );
    }
    
    $cache = serialize($url);
    $nv_Cache->setItem($module_name, $cacheFile, $cache, $cacheTTL);
}

nv_xmlSitemap_generate($url);
die();