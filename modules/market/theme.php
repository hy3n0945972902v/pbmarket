<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (!defined('NV_IS_MOD_MARKET')) die('Stop!!!');

/**
 * nv_theme_market_main()
 *
 * @param mixed $array_data
 * @param mixed $viewtype
 * @param mixed $page
 * @return
 *
 */
function nv_theme_market_main($array_data, $viewtype, $page)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op;

    $xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);

    if (nv_function_exists('nv_theme_market_' . $viewtype)) {
        $xtpl->assign('DATA', call_user_func('nv_theme_market_' . $viewtype, $array_data, $page));
    } else {
        return '';
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_main_cat()
 *
 * @param mixed $array_data
 * @param mixed $viewtype
 * @return
 *
 */
function nv_theme_market_main_cat($array_data, $viewtype)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_market_cat;

    $xtpl = new XTemplate('main_cat.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);

    if (!empty($array_data)) {
        foreach ($array_data as $catid => $catinfo) {
            if (isset($catinfo['subid']) && $catinfo['subid'] != '') {
                $_arr_subcat = explode(',', $catinfo['subid']);
                $limit = 0;
                foreach ($_arr_subcat as $catid_i) {
                    if ($array_market_cat[$catid_i]['inhome'] == 1) {
                        $xtpl->assign('SUBCAT', $array_market_cat[$catid_i]);
                        $xtpl->parse('main.cat.subcatloop');
                        $limit++;
                    }
                    if ($limit >= 3) {
                        $xtpl->assign('MORE', array(
                            'title' => $lang_module['more'],
                            'link' => $array_market_cat[$catid]['link']
                        ));
                        $xtpl->parse('main.cat.subcatmore');
                        break;
                    }
                }
            }

            if ($catinfo['count'] > 0) {
                $xtpl->assign('CAT', $catinfo);
                if (nv_function_exists('nv_theme_market_' . $catinfo['viewtype'])) {
                    $xtpl->assign('DATA', call_user_func('nv_theme_market_' . $catinfo['viewtype'], $catinfo['data']));
                }
                $xtpl->parse('main.cat');
            }
        }
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_viewcat()
 *
 * @param mixed $array_data
 * @param mixed $array_subcat_data
 * @param mixed $viewtype
 * @param mixed $page
 * @return
 *
 */
function nv_theme_market_viewcat($array_data, $array_subcat_data, $viewtype, $page)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_market_cat, $catid;

    $xtpl = new XTemplate('viewcat.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('CAT', $array_market_cat[$catid]);

    if (nv_function_exists('nv_theme_market_' . $viewtype)) {
        $xtpl->assign('DATA', call_user_func('nv_theme_market_' . $viewtype, $array_data, $page));
    } else {
        return '';
    }

    if (!empty($array_market_cat[$catid]['description_html'])) {
        $xtpl->parse('main.description_html');
    }

    if (!empty($array_subcat_data)) {
        foreach ($array_subcat_data as $subcat) {
            $xtpl->assign('SUBCAT', $subcat);
            $xtpl->parse('main.subcat.loop');
        }
        $xtpl->parse('main.subcat');
    }

    if (!empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_viewtag()
 *
 * @param mixed $title
 * @param mixed $array_data
 * @param mixed $viewtype
 * @param mixed $page
 * @return
 *
 */
function nv_theme_market_viewtag($title, $array_data, $viewtype, $page)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op;

    $xtpl = new XTemplate('tag.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TITLE', $title);

    if (nv_function_exists('nv_theme_market_' . $viewtype)) {
        $xtpl->assign('DATA', call_user_func('nv_theme_market_' . $viewtype, $array_data, $page));
    } else {
        return '';
    }

    if (!empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_viewlist()
 *
 * @param mixed $array_data
 * @return
 *
 */
function nv_theme_market_viewlist($array_data, $page = '')
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config, $themeConfig;

    $xtpl = new XTemplate('viewlist.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $home_image_size = explode('x', $array_config['home_image_size']);
    $xtpl->assign('WIDTH', $home_image_size[0]);
    $xtpl->assign('HEIGHT', $home_image_size[1]);

    require_once NV_ROOTDIR . '/modules/location/location.class.php';
    $location = new Location();

    if (!empty($array_data)) {
        $i = $j = 1;
        foreach ($array_data as $data) {
            $data['location'] = $location->locationString($data['area_p'], $data['area_d'], 0, ' » ');
            $data['location_link'] = nv_market_build_search_url($module_name, $data['typeid'], $data['catid'], $data['area_p'], $data['area_d']);

            $xtpl->assign('ROW', $data);

            if (!empty($data['location'])) {
                $xtpl->parse('main.loop.location');
            }

            if (!empty($data['type'])) {
                $xtpl->parse('main.loop.type');
            }

            if (isset($data['auction']) && nv_check_auction($data['auction'], $data['auction_begin'], $data['auction_end'], $data['auction_price_begin'], $data['auction_price_step'])) {
                $xtpl->parse('main.loop.auction');
            }

            if (!empty($data['color'])) {
                $xtpl->parse('main.loop.color');
            }

            if ($i == $array_config['block_viewlist']) {
                if (nv_block_position('[MARKET_BLOCK_' . $j . ']')) {
                    $xtpl->assign('BLOCK', '[MARKET_BLOCK_' . $j . ']');
                    $xtpl->parse('main.loop.block');
                    $i = 0;
                    $j++;
                }
            }

            if ($data['contact_fullname'] or $data['contact_email'] or $data['contact_phone'] or $data['contact_address']) {
                if ($data['contact_fullname']) {
                    $xtpl->parse('main.loop.contact.contact_fullname');
                }

                if ($data['contact_email']) {
                    $xtpl->parse('main.loop.contact.contact_email');
                }

                if ($data['contact_phone']) {
                    $xtpl->parse('main.loop.contact.contact_phone');
                    $xtpl->parse('main.loop.contact.contact_phone_icon');
                }

                if ($data['contact_address']) {
                    $xtpl->parse('main.loop.contact.contact_address');
                }
                $xtpl->parse('main.loop.contact');
            }

            $xtpl->parse('main.loop');
            $i++;
        }
    }

    if (!empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_viewgrid()
 *
 * @param mixed $array_data
 * @return
 *
 */
function nv_theme_market_viewgrid($array_data, $page = '')
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config, $themeConfig;

    $xtpl = new XTemplate('viewgrid.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $home_image_size = explode('x', $array_config['home_image_size']);
    $xtpl->assign('WIDTH', $home_image_size[0]);
    $xtpl->assign('HEIGHT', $home_image_size[1]);

    require_once NV_ROOTDIR . '/modules/location/location.class.php';
    $location = new Location();

    if (!empty($array_data)) {
        $i = $j = 1;
        foreach ($array_data as $data) {
            $data['location'] = $location->locationString($data['area_p'], $data['area_d'], 0, ' » ');
            $data['location_link'] = nv_market_build_search_url($module_name, $data['typeid'], $data['catid'], $data['area_p'], $data['area_d']);

            $xtpl->assign('ROW', $data);

            if (!empty($data['location'])) {
                $xtpl->parse('main.loop.location');
            }

            if (!empty($data['type'])) {
                $xtpl->parse('main.loop.type');
            }

            if (isset($data['auction']) && nv_check_auction($data['auction'], $data['auction_begin'], $data['auction_end'], $data['auction_price_begin'], $data['auction_price_step'])) {
                $xtpl->parse('main.loop.auction');
            }

            if (!empty($data['color'])) {
                $xtpl->parse('main.loop.color');
            }

            if ($i == $array_config['block_viewgrid']) {
                if (nv_block_position('[MARKET_BLOCK_' . $j . ']')) {
                    $xtpl->assign('BLOCK', '[MARKET_BLOCK_' . $j . ']');
                    $xtpl->parse('main.loop.block');
                    $i = 0;
                    $j++;
                }
            }

            $xtpl->parse('main.loop');
            $i++;
        }
    }

    if (!empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_viewgroup()
 *
 * @param mixed $groups_data
 * @param mixed $array_data
 * @param mixed $viewtype
 * @param mixed $page
 * @return
 *
 */
function nv_theme_market_viewgroup($groups_data, $array_data, $viewtype, $page)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_market_cat, $catid;

    $xtpl = new XTemplate('viewgroup.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GROUP', $groups_data);

    if (nv_function_exists('nv_theme_market_' . $viewtype)) {
        $xtpl->assign('DATA', call_user_func('nv_theme_market_' . $viewtype, $array_data, $page));
    } else {
        return '';
    }

    if (!empty($groups_data['description'])) {
        $xtpl->parse('main.description');
    }

    if (!empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_viewlist_simple()
 *
 * @param mixed $array_data
 * @return
 *
 */
function nv_theme_market_viewlist_simple($array_data, $page = '')
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config;

    $xtpl = new XTemplate('viewlist_simple.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);

    require_once NV_ROOTDIR . '/modules/location/location.class.php';
    $location = new Location();

    if (!empty($array_data)) {
        foreach ($array_data as $data) {
            $data['location'] = $location->locationString($data['area_p'], $data['area_d'], 0, ' » ');
            $data['location_link'] = nv_market_build_search_url($module_name, $data['typeid'], $data['catid'], $data['area_p'], $data['area_d']);

            $xtpl->assign('ROW', $data);

            if (!empty($data['color'])) {
                $xtpl->parse('main.loop.color');
            }

            if ($data['contact_fullname'] or $data['contact_email'] or $data['contact_phone'] or $data['contact_address']) {
                if ($data['contact_fullname']) {
                    $xtpl->parse('main.loop.contact.contact_fullname');
                }

                if ($data['contact_email']) {
                    $xtpl->parse('main.loop.contact.contact_email');
                }

                if ($data['contact_phone']) {
                    $xtpl->parse('main.loop.contact.contact_phone');
                    $xtpl->parse('main.loop.contact.contact_phone_icon');
                }

                if ($data['contact_address']) {
                    $xtpl->parse('main.loop.contact.contact_address');
                }
                $xtpl->parse('main.loop.contact');
            }

            $xtpl->parse('main.loop');
        }

        if (!empty($page)) {
            $xtpl->assign('PAGE', $page);
            $xtpl->parse('main.page');
        }
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_detail()
 *
 * @param mixed $array_data
 * @param mixed $rows_other
 * @param mixed $array_words
 * @return
 *
 */
function nv_theme_market_detail($array_data, $rows_other, $array_keyword)
{
    global $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $client_info, $array_config, $site_mods;

    $xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('DATA', $array_data);
    $xtpl->assign('SELFURL', $client_info['selfurl']);
    $xtpl->assign('MONEY_UNIT', $array_config['money_unit']);
    $xtpl->assign('TEMPLATE', $module_info['template']);

    if (!empty($array_data['images'])) {
        foreach ($array_data['images'] as $image) {
            $xtpl->assign('IMAGE', $image);
            $xtpl->parse('main.image.loop');
        }
        $xtpl->parse('main.image');
        $xtpl->parse('main.image1');
    } else {
        $xtpl->parse('main.image2');
    }

    $is_contact = 0;
    if (!empty($array_data['contact_fullname'])) {
        $is_contact = 1;
        $xtpl->parse('main.contact.fullname');
    }

    if (!empty($array_data['contact_email'])) {
        $is_contact = 1;
        $xtpl->parse('main.contact.email');
    }

    if (!empty($array_data['contact_phone'])) {
        $is_contact = 1;
        $xtpl->parse('main.contact.phone');
    }

    if (!empty($array_data['contact_address'])) {
        $is_contact = 1;
        $xtpl->parse('main.contact.address');
    }

    if ($is_contact) {
        $xtpl->parse('main.contact');
    }

    if (!empty($array_data['location'])) {
        $xtpl->parse('main.location');
    }

    if (!empty($array_data['price'])) {
        $xtpl->parse('main.price');
    }

    if (!empty($array_data['custom_field'])) {
        foreach ($array_data['custom_field'] as $field) {
            if (!empty($field['value'])) {
                $xtpl->assign('FIELD', $field);
                $xtpl->parse('main.field');
            }
        }
    }

    if (!empty($rows_other)) {
        $xtpl->assign('OTHER', nv_theme_market_viewlist_simple($rows_other));
        $xtpl->parse('main.other');
    }

    if ($array_data['auction']) {
        if (!defined('NV_IS_USER')) {
            $xtpl->parse('main.auction.login');
        } elseif ($array_data['auction_status'] != 2) {
            if (($array_data['auction_begin'] - $array_config['auction_register_time']) >= NV_CURRENTTIME) {
                if ($array_data['auction_registed']) {
                    $xtpl->parse('main.auction.register.register_hidden');
                } else {
                    $xtpl->parse('main.auction.register.cancel_hidden');
                }
                $xtpl->parse('main.auction.register');
            }

            if ($array_data['auction_registed'] and nv_auction_status($array_data['auction_begin'], $array_data['auction_end']) != 1) {
                $xtpl->parse('main.auction.auction_value_disabled');
                $xtpl->parse('main.auction.auction_value_disabled_btn');
            }

            if ($array_data['auction_registed']) {
                $xtpl->parse('main.auction.frm_auction');
            }
        }

        $xtpl->assign('FIREBASE_URL', $array_config['auction_firebase_url'] . '/' . $array_data['id']);
        $xtpl->assign('DES_POINT', $array_config['des_point']);
        $xtpl->assign('THOUSANDS_SEP', $array_config['thousands_sep']);

        if ($array_data['auction_status'] == 2) {
            $xtpl->parse('main.auction.auction_heading');
        } else {
            $xtpl->parse('main.auction.auction_heading_end');
        }

        $xtpl->parse('main.auction');
    }

    if (isset($site_mods['comment']) and isset($module_config[$module_name]['activecomm']) and $module_config[$module_name]['activecomm']) {
        $xtpl->parse('main.comment');
    }

    if (defined('NV_IS_MODADMIN')) {
        if (!empty($array_keyword)) {
            foreach ($array_keyword as $i => $value) {
                $xtpl->assign('KEYWORDS', $value['keyword']);
                $xtpl->parse('main.admin_keywords.keywords');
            }
        }
        $xtpl->parse('main.admin_keywords');
    }

    if ($array_data['is_admin']) {
        $xtpl->parse('main.admin');
    }

    if (!empty($array_keyword)) {
        $t = sizeof($array_keyword) - 1;
        foreach ($array_keyword as $i => $value) {
            $xtpl->assign('KEYWORD', $value['keyword']);
            $xtpl->assign('LINK_KEYWORDS', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=tag/' . urlencode($value['alias']));
            $xtpl->assign('SLASH', ($t == $i) ? '' : ', ');
            $xtpl->parse('main.keywords.loop');
        }
        $xtpl->parse('main.keywords');
    }

    if ($array_config['refresh_allow']) {
        $xtpl->parse('main.refresh');
    }

    if (!empty($array_data['type'])) {
        $xtpl->parse('main.type');
    }

    if (nv_block_position('[MARKET_BLOCK_1]')) {
        $xtpl->assign('BLOCK_1', '[MARKET_BLOCK_1]');
    }

    if (!empty($array_data['maps'])) {
        $xtpl->assign('MAPS_ADPI', $array_config['maps_appid']);
        $xtpl->parse('main.maps_title');
        $xtpl->parse('main.maps_content');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_search()
 *
 * @param mixed $array_data
 * @return
 *
 */
function nv_theme_market_search($array_data, $is_search, $viewtype, $page, $array_json)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $page_title, $array_config;

    $xtpl = new XTemplate('search.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TITLE', $page_title);
    $xtpl->assign('ARRAY_CONFIG', $array_config);

    if ($array_json) {
        $xtpl->assign('JSON_OUT', json_encode($array_json));
        $xtpl->parse('main.maps');
    }

    if ($is_search) {
        if (!empty($array_data)) {
            if (nv_function_exists('nv_theme_market_' . $viewtype)) {
                $xtpl->assign('DATA', call_user_func('nv_theme_market_' . $viewtype, $array_data));
            } else {
                return '';
            }

            if (!empty($page)) {
                $xtpl->assign('PAGE', $page);
                $xtpl->parse('main.result.page');
            }
            if ($array_json) {
                $xtpl->parse('main.result.maps');
            } else {
                $xtpl->parse('main.result.nomaps');
            }
            $xtpl->parse('main.result');
        } else {
            $xtpl->parse('main.result_empty');
        }
    } else {
        $xtpl->parse('main.empty');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_viewlocation()
 *
 * @param mixed $location_info
 * @param mixed $array_data
 * @param mixed $viewtype
 * @param mixed $page
 * @return
 *
 */
function nv_theme_market_viewlocation($location_info, $array_data, $viewtype, $page)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_market_cat, $catid;

    $xtpl = new XTemplate('viewlocation.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('LOCATION', $location_info);

    if (nv_function_exists('nv_theme_market_' . $viewtype)) {
        $xtpl->assign('DATA', call_user_func('nv_theme_market_' . $viewtype, $array_data, $page));
    } else {
        return '';
    }

    if (!empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_saved()
 *
 * @param mixed $array_data
 * @param mixed $page
 * @return
 *
 */
function nv_theme_market_saved($array_data, $page)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_market_cat, $catid, $lang_global, $user_info;

    $xtpl = new XTemplate('saved.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('ACTION_URL', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=ajax');
    $xtpl->assign('CHECKSS', md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . NV_CACHE_PREFIX));

    if (!empty($array_data)) {
        foreach ($array_data as $data) {
            $xtpl->assign('DATA', $data);
            $xtpl->parse('main.loop');
        }
    }

    if (!empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }

    $array_action = array(
        'delete_list_id' => $lang_global['delete']
    );

    foreach ($array_action as $key => $value) {
        $xtpl->assign('ACTION', array(
            'key' => $key,
            'value' => $value
        ));
        $xtpl->parse('main.action');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_company()
 *
 * @param mixed $company_info
 * @return
 *
 */
function nv_theme_market_company($company_info)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config;

    $xtpl = new XTemplate('company.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('ROW', $company_info);

    if (!empty($company_info['website'])) {
        $xtpl->parse('main.website');
    }

    if (!empty($company_info['agent'])) {
        $xtpl->parse('main.agent');
    }

    if (!empty($company_info['taxcode'])) {
        $xtpl->parse('main.taxcode');
    }

    if (!empty($company_info['fax'])) {
        $xtpl->parse('main.fax');
    }

    if (!empty($company_info['image'])) {
        $xtpl->parse('main.image');
    }

    if (!empty($company_info['descripion'])) {
        $xtpl->parse('main.descripion');
    }

    if (!empty($company_info['province'])) {
        $xtpl->parse('main.province');
    }

    if (!empty($company_info['district'])) {
        $xtpl->parse('main.district');
    }

    if (!empty($array_config['googlemaps_appid'])) {
        $xtpl->assign('GOOGLEMAPS_APPID', $array_config['googlemaps_appid']);
        $xtpl->parse('main.googlemaps');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_payment()
 *
 * @param mixed $array_info
 * @param mixed $array_option
 * @param mixed $id
 * @param mixed $mod
 * @return
 *
 */
function nv_theme_market_payment($array_info, $array_option, $id, $mod)
{
    global $global_config, $module_name, $module_file, $lang_module, $lang_global, $module_config, $module_info, $op, $array_config, $user_info, $array_groups;

    $xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('INFO', $array_info);
    $xtpl->assign('MONEY_UNIT', $array_config['money_unit']);

    if (!empty($array_option)) {

        if ($mod == 'group') {
            $xtpl->assign('GROUP', $array_groups);
        }

        $i = 0;
        foreach ($array_option as $option) {
            $option['tokenkey'] = md5($array_info['id'] . $option['price'] . 'VND');
            if ($mod == 'refresh') {
                $option['checksum'] = md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . $array_info['id'] . '-' . $option['number']);
            } elseif ($mod == 'group') {
                $option['checksum'] = md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . $array_info['id'] . '-' . $option['time']);
            }

            $option['checked'] = '';
            if ($i == 0) {
                $option['checked'] = 'checked="checked"';
                $xtpl->assign('FIRST', array(
                    'number' => isset($option['number']) ? $option['number'] : 0,
                    'price' => $option['price'],
                    'tokenkey' => $option['tokenkey'],
                    'checksum' => $option['checksum']
                ));
            }
            $option['price_format'] = nv_market_number_format($option['price']);
            $xtpl->assign('OPTION', $option);
            $xtpl->parse('main.' . $mod . '.option');
            $i++;
        }
        $xtpl->parse('main.' . $mod);
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_market_refresh()
 *
 * @param mixed $id
 * @param mixed $content
 * @param mixed $is_owner
 * @param mixed $refresh_timelimit
 * @return
 *
 */
function nv_theme_market_refresh($id, $content, $is_owner, $refresh_timelimit)
{
    global $global_config, $module_name, $module_file, $lang_module, $lang_global, $module_config, $module_info, $op, $array_config, $user_info, $array_groups, $user_info;

    $lang_module['refresh_alert_buy'] = sprintf($lang_module['refresh_alert_buy'], $module_name);

    $xtpl = new XTemplate('refresh.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('MODULE_NAME', $module_name);
    $xtpl->assign('ID', $id);
    $xtpl->assign('CHECKSS', md5($global_config['sitekey'] . '-' . $user_info['userid'] . '-' . $id));
    $xtpl->assign('CONTENT', $content);

    if (!defined('NV_IS_USER')) {
        $xtpl->parse('main.guest');
    } else {
        if ($is_owner) {
            $refresh = nv_count_refresh($module_name);
            $refresh_free = nv_count_refresh_free($module_name);
            $refresh_count = $refresh + $refresh_free;
            $xtpl->assign('REFRESH_COUNT', sprintf($lang_module['refresh_info'], $refresh_count));

            if (!empty($refresh_timelimit)) {
                $xtpl->assign('DISABLED', 'disabled');
                $xtpl->assign('TIMELIMIT', $refresh_timelimit);
                $xtpl->parse('main.member.owner.timelimit');
            } elseif ($refresh_count == 0) {
                $xtpl->assign('DISABLED', 'disabled');
                $xtpl->parse('main.member.owner.empty');
            }

            $xtpl->parse('main.member.owner');
        } else {
            $xtpl->parse('main.member.nonowner');
        }
        $xtpl->parse('main.member');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}