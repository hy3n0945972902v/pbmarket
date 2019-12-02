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

if (! nv_function_exists('nv_block_market_post')) {

    function nv_block_market_config_post($module, $data_block, $lang_block)
    {
        global $site_mods, $global_config;

        $array_type = array(
            $lang_block['type0'],
            $lang_block['type1']
        );

        $array_template = array(
            $lang_block['template0']
        )
        // $lang_block['template1']
        ;

        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/market/block_post_config.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }

        $xtpl = new XTemplate('block_post_config.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/market');
        $xtpl->assign('LANG', $lang_block);
        $xtpl->assign('DATA', $data_block);

        foreach ($array_type as $index => $value) {
            $ck = (isset($data_block['type']) and $index == $data_block['type']) ? 'checked="checked"' : '';
            $xtpl->assign('TYPE', array(
                'index' => $index,
                'value' => $value,
                'checked' => $ck
            ));
            $xtpl->parse('main.type');
        }

        foreach ($array_template as $index => $value) {
            $ck = (isset($data_block['template']) and $index == $data_block['template']) ? 'checked="checked"' : '';
            $xtpl->assign('TEMPLATE', array(
                'index' => $index,
                'value' => $value,
                'checked' => $ck
            ));
            $xtpl->parse('main.template');
        }

        $xtpl->parse('main');
        return $xtpl->text('main');
    }

    function nv_block_market_config_post_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['type'] = $nv_Request->get_int('config_type', 'type', 0);
        $return['config']['template'] = $nv_Request->get_int('config_template', 'post', 0);
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        $return['config']['title_lenght'] = $nv_Request->get_int('config_title_lenght', 'post', 0);
        return $return;
    }

    function nv_block_market_post($block_config)
    {
        global $db_config, $array_market_cat, $module_array_cat, $module_info, $site_mods, $module_config, $global_config, $nv_Cache, $db, $array_config, $lang_module, $money_config, $module_name;

        $module = $block_config['module'];
        $mod_data = $site_mods[$module]['module_data'];
        $mod_file = $site_mods[$module]['module_file'];
        $mod_upload = $site_mods[$module]['module_upload'];
        $array_config = $module_config[$module];

        if ($module != $module_name) {
            $array_market_cat = $module_array_cat;
            require_once NV_ROOTDIR . '/modules/' . $mod_file . '/language/' . NV_LANG_INTERFACE . '.php';
            require_once NV_ROOTDIR . '/modules/' . $mod_file . '/site.functions.php';
        }

        $order = 'ordertime DESC';
        $where = 'status=1 AND status_admin=1 AND is_queue=0 AND (exptime=0 OR exptime >= ' . NV_CURRENTTIME . ')';
        if ($block_config['type'] == 1) {
            $order = 'countview DESC';
        }

        $db->sqlreset()
            ->select('id, title, alias, catid, area_p, area_d, typeid, pricetype, price, price1, unitid, homeimgfile, homeimgalt, homeimgthumb, countview, countcomment, groupview, addtime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, groups_config')
            ->from(NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_rows')
            ->where($where)
            ->order($order)
            ->limit($block_config['numrow']);
        $list = $nv_Cache->db($db->sql(), '', $module);

        if (! empty($list)) {
            $template = 'block_post_list.tpl';
            if ($block_config['template'] == 1) {
                $template = 'block_post_list_simple.tpl';
            }

            if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/market/' . $template)) {
                $block_theme = $global_config['module_theme'];
            } else {
                $block_theme = 'default';
            }

            if ($module != $module_name) {
                $my_head .= '<link rel="StyleSheet" href="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/css/market.css' . '">';
            }

            $xtpl = new XTemplate($template, NV_ROOTDIR . '/themes/' . $block_theme . '/modules/market');
            $xtpl->assign('LANG', $lang_module);
            $xtpl->assign('TEMPLATE', $block_theme);
            $home_image_size = explode('x', $array_config['home_image_size']);
            $xtpl->assign('WIDTH', $home_image_size[0]);
            $xtpl->assign('HEIGHT', $home_image_size[1]);

            foreach ($list as $l) {
                if (nv_user_in_groups($l['groupview'])) {
                    if (! empty($data = nv_market_data($l, $module))) {
                        $xtpl->assign('ROW', $data);
                        $xtpl->parse('main.loop');
                    }
                }
            }

            $xtpl->parse('main');
            return $xtpl->text('main');
        }
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
        $content = nv_block_market_post($block_config);
    }
}
