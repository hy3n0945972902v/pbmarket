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

if (! nv_function_exists('nv_market_freelance')) {

    function nv_market_freelance($block_config)
    {
        global $db, $module_info, $lang_module, $global_config, $module_name, $module_data, $lang_module, $user_info, $site_mods, $array_config;
        
        if (! defined('NV_IS_USER') or ! in_array($array_config['freelancegroup'], $user_info['in_groups'])) {
            return '';
        }
        
        $module = $block_config['module'];
        $mod_file = $site_mods[$module]['module_file'];
        
        $array_data = array();
        list ($array_data['total'], $array_data['salary'], $array_data['pay']) = $db->query('SELECT total, salary, pay FROM ' . NV_PREFIXLANG . '_' . $module_data . '_freelance WHERE userid=' . $user_info['userid'])->fetch(3);
        $array_data['rest'] = $array_data['total'] - $array_data['pay'];
        $array_data = array_map('nv_market_number_format', $array_data);
        
        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/market/block_freelance.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }
        
        $xtpl = new XTemplate('block_freelance.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/market');
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('DATA', $array_data);
        $xtpl->assign('MONEY_UNIT', $array_config['money_unit']);
        
        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    $content = nv_market_freelance($block_config);
}
