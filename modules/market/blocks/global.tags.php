<?php
/**
 * @Author Kenny Nguyen (nguyentiendat713@gmail.com)
 * @License GNU/GPL version 2 or any later version
 * @Createdate Jan 28, 2015, 04:00:00 PM
 */
if (! defined('NV_MAINFILE'))
    die('Stop!!!');

if (! nv_function_exists('nv_market_tags')) {

    function nv_block_config_tags($module, $data_block, $lang_block)
    {
        global $site_mods;
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['numrow'] . '</td>';
        $html .= '<td><input type="text" class="form-control w200" name="config_numrow" size="5" value="' . $data_block['numrow'] . '"/></td>';
        $html .= '</tr>';
        
        $html .= "<tr>";
        $html .= "<td>" . $lang_block['tagsort'] . "</td>";
        $html .= "<td>";
        $sorting_array = array(
            'numnews' => 'Số lượng Tags',
            'keywords' => 'Xếp A->Z'
        );
        $html .= '<select name="config_tagsort">';
        foreach ($sorting_array as $key => $value) {
            $html .= '<option value="' . $key . '" ' . ($data_block['tagsort'] == $key ? 'selected="selected"' : '') . '>' . $value . '</option>';
        }
        $html .= '</select>';
        $html .= "</td";
        $html .= "	</tr>";
        
        $html .= "<tr>";
        $html .= "<td>" . $lang_block['tagsort_type'] . "</td>";
        $html .= "<td>";
        $sorting_array1 = array(
            'DESC' => 'Giảm dần',
            'ASC' => 'Tăng dần'
        );
        $html .= '<select name="config_tagsort_type">';
        foreach ($sorting_array1 as $key1 => $value1) {
            $html .= '<option value="' . $key1 . '" ' . ($data_block['tagsort_type'] == $key1 ? 'selected="selected"' : '') . '>' . $value1 . '</option>';
        }
        $html .= '</select>';
        $html .= "</td";
        $html .= "	</tr>";
        
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['excluded'] . '</td>';
        $html .= '<td><input type="text" class="form-control w200" name="config_excluded" size="50" value="' . $data_block['excluded'] . '"/></td>';
        $html .= '</tr>';
        
        $html .= "<tr>";
        $html .= "  <td>" . $lang_block['newslinks'] . "</td>";
        $newslinks = ($data_block['newslinks'] == true) ? 'checked="checked"' : '';
        $html .= "  <td><input type=\"checkbox\" name=\"config_newslinks\" value=\"true\" " . $newslinks . " \></td>";
        $html .= "</tr>";
        return $html;
    }

    function nv_block_config_tags_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        $return['config']['newslinks'] = $nv_Request->get_title('config_newslinks', 'post');
        $return['config']['tagsort'] = $nv_Request->get_string('config_tagsort', 'post', 0);
        $return['config']['excluded'] = $nv_Request->get_string('config_excluded', 'post', 0);
        $return['config']['tagsort_type'] = $nv_Request->get_string('config_tagsort_type', 'post', 0);
        return $return;
    }

    function nv_market_tags($block_config)
    {
        global $site_mods, $module_array_cat, $global_config, $lang_module, $db, $module_config, $module_info, $nv_Cache;
        $module = $block_config['module'];
        $mod_data = $site_mods[$module]['module_data'];
        $mod_file = $site_mods[$module]['module_file'];
        
        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $mod_file . '/block_news_real.tpl')) {
            $block_theme = $global_config['site_theme'];
        } else {
            $block_theme = 'default';
        }
        
        $xtpl = new XTemplate('block.tags.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('TEMPLATE', $block_theme);
        
        $excluded = $block_config['excluded'];
        if (! empty($excluded)) {
            $sql = 'SELECT tid, numnews, alias, keywords FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_tags WHERE tid NOT IN (' . $excluded . ') ORDER BY ' . $block_config['tagsort'] . ' ' . $block_config['tagsort_type'] . ' LIMIT 0, ' . $block_config['numrow'];
        } else {
            $sql = 'SELECT tid, numnews, alias, keywords FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_tags ORDER BY ' . $block_config['tagsort'] . ' ' . $block_config['tagsort_type'] . ' LIMIT 0, ' . $block_config['numrow'];
        }
        $list = $nv_Cache->db($sql, 'id', $module);
        
        if (! empty($list)) {
            foreach ($list as $loop) {
                $loop['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=tag/' . $loop['alias'];
                $loop['title'] = $loop['keywords'];
                $loop1['num_news'] = $loop['numnews'];
                if (! empty($block_config['newslinks'])) {
                    $xtpl->assign('OPEN', '(');
                    $xtpl->assign('LOOP1', $loop1);
                    $xtpl->assign('CLOSE', ')');
                }
                
                $xtpl->assign('LOOP', $loop);
                $xtpl->parse('main.loop');
                $xtpl->parse('main.loop1');
            }
        }
        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}
if (defined('NV_SYSTEM')) {
    $content = nv_market_tags($block_config);
}
