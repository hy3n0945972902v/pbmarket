<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */
if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (! nv_function_exists('nv_market_userarea_statistics')) {

    function nv_market_userarea_statistics($block_config)
    {
        global $db, $module_info, $lang_module, $global_config, $module_name, $module_data, $lang_module, $user_info, $site_mods, $array_config;
        
        if (! defined('NV_IS_USER')) {
            return '';
        }
        
        $module = $block_config['module'];
        $mod_file = $site_mods[$module]['module_file'];
        
        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/market/block_userarea_statistics.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }
        
        $array_data = array(
            'refresh_free' => 0,
            'refresh' => 0,
            'rowactived' => 0,
            'queue_rows' => 0,
            'queue_decline_rows' => 0
        );
        
        if ($array_config['refresh_allow']) {
            if ($array_config['refresh_free']) {
                $array_data['refresh_free'] = nv_count_refresh_free($module);
            }
            $array_data['refresh'] = nv_count_refresh($module);
        }
        
        $result = $db->query('SELECT id, is_queue FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows WHERE userid=' . $user_info['userid']);
        while ($_row = $result->fetch()) {
            if ($_row['is_queue'] == 0) {
                $array_data['rowactived'] ++;
            } elseif ($_row['is_queue'] == 1) {
                $array_data['queue_rows'] ++;
            } else {
                $array_data['queue_decline_rows'] ++;
            }
        }
        
        $xtpl = new XTemplate('block_userarea_statistics.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/market');
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('DATA', $array_data);
        $xtpl->assign('MODULE_NAME', $module);
        
        if ($array_config['refresh_allow']) {
            
            if (! empty($array_config['payport'])) {
                $xtpl->parse('main.refresh.buy_refresh');
            }
            
            if ($array_config['refresh_free']) {
                $xtpl->parse('main.refresh.refresh_free');
            }
            
            $xtpl->parse('main.refresh');
        }
        
        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    $content = nv_market_userarea_statistics($block_config);
}
