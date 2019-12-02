<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */
if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (!nv_function_exists('nv_block_market_button')) {

    function nv_block_market_config_button($module, $data_block, $lang_block)
    {
        global $site_mods;

        $html_input = '';
        $html = '';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['text'] . '</td>';
        $html .= '<td><input type="text" name="config_text" value="' . $data_block['text'] . '" class="form-control w200" /></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['popup'] . '</td>';
        $html .= '<td><input type="checkbox" name="config_popup" value="1" ' . ($data_block['popup'] ? 'checked="checked"' : '') . ' /></td>';
        $html .= '</tr>';

        return $html;
    }

    function nv_block_market_config_button_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['text'] = $nv_Request->get_title('config_text', 'type', '');
        $return['config']['popup'] = $nv_Request->get_int('config_popup', 'type', 0);
        return $return;
    }

    function nv_block_market_button($block_config)
    {
        global $module_info, $site_mods, $module_config, $global_config, $lang_module, $module_name, $my_head, $array_config;

        $module = $block_config['module'];

        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/market/block_button.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }

        if ($module != $module_name) {
            $my_head .= '<link rel="StyleSheet" href="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/css/market.css' . '">';
            require_once NV_ROOTDIR . '/modules/' . $site_mods[$module]['module_file'] . '/language/' . NV_LANG_INTERFACE . '.php';
            $array_config = $module_config[$module];
        }

        $xtpl = new XTemplate('block_button.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/market');
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('DATA', $block_config);
        $xtpl->assign('CONTENT', $site_mods[$module]['alias']['content']);
        $xtpl->assign('URL_CONTENT', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module]['alias']['content'] . ($block_config['popup'] ? '&amp;ispopup=1' : ''));
        $xtpl->assign('URL_USERAREA', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module]['alias']['userarea']);
        $xtpl->assign('URL_SAVED', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module]['alias']['saved']);

        if (!nv_user_in_groups($array_config['grouppost'])) {
            $xtpl->parse('main.login');
        } elseif ($block_config['popup']) {
            $xtpl->parse('main.popup_js');
        }

        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    global $site_mods;

    $module = $block_config['module'];
    if (isset($site_mods[$module])) {
        $content = nv_block_market_button($block_config);
    }
}
