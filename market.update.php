<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */
define('NV_SYSTEM', true);

// Xac dinh thu muc goc cua site
define('NV_ROOTDIR', pathinfo(str_replace(DIRECTORY_SEPARATOR, '/', __file__), PATHINFO_DIRNAME));

require NV_ROOTDIR . '/includes/mainfile.php';
require NV_ROOTDIR . '/includes/core/user_functions.php';

// Duyệt tất cả các ngôn ngữ
$language_query = $db->query('SELECT lang FROM ' . $db_config['prefix'] . '_setup_language WHERE setup = 1');
while (list ($lang) = $language_query->fetch(3)) {
    $mquery = $db->query("SELECT title, module_data FROM " . $db_config['prefix'] . "_" . $lang . "_modules WHERE module_file = 'market'");
    while (list ($mod, $mod_data) = $mquery->fetch(3)) {

        $_sql = array();

        $data = array(
            'maps_appid' => '',
            'priceformat' => 0
        );
        foreach ($data as $config_name => $config_value) {
            $_sql[] = "INSERT INTO " . $dataname . "." . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', " . $db->quote($mod) . ", " . $db->quote($config_name) . ", " . $db->quote($config_value) . ")";
        }

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'refresh_allow', '0');";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'refresh_config', '');";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows ADD ordertime INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER userid;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_block_cat ADD useradd TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER keywords;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_block_cat ADD color VARCHAR(10) NOT NULL AFTER image;";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'specialgroup_config', '');";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_block ADD exptime INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER id;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows DROP queue_time, DROP queue_userid;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows ADD groupid VARCHAR(255) NOT NULL AFTER catid;";

        $result = $db->query("SELECT id FROM " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows");
        while ($row = $result->fetch()) {
            $_result = $db->query("SELECT bid FROM " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_block WHERE id=" . $row['id']);
            $list_bid = array();
            while (list ($bid) = $_result->fetch(3)) {
                $list_bid[] = $bid;
            }
            if (!empty($list_bid)) {
                $_sql[] = "UPDATE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows SET groupid=" . $db->quote(implode(',', $list_bid)) . ' WHERE id=' . $row['id'];
            }
        }

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_econtent(action varchar(100) NOT NULL, econtent text NOT NULL, PRIMARY KEY (action ) ENGINE=MyISAM";

        $_sql[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_econtent (action, econtent) VALUES('queue_status', 'Xin chào <strong>&#91;NAME&#93;. </strong>Chúng tôi<strong>&nbsp;</strong>xin gửi đến bạn thông báo về trạng thái tin rao của bạn tại&nbsp;<strong>&#91;SITE_NAME&#93;!</strong><br  /><br  />Trạng thái hiện tại: &#91;STATUS&#93;<br  />Kiểm duyệt bởi: &#91;QUEUE_NAME&#93;<br  />Thời gian duyệt: &#91;QUEUE_TIME&#93;<br  />Ghi chú: &#91;NOTE&#93;<br  /><br  />Mọi ý kiến xin gửi về &#91;SITE_EMAIL&#93; để được giải đáp, xin cảm ơn!')";

        $_sql[] = "UPDATE " . $db_config['prefix'] . "_setup_extensions SET version='1.0.01 " . NV_CURRENTTIME . "' WHERE type='module' and basename=" . $db->quote($mod);

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'style_default', 'viewlist_simple');";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'province_default', '0');";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows ADD description TEXT NOT NULL AFTER typeid;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows CHANGE contact_phone contact_phone VARCHAR(255) NOT NULL;";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'tags_alias', '0'), ('" . $lang . "', '" . $mod . "', 'auto_tags', '0'), ('" . $lang . "', '" . $mod . "', 'tags_remind', '1');";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_tags(
          tid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
          numnews mediumint(8) NOT NULL DEFAULT '0',
          alias varchar(250) NOT NULL DEFAULT '',
          image varchar(255) DEFAULT '',
          description text,
          keywords varchar(255) DEFAULT '',
          PRIMARY KEY (tid),
          UNIQUE KEY alias (alias)
        ) ENGINE=MyISAM;";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_tags_id(
          id int(11) NOT NULL,
          tid mediumint(9) NOT NULL,
          keyword varchar(65) NOT NULL,
          UNIQUE KEY id_tid (id,tid),
          KEY tid (tid)
        ) ENGINE=MyISAM;";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'fb_appid', ''), ('" . $lang . "', '" . $mod . "', 'fb_secret', ''), ('" . $lang . "', '" . $mod . "', 'fb_pagetoken_pages', '');";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows ADD count_fb_post SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0' AFTER count_refresh;";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'similar_content', '80');";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'similar_time', '5');";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_fb_queue( rowsid int(11) unsigned NOT NULL, UNIQUE KEY rowsid (rowsid) ) ENGINE=MyISAM";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_mail_queue( id smallint(4) unsigned NOT NULL AUTO_INCREMENT, tomail varchar(100) NOT NULL, subject varchar(255) NOT NULL, message text NOT NULL, PRIMARY KEY (id) ) ENGINE=MyISAM";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'fb_enable', '0');";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_queue_logs ADD reasonid TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' AFTER reason;";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_queue_reason( id smallint(4) unsigned NOT NULL AUTO_INCREMENT, title varchar(255) NOT NULL, note tinytext NOT NULL COMMENT 'Ghi chú', weight smallint(4) unsigned NOT NULL DEFAULT '0', status tinyint(1) NOT NULL COMMENT 'Trạng thái', PRIMARY KEY (id) ) ENGINE=MyISAM";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_refresh( userid int(11) unsigned NOT NULL, count int(11) unsigned NOT NULL DEFAULT '0', free smallint(4) unsigned NOT NULL DEFAULT '0', free_time int(11) unsigned NOT NULL DEFAULT '0', UNIQUE KEY userid (userid) ) ENGINE=MyISAM";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'refresh_default', '0'), ('" . $lang . "', '" . $mod . "', 'refresh_free', '0');";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows DROP count_refresh;";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'refresh_timelimit', '120');";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows ADD refresh_time INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER ordertime;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows ADD prior INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Ưu tiên' AFTER userid;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_block_cat ADD prior SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' AFTER weight;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows ADD groups_config TEXT NOT NULL AFTER is_queue;";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'fb_accesstoken', '');";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'fb_groupid', '');";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'auto_link', '1');";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'auto_link_casesens', '0');";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'auto_link_target', '');";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'auto_link_limit', '3');";

        $_sql[] = "UPDATE " . NV_CONFIG_GLOBALTABLE . " SET config_name = 'freelancegroup' WHERE lang = '" . $lang . "' AND module = '" . $mod . "' AND config_name = 'vipgroup';";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_freelance(
          userid int(11) unsigned NOT NULL,
          salary double unsigned NOT NULL DEFAULT '0' COMMENT 'Lương',
          total double unsigned NOT NULL DEFAULT '0' COMMENT 'Tổng thu nhập',
          pay double unsigned NOT NULL DEFAULT '0',
          UNIQUE KEY userid (userid)
        ) ENGINE=MyISAM";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'tags_alias_lower', '1');";

        $_sql[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_econtent (action, econtent) VALUES('refresh', '')";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_freelance_payment( id smallint(4) unsigned NOT NULL AUTO_INCREMENT, userid int(11) unsigned NOT NULL, money double unsigned NOT NULL DEFAULT '0' COMMENT 'Số tiền thanh toán', addtime int(11) unsigned NOT NULL, PRIMARY KEY (id) ) ENGINE=MyISAM";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_queue_edit(
          rowsid int(11) unsigned NOT NULL,
          title varchar(255) NOT NULL,
          catid smallint(4) NOT NULL,
          area_p smallint(4) NOT NULL COMMENT 'Vùng',
          area_d smallint(4) unsigned NOT NULL,
          typeid tinyint(1) NOT NULL,
          description text NOT NULL,
          content text NOT NULL,
          pricetype tinyint(1) unsigned NOT NULL DEFAULT '0',
          price double unsigned NOT NULL DEFAULT '0',
          price1 double unsigned NOT NULL DEFAULT '0',
          unitid smallint(4) unsigned NOT NULL,
          homeimgfile varchar(255) NOT NULL DEFAULT '',
          homeimgalt varchar(255) NOT NULL DEFAULT '',
          homeimgthumb tinyint(1) unsigned NOT NULL DEFAULT '0',
          note text NOT NULL,
          exptime int(11) unsigned NOT NULL DEFAULT '0',
          auction tinyint(1) unsigned NOT NULL DEFAULT '0',
          auction_begin int(11) unsigned NOT NULL DEFAULT '0',
          auction_end int(11) unsigned NOT NULL DEFAULT '0',
          auction_price_begin double unsigned NOT NULL DEFAULT '0',
          auction_price_step double unsigned NOT NULL DEFAULT '0',
          contact_fullname varchar(255) NOT NULL,
          contact_email varchar(100) NOT NULL,
          contact_phone varchar(255) NOT NULL,
          contact_address varchar(255) NOT NULL,
          images TEXT NOT NULL,
          keywords TEXT NOT NULL,
          PRIMARY KEY (rowsid)
        ) ENGINE=MyISAM";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows ADD is_queue_edit TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER is_queue;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_cat ADD pricetype TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER image;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows CHANGE price price DOUBLE UNSIGNED NOT NULL DEFAULT '0';";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows ADD price1 DOUBLE UNSIGNED NOT NULL DEFAULT '0' AFTER price;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_queue_edit CHANGE price price DOUBLE UNSIGNED NOT NULL DEFAULT '0';";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_queue_edit ADD price1 DOUBLE UNSIGNED NOT NULL DEFAULT '0' AFTER price;";

        $_sql[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_econtent (action, econtent) VALUES('terms', '<h2>1 .Tiêu đề</h2> - Tiêu đề cần thể hiện ngắn gọn nội dung cần đăng.<br /> Ví dụ: thay vì đặt <strong>Cần tuyển nhân viên</strong>, hãy đặt <strong>Cần tuyển nhân viên bán hàng quần áo tại shops Famy</strong><br /> - Không viết hoa toàn bộ tiêu đề<br /> - Nên viết hoa đầu dòng cũng như các địa danh, tên người, theo quy tắc tiếng việt <h2>2. Nội dung</h2> - Nội dung chi tiết cần mô tả rõ ràng nhất về nội dung muốn truyền tải<br /> - Chỉ chấp nhận nội dung là tiếng việt<br /> - Không đăng lại các nội dung đã được đăng trước đó. Sử dụng chức năng <a href=\"https://raodn.com/huong-dan/Huong-dan-lam-moi-tin.html\"><strong>Làm mới tin</strong></a> nếu bán muốn đăng lại tin cùng nội dung<br /> - Không được chèn các liên kết đến site khác ngoài <strong>raodn.com</strong><br /> - Không chứa các ký tự đặc biệt, cái biểu tượng (icon) <h2>3. Thông tin liên hệ</h2> Nội dung của bạn phải có ít nhất một trường thông tin liên hệ, là số điện thoại hoặc email để người xem có thể chủ động liên hệ với bạn khi cần thiết')";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'editor_guest', '0');";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'block_viewlist', '6');";

        $_sql[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $mod . "', 'remove_link', '1');";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_news( id int(11) unsigned NOT NULL AUTO_INCREMENT, catid smallint(4) unsigned NOT NULL, content text NOT NULL, addtime int(11) unsigned NOT NULL, PRIMARY KEY (id) ) ENGINE=MyISAM";

        $_sql[] = "UPDATE " . $db_config['prefix'] . "_setup_extensions SET version='1.0.02 " . NV_CURRENTTIME . "' WHERE type='module' and basename=" . $db->quote($mod);

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_type CHANGE title title VARCHAR(250) NOT NULL;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_type ADD alias VARCHAR(250) NOT NULL AFTER title;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_queue_edit CHANGE images images TEXT NOT NULL DEFAULT '';";

		/*
        $result = $db->query("SELECT id, title FROM " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_type WHERE alias=''");
        while (list ($id, $title) = $result->fetch(3)) {
            $_sql[] = "UPDATE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_type SET alias=" . $db->quote(change_alias($title)) . " WHERE id=" . $id;
        }
		*/

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_detail(
          id int(11) unsigned NOT NULL,
          content text NOT NULL,
          note text NOT NULL,
          groupcomment varchar(255) NOT NULL,
          contact_fullname varchar(255) NOT NULL,
          contact_email varchar(100) NOT NULL,
          contact_phone varchar(255) NOT NULL,
          contact_address varchar(255) NOT NULL,
          PRIMARY KEY (id)
        ) ENGINE=MyISAM";

        $result = $db->query("SHOW COLUMNS FROM " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows LIKE 'content'")->fetch();
        if ($result) {
            $result = $db->query("select id, content, note, groupcomment, contact_fullname, contact_email, contact_phone, contact_address from " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows");
            while ($row = $result->fetch()) {
                $_sql[] = "insert into " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_detail values (" . $row['id'] . ", " . $db->quote($row['content']) . ", " . $db->quote($row['note']) . ", " . $db->quote($row['groupcomment']) . ", " . $db->quote($row['contact_fullname']) . ", " . $db->quote($row['contact_email']) . ", " . $db->quote($row['contact_phone']) . ", " . $db->quote($row['contact_address']) . ")";
            }
        }

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows DROP content, DROP note, DROP groupcomment, DROP contact_fullname, DROP contact_email, DROP contact_phone, DROP contact_address;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows CHANGE groups_config groups_config TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_queue_edit CHANGE keywords keywords TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_cat ADD form VARCHAR(250) NOT NULL DEFAULT '' AFTER sort;";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_template (
          id mediumint(8) NOT NULL AUTO_INCREMENT,
          status tinyint(1) NOT NULL DEFAULT '1',
          title VARCHAR(250) NOT NULL DEFAULT '',
          alias VARCHAR(250) NOT NULL DEFAULT '',
          weight mediumint(8) unsigned NOT NULL DEFAULT '1',
          UNIQUE alias (alias),
          PRIMARY KEY (id)
        ) ENGINE=MyISAM ";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_field (
          fid mediumint(8) NOT NULL AUTO_INCREMENT,
          field varchar(25) NOT NULL,
          listtemplate varchar(25) NOT NULL,
          tab varchar(250) NOT NULL DEFAULT '',
          weight int(10) unsigned NOT NULL DEFAULT '1',
          field_type enum('number','date','textbox','textarea','editor','select','radio','checkbox','multiselect') NOT NULL DEFAULT 'textbox',
          field_choices text NOT NULL,
          sql_choices text NOT NULL,
          match_type enum('none','alphanumeric','email','url','regex','callback') NOT NULL DEFAULT 'none',
          match_regex varchar(250) NOT NULL DEFAULT '',
          func_callback varchar(75) NOT NULL DEFAULT '',
          min_length int(11) NOT NULL DEFAULT '0',
          max_length bigint(20) unsigned NOT NULL DEFAULT '0',
          class varchar(25) NOT NULL DEFAULT '',
          language text NOT NULL,
          default_value varchar(250) NOT NULL DEFAULT '',
          PRIMARY KEY (fid),
          UNIQUE KEY field (field)
        ) ENGINE=MyISAM";

        $_sql[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_info (
        	rowid int(11) unsigned NOT NULL,
        	PRIMARY KEY (rowid)
        ) ENGINE=MyISAM";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_detail ADD maps VARCHAR(255) NOT NULL AFTER content;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows ADD address VARCHAR(255) NOT NULL AFTER area_d;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_queue_edit ADD maps VARCHAR(255) NOT NULL AFTER content;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_detail ADD display_maps TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER maps;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_queue_edit ADD display_maps TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER maps;";

        $_sql[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $mod_data . "_rows CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';";

        if (!empty($_sql)) {
            foreach ($_sql as $sql) {
                try {
                    $db->query($sql);
                } catch (PDOException $e) {
                    //
                }
            }
            $nv_Cache->delMod($mod);
        }
    }
}

die('OK');
