<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */
if (!defined('NV_MAINFILE')) die('Stop!!!');

if (!nv_function_exists('nv_market_block_search')) {

    function nv_block_config_market_search_blocks($module, $data_block, $lang_block)
    {
        global $site_mods;
        $html = '';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['search_template'] . '</td>';
        $html .= '<td>';
        $html .= "<select name=\"config_search_template\" class=\"form-control w200\">\n";

        $sl = (isset($data_block['search_template']) and $data_block['search_template'] == 'vertical') ? 'selected="selected"' : '';
        $html .= "<option value=\"vertical\" " . $sl . " >" . $lang_block['search_template_vertical'] . "</option>\n";
        $sl = (isset($data_block['search_template']) and $data_block['search_template'] == 'horizontal') ? 'selected="selected"' : '';
        $html .= "<option value=\"horizontal\" " . $sl . " >" . $lang_block['search_template_horizontal'] . "</option>\n";
        $sl = (isset($data_block['search_template']) and $data_block['search_template'] == 'horizontal_line') ? 'selected="selected"' : '';
        $html .= "<option value=\"horizontal_line\" " . $sl . " >" . $lang_block['search_template_horizontal_line'] . "</option>\n";
        $html .= "</select>\n";
        $html .= '</td>';
        $html .= '</tr>';

        return $html;
    }

    function nv_block_config_market_search_blocks_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['search_template'] = $nv_Request->get_title('config_search_template', 'post', 'vertical');
        return $return;
    }

    function nv_market_block_search($block_config)
    {
        global $module_array_cat, $site_mods, $module_info, $db, $module_config, $global_config, $module_name, $db_config, $nv_Request, $my_head, $op, $lang_module, $module_array_type, $catid, $array_search_params;

        $module = $block_config['module'];
        $mod_file = $site_mods[$module]['module_file'];

        $tplfile = 'block_search_vertical.tpl';
        if ($block_config['search_template'] == 'horizontal') {
            $tplfile = 'block_search_horizontal.tpl';
        } elseif ($block_config['search_template'] == 'list') {
            $tplfile = 'block_search_list.tpl';
        } elseif ($block_config['search_template'] == 'horizontal_line') {
            $tplfile = 'block_search_horizontal_line.tpl';
        }

        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $mod_file . '/' . $tplfile)) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }

        if ($module != $module_name) {
            $my_head .= '<link rel="StyleSheet" href="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/css/' . $site_mods[$module]['module_file'] . '.css">';
            include NV_ROOTDIR . '/modules/' . $site_mods[$module]['module_file'] . '/language/' . NV_LANG_INTERFACE . '.php';
        }

        $array_search = array(
            'q' => $nv_Request->get_title('q', 'post,get', ''),
            'catid' => $nv_Request->get_int('catid', 'post,get', ($module_name == $module and in_array($op, array(
                'viewcat',
                'detail'
            ))) ? $catid : $array_search_params['catid']),
            'area_p' => $nv_Request->get_int('area_p', 'post,get', $array_search_params['provinceid']),
            'area_d' => $nv_Request->get_int('area_d', 'post,get', $array_search_params['districtid']),
            'type' => $nv_Request->get_int('type', 'post,get', $array_search_params['typeid'])
        );

        if (!$global_config['rewrite_enable']) {
            $array_search['action'] = NV_BASE_SITEURL . 'index.php';
        } else {
            $array_search['action'] = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=search', true);
        }

        $xtpl = new XTemplate($tplfile, NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('TEMPLATE', $block_theme);
        $xtpl->assign('MODULE_NAME', $module);
        $xtpl->assign('SEARCH', $array_search);
        $xtpl->assign('FIRST_TYPE', reset($module_array_type));

        if (!empty($module_array_cat)) {
            foreach ($module_array_cat as $catid => $value) {
                $value['space'] = '';
                if ($value['lev'] > 0) {
                    for ($i = 1; $i <= $value['lev']; $i++) {
                        $value['space'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                }
                $value['selected'] = $catid == $array_search['catid'] ? ' selected="selected"' : '';

                $xtpl->assign('CAT', $value);
                $xtpl->parse('main.cat');
            }
        }

        require_once NV_ROOTDIR . '/modules/location/location.class.php';
        $location = new Location();
        $location->set('SelectCountryid', $module_config[$module]['countryid']);
        $location->set('IsDistrict', 1);
        $location->set('BlankTitleProvince', 1);
        $location->set('BlankTitleDistrict', 1);
        $location->set('NameProvince', 'area_p');
        $location->set('NameDistrict', 'area_d');
        $location->set('SelectProvinceid', $array_search['area_p']);
        $location->set('SelectDistrictid', $array_search['area_d']);
        $xtpl->assign('LOCATION', $location->buildInput());

        if (!empty($module_array_type)) {
            foreach ($module_array_type as $type) {
                $type['selected'] = $array_search['type'] == $type['id'] ? 'selected="selected"' : '';
                $xtpl->assign('TYPE', $type);
                $xtpl->parse('main.type');
            }
        }

        if (!defined('SELECT2')) {
            $xtpl->parse('main.select2');
        }

        if (!$global_config['rewrite_enable']) {
            $xtpl->parse('main.no_rewrite');
        }

        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    global $db_config, $site_mods, $module_name, $array_market_cat, $module_array_cat, $nv_Cache, $array_type, $module_array_type;

    $module = $block_config['module'];

    if (isset($site_mods[$module])) {
        $mod_data = $site_mods[$module]['module_data'];
        if ($module == $module_name) {
            $module_array_cat = $array_market_cat;
            $module_array_type = $array_type;
        } else {
            $module_array_cat = array();
            $_sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_cat WHERE status=1 ORDER BY sort ASC';
            $list = $nv_Cache->db($_sql, 'id', $module);
            foreach ($list as $l) {
                $module_array_cat[$l['id']] = $l;
                $module_array_cat[$l['id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
            }

            $_sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_type WHERE status=1 ORDER BY weight';
            $module_array_type = $nv_Cache->db($_sql, 'id', $module);
        }
        $content = nv_market_block_search($block_config);
    }
}