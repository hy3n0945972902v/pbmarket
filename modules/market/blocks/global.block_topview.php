<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */
if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (!nv_function_exists('nv_news_block_topview')) {

    /**
     * nv_block_config_topview_blocks()
     *
     * @param mixed $module
     * @param mixed $data_block
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_topview_blocks($module, $data_block, $lang_block)
    {
        global $nv_Cache, $site_mods;
        $tooltip_position = array(
            'top' => $lang_block['tooltip_position_top'],
            'bottom' => $lang_block['tooltip_position_bottom'],
            'left' => $lang_block['tooltip_position_left'],
            'right' => $lang_block['tooltip_position_right']
        );
        $html = '';
        $html .= '<div class="form-group">';
        $html .= '	<label class="control-label col-sm-6">' . $lang_block['number_day'] . ':</label>';
        $html .= '	<div class="col-sm-18"><input type="text" name="config_number_day" class="form-control w100" size="5" value="' . $data_block['number_day'] . '"/></div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '	<label class="control-label col-sm-6">' . $lang_block['numrow'] . ':</label>';
        $html .= '	<div class="col-sm-18"><input type="text" name="config_numrow" class="form-control w100" size="5" value="' . $data_block['numrow'] . '"/></div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_block['showtooltip'] . ':</label>';
        $html .= '<div class="col-sm-18">';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-4">';
        $html .= '<div class="checkbox"><label><input type="checkbox" value="1" name="config_showtooltip" ' . ($data_block['showtooltip'] == 1 ? 'checked="checked"' : '') . ' /></label>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-sm-10">';
        $html .= '<div class="input-group margin-bottom-sm">';
        $html .= '<div class="input-group-addon">' . $lang_block['tooltip_position'] . '</div>';
        $html .= '<select name="config_tooltip_position" class="form-control">';

        foreach ($tooltip_position as $key => $value) {
            $html .= '<option value="' . $key . '" ' . ($data_block['tooltip_position'] == $key ? 'selected="selected"' : '') . '>' . $value . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-sm-10">';
        $html .= '<div class="input-group">';
        $html .= '<div class="input-group-addon">' . $lang_block['tooltip_length'] . '</div>';
        $html .= '<input type="text" class="form-control" name="config_tooltip_length" value="' . $data_block['tooltip_length'] . '"/>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_block['nocatid'] . ':</label>';
        $sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_cat ORDER BY sort ASC';
        $list = $nv_Cache->db($sql, '', $module);
        $html .= '<div class="col-sm-18">';
        $html .= '<div style="max-height: 200px; overflow: auto">';
        if (!is_array($data_block['nocatid'])) {
            $data_block['nocatid'] = explode(',', $data_block['nocatid']);
        }
        foreach ($list as $l) {
            if ($l['status'] == 1 or $l['status'] == 2) {
                $xtitle_i = '';

                if ($l['lev'] > 0) {
                    for ($i = 1; $i <= $l['lev']; ++$i) {
                        $xtitle_i .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                }
                $html .= $xtitle_i . '<label><input type="checkbox" name="config_nocatid[]" value="' . $l['catid'] . '" ' . ((in_array($l['catid'], $data_block['nocatid'])) ? ' checked="checked"' : '') . '</input>' . $l['title'] . '</label><br />';
            }
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * nv_block_config_topview_blocks_submit()
     *
     * @param mixed $module
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_topview_blocks_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['number_day'] = $nv_Request->get_int('config_number_day', 'post', 0);
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        $return['config']['showtooltip'] = $nv_Request->get_int('config_showtooltip', 'post', 0);
        $return['config']['tooltip_position'] = $nv_Request->get_string('config_tooltip_position', 'post', 0);
        $return['config']['tooltip_length'] = $nv_Request->get_string('config_tooltip_length', 'post', 0);
        $return['config']['nocatid'] = $nv_Request->get_typed_array('config_nocatid', 'post', 'int', array());
        return $return;
    }

    /**
     * nv_news_block_topview()
     *
     * @param mixed $block_config
     * @param mixed $mod_data
     * @return
     */
    function nv_news_block_topview($block_config, $mod_data)
    {
        global $array_market_cat, $site_mods, $db_slave, $module_config, $global_config, $lang_module;

        $module = $block_config['module'];
        $mod_file = $site_mods[$module]['module_file'];

        $blockwidth = $module_config[$module]['blockwidth'];
        $show_no_image = $module_config[$module]['show_no_image'];
        $addtime = NV_CURRENTTIME - $block_config['number_day'] * 86400;

        $array_block_news = array();
        $db_slave->sqlreset()
            ->select('id, catid, addtime, title, alias, homeimgthumb, homeimgfile, price, price1, pricetype, unitid, countview')
            ->from(NV_PREFIXLANG . '_' . $mod_data . '_rows')
            ->order('countview DESC')
            ->limit($block_config['numrow']);
        if (empty($block_config['nocatid'])) {
            $db_slave->where('status= 1 AND addtime > ' . $addtime);
        } else {
            $db_slave->where('status= 1 AND addtime > ' . $addtime . ' AND catid NOT IN (' . implode(',', $block_config['nocatid']) . ')');
        }

        $result = $db_slave->query($db_slave->sql());
        while (list ($id, $catid, $addtime, $title, $alias, $homeimgthumb, $homeimgfile, $price, $price1, $pricetype, $unitid, $countview) = $result->fetch(3)) {
            $array_block_news[] = array(
                'id' => $id,
                'title' => $title,
                'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $array_market_cat[$catid]['alias'] . '/' . $alias . '-' . $id . $global_config['rewrite_exturl'],
                'imgurl' => nv_market_get_thumb($homeimgfile, $homeimgthumb, $site_mods[$module]['module_upload']),
                'width' => $blockwidth,
                'addtime' => nv_get_timeago($addtime),
                'price' => nv_get_price($price, $price1, $catid, $pricetype, $unitid),
                'countview' => $countview
            );
        }

        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $mod_file . '/block_topview.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }

        $xtpl = new XTemplate('block_topview.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file);
        $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
        $xtpl->assign('TEMPLATE', $block_theme);
        $xtpl->assign('LANG', $lang_module);

        foreach ($array_block_news as $array_news) {

            $xtpl->assign('blocknews', $array_news);

            if (!empty($array_news['imgurl'])) {
                $xtpl->parse('main.newloop.imgblock');
            }

            if (!$block_config['showtooltip']) {
                $xtpl->assign('TITLE', 'title="' . $array_news['title'] . '"');
            }

            $xtpl->parse('main.newloop');
        }

        if ($block_config['showtooltip']) {
            $xtpl->assign('TOOLTIP_POSITION', $block_config['tooltip_position']);
            $xtpl->parse('main.tooltip');
        }

        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    global $nv_Cache, $site_mods, $module_name, $array_market_cat, $array_market_cat;
    $module = $block_config['module'];
    if (isset($site_mods[$module])) {
        $mod_data = $site_mods[$module]['module_data'];
        if ($module == $module_name) {
            $array_market_cat = $array_market_cat;
            unset($array_market_cat[0]);
        } else {
            $array_market_cat = array();
            $sql = 'SELECT catid, parentid, title, alias, viewcat, subcatid, numlinks, description, keywords, groups_view, status FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_cat ORDER BY sort ASC';
            $list = $nv_Cache->db($sql, 'catid', $module);
            if (!empty($list)) {
                foreach ($list as $l) {
                    $array_market_cat[$l['catid']] = $l;
                    $array_market_cat[$l['catid']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
                }
            }
        }

        $content = nv_news_block_topview($block_config, $mod_data);
    }
}