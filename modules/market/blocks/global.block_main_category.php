<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */
if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (! nv_function_exists('nv_block_main_category')) {

    function nv_block_config_main_category($module, $data_block, $lang_block)
    {
        global $db_config, $nv_Cache, $site_mods;
        
        $html = '';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['title_lenght'] . '</td>';
        $html .= '<td><input type="text" class="form-control w200" name="config_title_lenght" size="5" value="' . $data_block['title_lenght'] . '"/></td>';
        $html .= '</tr>';
        return $html;
    }

    function nv_block_config_main_category_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['title_lenght'] = $nv_Request->get_int('config_title_lenght', 'post', 0);
        return $return;
    }

    function nv_block_main_category($block_config)
    {
        global $db_config, $lang_module, $module_array_cat, $module_info, $site_mods, $module_config, $global_config, $nv_Cache, $db, $module_name;
        
        $module = $block_config['module'];
        $mod_upload = $site_mods[$module]['module_upload'];
        
        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/market/block_main_category.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }
        
        $xtpl = new XTemplate('block_main_category.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/market');
        $xtpl->assign('LANG', $lang_module);
        
        if (! empty($module_array_cat)) {
            foreach ($module_array_cat as $catid => $cat) {
                if ($cat['parentid'] == 0 and $cat['inhome']) {
                    if (! empty($cat['image']) and file_exists(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $mod_upload . '/' . $cat['image'])) {
                        $cat['image'] = NV_BASE_SITEURL . NV_ASSETS_DIR . '/' . $mod_upload . '/' . $cat['image'];
                    } elseif (! empty($cat['image']) and file_exists(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $cat['image'])) {
                        $cat['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $cat['image'];
                    } else {
                        $cat['image'] = NV_BASE_SITEURL . 'themes/' . $block_theme . '/images/market/nopicture.jpg';
                    }
                    $xtpl->assign('CAT', $cat);
                    $xtpl->parse('main.cat');
                }
            }
        }
        
        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    global $site_mods, $module_name, $array_market_cat, $module_array_cat, $nv_Cache, $db;
    
    $module = $block_config['module'];
    
    if (isset($site_mods[$module])) {
        if ($module == $module_name) {
            $module_array_cat = $array_market_cat;
        } else {
            $module_array_cat = array();
            $sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_cat WHERE status=1 ORDER BY sort';
            $list = $nv_Cache->db($sql, 'id', $module);
            if (! empty($list)) {
                foreach ($list as $l) {
                    $module_array_cat[$l['id']] = $l;
                    $module_array_cat[$l['id']]['link'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module . "&amp;" . NV_OP_VARIABLE . "=" . $l['alias'];
                }
            }
        }
        $content = nv_block_main_category($block_config);
    }
}
