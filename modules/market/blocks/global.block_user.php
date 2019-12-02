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

if (! nv_function_exists('nv_market_user')) {

    function nv_market_user($block_config)
    {
        global $module_info, $lang_module, $global_config, $module_name, $lang_module, $site_mods, $user_info;
        
        if (! defined('NV_IS_USER')) {
            return '';
        }
        
        $module = $block_config['module'];
        $mod_file = $site_mods[$module]['module_file'];
        
        if ($module != $module_name) {
            require_once NV_ROOTDIR . '/modules/' . $mod_file . '/language/' . NV_LANG_DATA . '.php';
        }
        
        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/market/block_user.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }
        
        $xtpl = new XTemplate('block_user.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/market');
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('URL_USERAREA', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module]['alias']['userarea']);
        $xtpl->assign('URL_POST', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module]['alias']['content']);
        
        // nếu là doanh nghiệp
        if ($user_info['typeid'] == 2) {
            $xtpl->assign('URL_COMPANY_CONTENT', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module]['alias']['company-content']);
            $xtpl->parse('main.company');
        }
        
        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    global $site_mods, $module_name, $array_market_cat, $module_array_cat, $nv_Cache;
    
    $module = $block_config['module'];
    
    if (isset($site_mods[$module])) {
        $content = nv_market_user($block_config);
    }
}
