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

if (!nv_function_exists('nv_block_count_groups')) {

    function nv_block_config_count_groups($module, $data_block, $lang_block)
    {
        global $db_config, $nv_Cache, $site_mods;

        $html_input = '';



        $html = '';


        return $html;
    }

    function nv_block_config_count_groups_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['blockid'] = $nv_Request->get_int('config_blockid', 'post', 0);
        $return['config']['template'] = $nv_Request->get_int('config_template', 'post', 0);
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        $return['config']['title_lenght'] = $nv_Request->get_int('config_title_lenght', 'post', 0);
        return $return;
    }

    function nv_block_count_groups($block_config)
    {
        global $db_config, $lang_module, $module_array_cat, $array_market_cat, $module_info, $site_mods, $module_config, $global_config, $nv_Cache, $db, $module_name, $module_data, $module_file, $module_upload, $my_head;

        $module = $block_config['module'];
        $mod_data = $site_mods[$module]['module_data'];
        $mod_file = $site_mods[$module]['module_file'];
        $mod_upload = $site_mods[$module]['module_upload'];

        $array_config = $module_config[$module];

        if ($module != $module_name) {
            $array_market_cat = $module_array_cat;

            $module_name_tmp = $module_name;
            $module_data_tmp = $module_data;
            $module_file_tmp = $module_file;
            $module_upload_tmp = $module_upload;

            $module_name = $module;
            $module_data = $mod_data;
            $module_file = $mod_file;
            $module_upload = $mod_upload;

            require_once NV_ROOTDIR . '/modules/' . $mod_file . '/language/' . NV_LANG_INTERFACE . '.php';
            require_once NV_ROOTDIR . '/modules/' . $mod_file . '/site.functions.php';

            $module_name = $module_name_tmp;
            $module_data = $module_data_tmp;
            $module_file = $module_file_tmp;
            $module_upload = $module_upload_tmp;
        }


        $rows = $db->query('SELECT COUNT(*) as num_market FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_rows')->fetchColumn();
        $member = $db->query('SELECT COUNT(*) as num_member FROM ' . NV_USERS_GLOBALTABLE)->fetchColumn();




            if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/market/block_count_groups.tpl')) {
                $block_theme = $global_config['module_theme'];
            } elseif (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/market/block_count_groups.tpl')) {
                $block_theme = $global_config['site_theme'];
            } else {
                $block_theme = 'default';
            }

            $xtpl = new XTemplate('block_count_groups.tpl',  NV_ROOTDIR . '/themes/' . $block_theme . '/modules/market');
            $xtpl->assign('LANG', $lang_module);
            $xtpl->assign('ROW_POST', $rows);
            $xtpl->assign('NUM_MEMBER', $member);
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
            if (!empty($list)) {
                foreach ($list as $l) {
                    $module_array_cat[$l['id']] = $l;
                    $module_array_cat[$l['id']]['link'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module . "&amp;" . NV_OP_VARIABLE . "=" . $l['alias'];
                }
            }
        }
        $content = nv_block_count_groups($block_config);
    }
}
