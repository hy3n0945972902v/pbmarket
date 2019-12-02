<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
if (! defined('NV_MAINFILE'))
    die('Stop!!!');

$module_version = array(
    'name' => 'Market',
    'modfuncs' => 'main,detail,search,upload,ajax,viewcat,userarea,content,viewlocation,saved,company,company-content,groups,payment,tag,cronjobs,news',
    'change_alias' => 'userarea,content,saved,company,company-content,groups,tag,news',
    'submenu' => 'main,detail,search,news',
    'is_sysmod' => 0,
    'virtual' => 1,
    'version' => '1.0.02',
    'date' => 'Sun, 20 Nov 2016 07:31:05 GMT',
    'author' => 'mynukeviet (contact@mynukeviet.net)',
    'uploads_dir' => array(
        $module_name
    ),
    'note' => ''
);