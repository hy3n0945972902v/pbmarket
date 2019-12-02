<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */
if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$idtemplate = $db->query('SELECT id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_template where alias = "' . preg_replace("/[\_]/", "-", $array_market_cat[$row['catid']]['form']) . '"')->fetchColumn();
if ($idtemplate) {
    $array_tmp = array();
    $result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_field');
    while ($_row = $result->fetch()) {
        $language = unserialize($_row['language']);
        $_row['title'] = (isset($language[NV_LANG_DATA])) ? $language[NV_LANG_DATA][0] : $_row['field'];
        $_row['description'] = (isset($language[NV_LANG_DATA])) ? nv_htmlspecialchars($language[NV_LANG_DATA][1]) : '';

        $value = (isset($row['custom_field'][$_row['fid']])) ? $row['custom_field'][$_row['fid']] : '';

        if (!empty($_row['field_choices'])) {
            $_row['field_choices'] = unserialize($_row['field_choices']);
            if ($_row['field_type'] == 'date') {
                $row['custom_field'][$_row['fid']] = ($_row['field_choices']['current_date']) ? NV_CURRENTTIME : $_row['default_value'];
            } elseif ($_row['field_type'] == 'number') {
                $row['custom_field'][$_row['fid']] = $_row['default_value'];
            } else {
                $temp = array_keys($_row['field_choices']);
                $tempkey = intval($_row['default_value']) - 1;
                $row['custom_field'][$_row['fid']] = (isset($temp[$tempkey])) ? $temp[$tempkey] : '';
            }
        } elseif (!empty($_row['sql_choices'])) {
            $_row['sql_choices'] = explode(',', $_row['sql_choices']);
            $query = 'SELECT ' . $_row['sql_choices'][2] . ', ' . $_row['sql_choices'][3] . ' FROM ' . $_row['sql_choices'][1];
            $result_sql = $db->query($query);
            $weight = 0;
            while (list ($key, $val) = $result_sql->fetch(3)) {
                $_row['field_choices'][$key] = $val;
            }
        }

        if ($value != '') {
            if ($_row['field_type'] == 'number') {
                $number_type = $_row['field_choices']['number_type'];
                $pattern = ($number_type == 1) ? "/^[0-9]+$/" : "/^[0-9\.]+$/";

                if (!preg_match($pattern, $value)) {
                    $error[] = sprintf($lang_module['field_match_type_error'], $_row['title']);
                } else {
                    $value = ($number_type == 1) ? intval($value) : floatval($value);

                    if ($value < $_row['min_length'] or $value > $_row['max_length']) {
                        $error[] = sprintf($lang_module['field_min_max_value'], $_row['title'], $_row['min_length'], $_row['max_length']);
                    }
                }
            } elseif ($_row['field_type'] == 'date') {
                if (preg_match("/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/", $value, $m)) {
                    $value = mktime(0, 0, 0, $m[2], $m[1], $m[3]);

                    if ($value < $_row['min_length'] or $value > $_row['max_length']) {
                        $error[] = sprintf($lang_module['field_min_max_value'], $_row['title'], date('d/m/Y', $_row['min_length']), date('d/m/Y', $_row['max_length']));
                    }
                } else {
                    $error[] = sprintf($lang_module['field_match_type_error'], $_row['title']);
                }
            } elseif ($_row['field_type'] == 'textbox') {
                if ($_row['match_type'] == 'alphanumeric') {
                    if (!preg_match("/^[a-zA-Z0-9\_]+$/", $value)) {
                        $error[] = sprintf($lang_module['field_match_type_error'], $_row['title']);
                    }
                } elseif ($_row['match_type'] == 'email') {
                    if ($check = nv_check_valid_email($value) != '') {
                        $error[] = $check;
                    }
                } elseif ($_row['match_type'] == 'url') {
                    if (!nv_is_url($value)) {
                        $error[] = sprintf($lang_module['field_match_type_error'], $_row['title']);
                    }
                } elseif ($_row['match_type'] == 'regex') {
                    if (!preg_match("/" . $_row['match_regex'] . "/", $value)) {
                        $error[] = sprintf($lang_module['field_match_type_error'], $_row['title']);
                    }
                } elseif ($_row['match_type'] == 'callback') {
                    if (function_exists($_row['func_callback'])) {
                        if (!call_user_func($_row['func_callback'], $value)) {
                            $error[] = sprintf($lang_module['field_match_type_error'], $_row['title']);
                        }
                    } else {
                        $error[] = "error function not exists " . $_row['func_callback'];
                    }
                } else {
                    $value = nv_htmlspecialchars($value);
                }

                $strlen = nv_strlen($value);

                if ($strlen < $_row['min_length'] or $strlen > $_row['max_length']) {
                    nv_jsonOutput(array(
                        'error' => 1,
                        'msg' => sprintf($lang_module['field_min_max_error'], $_row['title'], $_row['min_length'], $_row['max_length'])
                    ));
                }
            } elseif ($_row['field_type'] == 'textarea' or $_row['field_type'] == 'editor') {
                $allowed_html_tags = array_map("trim", explode(',', NV_ALLOWED_HTML_TAGS));
                $allowed_html_tags = "<" . implode("><", $allowed_html_tags) . ">";
                $value = strip_tags($value, $allowed_html_tags);

                if ($_row['match_type'] == 'regex') {
                    if (!preg_match("/" . $_row['match_regex'] . "/", $value)) {
                        $error[] = sprintf($lang_module['field_match_type_error'], $_row['title']);
                    }
                } elseif ($_row['match_type'] == 'callback') {
                    if (function_exists($_row['func_callback'])) {
                        if (!call_user_func($_row['func_callback'], $value)) {
                            $error[] = sprintf($lang_module['field_match_type_error'], $_row['title']);
                        }
                    } else {
                        $error[] = "error function not exists " . $_row['func_callback'];
                    }
                }

                $value = ($_row['field_type'] == 'textarea') ? nv_nl2br($value, '<br />') : nv_editor_nl2br($value);
                $strlen = nv_strlen($value);

                if ($strlen < $_row['min_length'] or $strlen > $_row['max_length']) {
                    $error[] = sprintf($lang_module['field_min_max_error'], $_row['title'], $_row['min_length'], $_row['max_length']);
                }
            } elseif ($_row['field_type'] == 'checkbox' or $_row['field_type'] == 'multiselect') {
                $temp_value = array();
                foreach ($value as $value_i) {
                    if (isset($_row['field_choices'][$value_i])) {
                        $temp_value[] = $value_i;
                    }
                }

                $value = implode(',', $temp_value);
            } elseif ($_row['field_type'] == 'select' or $_row['field_type'] == 'radio') {
                if (!isset($_row['field_choices'][$value])) {
                    $error[] = sprintf($lang_module['field_match_type_error'], $_row['title']);
                }
            }

            $row['custom_field'][$_row['fid']] = $value;
        }

        if ($row['id']) {
            $query_field[] = $_row['field'] . '=' . $db->quote($value);
        } else {
            $query_field[$_row['field']] = $db->quote($value);
        }
    }
}