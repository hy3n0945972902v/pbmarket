<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) die('Stop!!!');

define('NV_IS_FILE_ADMIN', true);
require_once NV_ROOTDIR . '/modules/' . $module_file . '/site.functions.php';
require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';

$allow_func = array(
    'main',
    'config',
    'cat',
    'unit',
    'type',
    'content',
    'queue',
    'change_cat',
    'block',
    'chang_block_cat',
    'change_block',
    'list_block_cat',
    'list_block',
    'groups',
    'econtent',
    'tags',
    'ajax',
    'reason',
    'freelance',
    'news',
    'fields',
    'template',
    'detemplate'
);

if ($array_config['auction']) {
    $allow_func[] = 'auction';
} else {
    unset($submenu['auction']);
}

/**
 * nv_fix_order()
 *
 * @param integer $parentid
 * @param integer $order
 * @param integer $lev
 * @return
 *
 */
function nv_fix_order($table_name, $parentid = 0, $sort = 0, $lev = 0)
{
    global $db, $db_config, $module_data;

    $sql = 'SELECT id, parentid FROM ' . $table_name . ' WHERE parentid=' . $parentid . ' ORDER BY weight ASC';
    $result = $db->query($sql);
    $array_order = array();
    while ($row = $result->fetch()) {
        $array_order[] = $row['id'];
    }
    $result->closeCursor();
    $weight = 0;
    if ($parentid > 0) {
        ++$lev;
    } else {
        $lev = 0;
    }
    foreach ($array_order as $order_i) {
        ++$sort;
        ++$weight;

        $sql = 'UPDATE ' . $table_name . ' SET weight=' . $weight . ', sort=' . $sort . ', lev=' . $lev . ' WHERE id=' . $order_i;
        $db->query($sql);

        $sort = nv_fix_order($table_name, $order_i, $sort, $lev);
    }

    $numsub = $weight;

    if ($parentid > 0) {
        $sql = "UPDATE " . $table_name . " SET numsub=" . $numsub;
        if ($numsub == 0) {
            $sql .= ",subid=''";
        } else {
            $sql .= ",subid='" . implode(",", $array_order) . "'";
        }
        $sql .= " WHERE id=" . intval($parentid);
        $db->query($sql);
    }
    return $sort;
}

/**
 * nv_show_groups_list()
 *
 * @return
 *
 */
function nv_show_groups_list()
{
    global $db, $db_config, $lang_module, $lang_global, $module_name, $module_data, $op, $module_file, $global_config, $module_info;

    $sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block_cat ORDER BY weight ASC';
    $_array_block_cat = $db->query($sql)->fetchAll();
    $num = sizeof($_array_block_cat);

    if ($num > 0) {
        $array_useradd = $array_adddefault = array(
            $lang_global['no'],
            $lang_global['yes']
        );

        $xtpl = new XTemplate('blockcat_lists.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('GLANG', $lang_global);

        foreach ($_array_block_cat as $row) {
            $numnews = $db->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block where bid=' . $row['bid'])->fetchColumn();

            $xtpl->assign('ROW', array(
                'bid' => $row['bid'],
                'title' => $row['title'],
                'numnews' => $numnews,
                'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=block&amp;bid=' . $row['bid'],
                'linksite' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['groups'] . '/' . $row['alias'],
                'url_edit' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;bid=' . $row['bid'] . '#edit'
            ));

            for ($i = 1; $i <= $num; ++$i) {
                $xtpl->assign('WEIGHT', array(
                    'key' => $i,
                    'title' => $i,
                    'selected' => $i == $row['weight'] ? ' selected="selected"' : ''
                ));
                $xtpl->parse('main.loop.weight');
            }

            foreach ($array_useradd as $key => $val) {
                $xtpl->assign('USERADD', array(
                    'key' => $key,
                    'title' => $val,
                    'selected' => $key == $row['useradd'] ? ' selected="selected"' : ''
                ));
                $xtpl->parse('main.loop.useradd');
            }

            foreach ($array_adddefault as $key => $val) {
                $xtpl->assign('ADDDEFAULT', array(
                    'key' => $key,
                    'title' => $val,
                    'selected' => $key == $row['adddefault'] ? ' selected="selected"' : ''
                ));
                $xtpl->parse('main.loop.adddefault');
            }

            for ($i = 1; $i <= 30; ++$i) {
                $xtpl->assign('NUMBER', array(
                    'key' => $i,
                    'title' => $i,
                    'selected' => $i == $row['numbers'] ? ' selected="selected"' : ''
                ));
                $xtpl->parse('main.loop.number');
            }

            for ($i = 0; $i <= $num; ++$i) {
                $xtpl->assign('PRIOR', array(
                    'key' => $i,
                    'title' => $i,
                    'selected' => $i == $row['prior'] ? ' selected="selected"' : ''
                ));
                $xtpl->parse('main.loop.prior');
            }

            $xtpl->parse('main.loop');
        }

        $xtpl->parse('main');
        $contents = $xtpl->text('main');
    } else {
        $contents = '&nbsp;';
    }

    return $contents;
}

/**
 * nv_fix_block_cat()
 *
 * @return
 *
 */
function nv_fix_block_cat()
{
    global $db, $db_config, $module_data;

    $sql = 'SELECT bid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_block_cat ORDER BY weight ASC';
    $weight = 0;
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        ++$weight;
        $sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_block_cat SET weight=' . $weight . ' WHERE bid=' . intval($row['bid']);
        $db->query($sql);
    }
    $result->closeCursor();
}

