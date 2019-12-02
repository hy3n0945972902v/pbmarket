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

if (!nv_function_exists('nv_block_market_groups')) {

    function nv_block_config_market_groups($module, $data_block, $lang_block)
    {
        global $db_config, $nv_Cache, $site_mods;

        $html_input = '';

        $array_template = array(
            '0' => $lang_block['template_0'],
            '1' => $lang_block['template_1']
        );

        $html = '';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['blockid'] . '</td>';
        $html .= '<td><select name="config_blockid" class="form-control w200">';
        $html .= '<option value="0"> -- </option>';
        $sql = 'SELECT bid, title, alias FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_block_cat ORDER BY weight ASC';
        $list = $nv_Cache->db($sql, '', $module);
        foreach ($list as $l) {
            $html_input .= '<input type="hidden" id="config_blockid_' . $l['bid'] . '" value="' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module]['alias']['groups'] . '/' . $l['alias'] . '" />';
            $html .= '<option value="' . $l['bid'] . '" ' . (($data_block['blockid'] == $l['bid']) ? ' selected="selected"' : '') . '>' . $l['title'] . '</option>';
        }
        $html .= '</select>';
        $html .= $html_input;
        $html .= '<script type="text/javascript">';
        $html .= '	$("select[name=config_blockid]").change(function() {';
        $html .= '		$("input[name=title]").val($("select[name=config_blockid] option:selected").text());';
        $html .= '		$("input[name=link]").val($("#config_blockid_" + $("select[name=config_blockid]").val()).val());';
        $html .= '	});';
        $html .= '</script>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['template'] . '</td>';
        $html .= '<td><select class="form-control w200" name="config_template">';
        foreach ($array_template as $index => $value) {
            $sl = $index == $data_block['template'] ? 'selected="selected"' : '';
            $html .= '<option value="' . $index . '" ' . $sl . ' >' . $value . '</option>';
        }
        $html .= '</select></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['numrow'] . '</td>';
        $html .= '<td><input type="text" class="form-control w200" name="config_numrow" size="5" value="' . $data_block['numrow'] . '"/></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['title_lenght'] . '</td>';
        $html .= '<td><input type="text" class="form-control w200" name="config_title_lenght" size="5" value="' . $data_block['title_lenght'] . '"/></td>';
        $html .= '</tr>';
        return $html;
    }

    function nv_block_config_market_groups_submit($module, $lang_block)
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

    function nv_block_market_groups($block_config)
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

        $db->sqlreset()
            ->select('t1.id, title, alias, catid, area_p, area_d, typeid, pricetype, price, price1, unitid, homeimgfile, homeimgalt, homeimgthumb, countview, countcomment, groupview, addtime, auction, auction_begin, auction_end, auction_price_begin, auction_price_step, groups_config')
            ->from(NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_rows t1')
            ->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_block t2 ON t1.id = t2.id')
            ->where('t2.bid= ' . $block_config['blockid'] . ' AND (t2.exptime = 0 OR t2.exptime >= ' . NV_CURRENTTIME . ') AND t1.status=1 AND t1.status_admin=1 AND t1.is_queue=0 AND (t1.exptime=0 OR t1.exptime >= ' . NV_CURRENTTIME . ')')
            ->order('t1.ordertime DESC')
            ->limit($block_config['numrow']);

        $list = $nv_Cache->db($db->sql(), '', $module);

        if (!empty($list)) {
            $template = 'block_post_list.tpl';
            if ($block_config['template'] == 1) {
                $template = 'block_post_title.tpl';
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
                    if (!empty($data = nv_market_data($l, $module))) {
                        $xtpl->assign('ROW', $data);

                        if (!empty($data['color'])) {
                            $xtpl->parse('main.loop.color');
                        }

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
            if (!empty($list)) {
                foreach ($list as $l) {
                    $module_array_cat[$l['id']] = $l;
                    $module_array_cat[$l['id']]['link'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module . "&amp;" . NV_OP_VARIABLE . "=" . $l['alias'];
                }
            }
        }
        $content = nv_block_market_groups($block_config);
    }
}