/**
 * nv_show_block_list()
 *
 * @param mixed $bid
 * @return
 *
 */
function nv_show_block_list($bid)
{
    global $db, $db_config, $lang_module, $lang_global, $module_name, $module_data, $op, $module_file, $global_config, $array_cat;

    $xtpl = new XTemplate('block_list.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('MODULE_NAME', $module_name);
    $xtpl->assign('OP', $op);
    $xtpl->assign('BID', $bid);

    $array_jobs[0] = array(
        'alias' => 'Other'
    );

    $sql = 'SELECT t1.id, t1.catid, title, alias, t2.weight FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows t1 INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_block t2 ON t1.id = t2.id WHERE t2.bid= ' . $bid . ' AND t1.status=1 ORDER BY t2.weight ASC';
    $array_block = $db->query($sql)->fetchAll();

    $num = sizeof($array_block);
    if ($num > 0) {
        foreach ($array_block as $row) {
            $xtpl->assign('ROW', array(
                'id' => $row['id'],
                'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$row['catid']]['alias'] . '/' . $row['alias'] . '-' . $row['id'],
                'title' => $row['title']
            ));

            for ($i = 1; $i <= $num; ++$i) {
                $xtpl->assign('WEIGHT', array(
                    'key' => $i,
                    'title' => $i,
                    'selected' => $i == $row['weight'] ? ' selected="selected"' : ''
                ));
                $xtpl->parse('main.loop.weight');
            }

            $xtpl->parse('main.loop');
        }

        $xtpl->parse('main');
        $contents = $xtpl->text('main');
    } else {
        $contents = '&nbsp;';
    }

    return $contents;
}

function nv_sendmail_queue($queue_info)
{
    global $global_config, $module_file;

    $xtpl = new XTemplate('template_queue_' . NV_LANG_DATA . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
    $xtpl->assign('DATA', $queue_info);

    if ($queue_info['queue'] == 1) {
        // dong y
        $xtpl->parse('main.link');
    } elseif ($queue_info['queue'] == 2) {
        // tu choi
        if (!empty($queue_info['reason'])) {
            $xtpl->parse('main.reason');
        }

        if (!empty($queue_info['reason_note'])) {
            $xtpl->parse('main.reason_note');
        }

        $xtpl->parse('main.reason_con');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

function nv_queue_edit_accept($rowsid)
{
    global $db, $module_name, $module_data, $nv_Cache;

    $count = $db->exec('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_queue_edit WHERE rowsid=' . $rowsid);
    if ($count) {
        $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET is_queue_edit=0 WHERE id=' . $rowsid);
    }

    $nv_Cache->delMod($module_name);
}

/**
 * nv_get_data_type()
 *
 * @param mixed $dataform
 * @return
 */
function nv_get_data_type($dataform)
{
    $type_date = '';
    if ($dataform['field_type'] == 'number') {
        $type_date = "DOUBLE NOT NULL DEFAULT '" . $dataform['default_value'] . "'";
    } elseif ($dataform['field_type'] == 'date') {
        $type_date = "INT(11) NOT NULL DEFAULT '0'";
    } elseif ($dataform['max_length'] <= 255) {
        $type_date = "VARCHAR( " . $dataform['max_length'] . " ) NOT NULL DEFAULT ''";
    } elseif ($dataform['max_length'] <= 65536) {
        //2^16 TEXT

        $type_date = 'TEXT NOT NULL';
    } elseif ($dataform['max_length'] <= 16777216) {
        //2^24 MEDIUMTEXT

        $type_date = 'MEDIUMTEXT NOT NULL';
    } elseif ($dataform['max_length'] <= 4294967296) {
        //2^32 LONGTEXT

        $type_date = 'LONGTEXT NOT NULL';
    }

    return $type_date;
}

/**
 * nv_create_form_file()
 *
 * @param mixed $array_template_id
 * @return
 */
function nv_create_form_file($array_template_id)
{
    global $db, $db_config, $module_upload, $module_data, $module_file, $array_template, $lang_module;

    foreach ($array_template_id as $templateids_i) {
        $array_views = array();
        $result = $db->query("SELECT fid, field, field_type, listtemplate FROM " . NV_PREFIXLANG . '_' . $module_data . "_field");
        while ($column = $result->fetch()) {
            $column['listtemplate'] = explode(',', $column['listtemplate']);
            if (in_array($templateids_i, $column['listtemplate'])) {
                $array_views[$column['fid']] = $column;
            }
        }

        $array_field_js = array();
        $content_2 = "<!-- BEGIN: main -->\n";
        $content_2 .= "\t<div class=\"panel panel-default\">\n\t\t<div class=\"panel-heading\">{LANG.other_info}</div>\n";
        $content_2 .= "\t\t<div class=\"panel-body\">\n";

        foreach ($array_views as $key => $column) {
            $content_2 .= "\t\t\t<div class=\"form-group\">\n";
            $content_2 .= "\t\t\t\t<label class=\"col-md-4 control-label\"> {CUSTOM_LANG." . $key . ".title} </label>\n";

            $content_2 .= "\t\t\t\t<div class=\"col-md-20\">";

            if ($column['field_type'] == 'time') {
                $content_2 .= "<input class=\"form-control\" type=\"text\" pattern=\"^[0-9]{2,2}\$\" name=\"custom[" . $key . "_hour]\" value=\"{ROW." . $column['field'] . "_hour}\" >:";
                $content_2 .= "<input class=\"form-control\" type=\"text\" pattern=\"^[0-9]{2,2}\$\" name=\"custom[" . $key . "_min]\" value=\"{ROW." . $column['field'] . "_min}\" >&nbsp;";
            }

            if ($column['field_type'] == 'textarea') {
                $content_2 .= "<textarea class=\"form-control\" style=\"width: 98%; height:100px;\" cols=\"75\" rows=\"5\" name=\"custom[" . $key . "]\">{ROW." . $key . "}</textarea>";
            } elseif ($column['field_type'] == 'editor') {
                $content_2 .= "{ROW." . $column['field'] . "}";
            } elseif ($column['field_type'] == 'select') {
                $content_2 .= "<select class=\"form-control\" name=\"custom[" . $key . "]\">\n";
                $content_2 .= "\t\t\t\t\t\t\t<option value=\"\"> --- </option>\n";
                $content_2 .= "\t\t\t\t\t\t<!-- BEGIN: select_" . $key . " -->\n";
                $content_2 .= "\t\t\t\t\t\t\t<option value=\"{OPTION.key}\" {OPTION.selected}>{OPTION.title}</option>\n";
                $content_2 .= "\t\t\t\t\t\t\t<!-- END: select_" . $key . " -->\n";
                $content_2 .= "\t\t\t\t\t</select>";
            } elseif ($column['field_type'] == 'radio' or $column['field_type'] == 'checkbox') {
                $type_html = ($column['field_type'] == 'radio') ? 'radio' : 'checkbox';
                $content_2 .= "\n\t\t\t\t\t<!-- BEGIN: " . $type_html . "_" . $key . " -->\n";
                $content_2 .= "\t\t\t\t\t<label><input class=\"form-control\" type=\"" . $type_html . "\" name=\"custom[" . $key . "]\" value=\"{OPTION.key}\" {OPTION.checked}";

                if (isset($array_requireds[$key])) {
                    $content_2 .= 'required="required" ';
                    if ($oninvalid) {
                        $content_2 .= "oninvalid=\"setCustomValidity( nv_required )\" oninput=\"setCustomValidity('')\" ";
                    }
                }
                $content_2 .= ">{OPTION.title} &nbsp;</label>\n";
                $content_2 .= "\t\t\t\t\t<!-- END: " . $type_html . "_" . $key . " -->\n";
                $content_2 .= "\t\t\t\t";
            } elseif ($column['field_type'] == 'multiselect') {
                $content_2 .= "\n\t\t\t\t\t<select class=\"form-control\" name=\"custom[" . $key . "][]\" multiple=\"multiple\" >\n";
                $content_2 .= "\t\t\t\t\t\t\t<option value=\"\"> --- </option>\n";
                $content_2 .= "\n\t\t\t\t\t<!-- BEGIN: " . $key . " -->\n";
                $content_2 .= "\t\t\t\t\t\t<option value=\"{OPTION.key}\" {OPTION.selected}>{OPTION.title}</option\n>";
                $content_2 .= "\t\t\t\t\t<!-- END: " . $key . " -->\n";
                $content_2 .= "\t\t\t\t\t</select>\n";
                $content_2 .= "\t\t\t\t";
            } else {
                switch ($column['field_type']) {
                    case 'email':
                        $type_html = 'email';
                        break;
                    case 'url':
                        $type_html = 'url';
                        break;
                    case 'password':
                        $type_html = 'password';
                        break;
                    default:
                        $type_html = 'text';
                }

                $oninvalid = true;
                $content_2 .= "<input class=\"form-control\" type=\"" . $type_html . "\" name=\"custom[" . $key . "]\" value=\"{ROW." . $column['field'] . "}\" ";
                if ($column['field_type'] == 'date' or $column['field_type'] == 'time') {
                    $content_2 .= 'id="' . $key . '" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" ';
                    $array_field_js['date'][] = '#' . $key;
                } elseif ($column['field_type'] == 'textfile') {
                    $content_2 .= 'id="id_' . $key . '" ';
                    $array_field_js['file'][] = $key;
                } elseif ($column['field_type'] == 'textalias') {
                    $content_2 .= 'id="id_' . $key . '" ';
                } elseif ($column['field_type'] == 'email') {
                    $content_2 .= "oninvalid=\"setCustomValidity( nv_email )\" oninput=\"setCustomValidity('')\" ";
                    $oninvalid = false;
                } elseif ($column['field_type'] == 'url') {
                    $content_2 .= "oninvalid=\"setCustomValidity( nv_url )\" oninput=\"setCustomValidity('')\" ";
                    $oninvalid = false;
                } elseif ($column['field_type'] == 'number_int') {
                    $content_2 .= "pattern=\"^[0-9]*$\"  oninvalid=\"setCustomValidity( nv_digits )\" oninput=\"setCustomValidity('')\" ";
                    $oninvalid = false;
                } elseif ($column['field_type'] == 'number_float') {
                    $content_2 .= "pattern=\"^([0-9]*)(\.*)([0-9]+)$\" oninvalid=\"setCustomValidity( nv_number )\" oninput=\"setCustomValidity('')\" ";
                    $oninvalid = false;
                }

                if (isset($array_requireds[$key])) {
                    $content_2 .= 'required="required" ';
                    if ($oninvalid) {
                        $content_2 .= "oninvalid=\"setCustomValidity( nv_required )\" oninput=\"setCustomValidity('')\" ";
                    }
                }

                $content_2 .= "/>";
                if ($column['field_type'] == 'textfile') {
                    $content_2 .= '&nbsp;<button type="button" class="btn btn-info" id="img_' . $key . '"><i class="fa fa-folder-open-o">&nbsp;</i> Browse server </button>';
                }
                if ($column['field_type'] == 'textalias' and $array_field_js['textalias'] == $key) {
                    $content_2 .= "&nbsp;<i class=\"fa fa-refresh fa-lg icon-pointer\" onclick=\"nv_get_alias('id_" . $key . "');\">&nbsp;</i>";
                }
            }
            $content_2 .= "</div>\n";
            $content_2 .= "\t\t\t</div>\n";
        }

        $content_2 .= "\t\t</div>\n";
        $content_2 .= "\t</div>\n";

        if (!empty($array_field_js['date'])) {
            $array_field_js['date'] = implode(',', $array_field_js['date']);
            $content_2 .= "\n<script type=\"text/javascript\">\n";
            $content_2 .= "$(document).ready(function() {\n";
            $content_2 .= "\t$(\"" . $array_field_js['date'] . "\").datepicker({\n";
            $content_2 .= "\t	showOn : \"both\",\n";
            $content_2 .= "\t	dateFormat : \"dd/mm/yy\",\n";
            $content_2 .= "\t	changeMonth : true,\n";
            $content_2 .= "\t	changeYear : true,\n";
            $content_2 .= "\t	showOtherMonths : true,\n";
            $content_2 .= "\t	buttonImage : nv_base_siteurl + \"assets/images/calendar.gif\",\n";
            $content_2 .= "\t	buttonImageOnly : true\n";
            $content_2 .= "\t});\n";
            $content_2 .= "});\n";
            $content_2 .= "</script>\n";
        }

        $content_2 .= "<!-- END: main -->";

        if (!file_exists(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_upload . '/files_tpl')) {
            nv_mkdir(NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_upload, 'files_tpl');
        }
        $file = NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/' . $module_upload . '/files_tpl/cat_form_' . preg_replace('/[\-]/', '_', $array_template[$templateids_i]['alias']) . '.tpl';
        file_put_contents($file, $content_2, LOCK_EX);
    }
}